#include <MFRC522.h>
#include <MFRC522Extended.h>
#include <deprecated.h>
#include <require_cpp11.h>

#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <ArduinoJson.h> // Biblioteca para ler as respostas da API

// ================= CONFIGURAÇÕES =================
const char* ssid = "iPhone de Alefe";
const char* password = "Alefe12345";
String urlApi = "http://10.10.0.58:8000/api/rfid/read"; // Troque pelo IP do seu PC

#define SS_PIN 5   
#define RST_PIN 33 
#define BUZZER_PIN 32
#define BOTAO_SAIDA 4 // Opcional: Pino do botão para remover itens (Segure ligado ao GND)
// =================================================

MFRC522 mfrc522(SS_PIN, RST_PIN); 
LiquidCrystal_I2C lcd(0x27, 16, 2); 

void mostrarMensagemEspera() {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("  Passe aqui o  ");
  lcd.setCursor(0, 1);
  lcd.print("  seu produto!  ");
}

void setup() {
  Serial.begin(115200); 
  SPI.begin();          
  mfrc522.PCD_Init();   
  
  lcd.init();           
  lcd.backlight();      
  
  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);
  pinMode(BOTAO_SAIDA, INPUT_PULLUP);

  // Exibe tela de conexão Wi-Fi
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Conectando...");
  lcd.setCursor(0, 1);
  lcd.print("Rede Wi-Fi");

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  // Aviso de sucesso
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Wi-Fi Conectado!");
  Serial.println("\nIP do ESP32: " + WiFi.localIP().toString());
  delay(2000);
  
  mostrarMensagemEspera();
}

void loop() {
  // Verifica se o Wi-Fi caiu
  if (WiFi.status() != WL_CONNECTED) {
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Sem Sinal Wi-Fi");
    WiFi.reconnect();
    delay(2000);
    return;
  }

  if (!mfrc522.PICC_IsNewCardPresent()) return;
  if (!mfrc522.PICC_ReadCardSerial()) return;

  // Feedback sonoro da leitura
  digitalWrite(BUZZER_PIN, HIGH);
  delay(100);
  digitalWrite(BUZZER_PIN, LOW);

  // Lê a tag e converte para texto limpo (ex: A3F910B2)
  String tagID = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    if (mfrc522.uid.uidByte[i] < 0x10) tagID += "0"; 
    tagID += String(mfrc522.uid.uidByte[i], HEX);
  }
  tagID.toUpperCase(); 

  // Define se a ação é adicionar (Entrada) ou remover (Saida)
  String acao = (digitalRead(BOTAO_SAIDA) == LOW) ? "Saida" : "Entrada";

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Buscando no");
  lcd.setCursor(0, 1);
  lcd.print("sistema...");

  Serial.print("Enviando Tag: ");
  Serial.println(tagID);

  // =========================================================
  // FAZ A COMUNICAÇÃO COM O LARAVEL VIA WI-FI
  // =========================================================
  HTTPClient http;
  http.begin(urlApi);
  http.addHeader("Content-Type", "application/json");

  // Monta o JSON que a sua API espera receber
  String jsonPayload = "{\"codigo_rfid\":\"" + tagID + "\", \"id_stroller\":1, \"acao\":\"" + acao + "\"}";
  
  // Executa o envio
  int httpResponseCode = http.POST(jsonPayload);

  lcd.clear();
  
  if (httpResponseCode > 0) {
    String resposta = http.getString();
    Serial.println("Resposta API: " + resposta);
    
    // Decodifica a resposta JSON do Laravel
    DynamicJsonDocument doc(1024);
    DeserializationError error = deserializeJson(doc, resposta);

    if (!error) {
      if (doc.containsKey("erro")) {
        // Se a API retornou um erro (ex: Produto não cadastrado)
        String msgErro = doc["erro"].as<String>();
        lcd.setCursor(0, 0);
        lcd.print("Atencao:");
        lcd.setCursor(0, 1);
        lcd.print(msgErro.substring(0, 16)); // Limita a 16 letras p/ caber no LCD
      } else {
        // Sucesso! Mostra nome e preço do produto
        String nome = doc["produto"].as<String>();
        String preco = doc["preco"].as<String>();
        
        lcd.setCursor(0, 0);
        lcd.print(nome.substring(0, 16)); 
        
        lcd.setCursor(0, 1);
        if (acao == "Saida") {
          lcd.print("- R$ " + preco);
        } else {
          lcd.print("+ R$ " + preco);
        }
      }
    } else {
      lcd.print("Erro de Leitura");
    }
  } else {
    // Servidor offline ou IP errado
    lcd.setCursor(0, 0);
    lcd.print("Falha Servidor");
    lcd.setCursor(0, 1);
    lcd.print("Erro: " + String(httpResponseCode));
  }

  http.end();

  // Finaliza a leitura da tag atual e aguarda antes de ler a próxima
  mfrc522.PICC_HaltA();
  delay(2500);
  mostrarMensagemEspera();
}