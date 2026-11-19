import serial
import time
import mysql.connector

# --- Configurações da Serial ---
PORTA = 'COM14'
BAUD_RATE = 115200 

# --- Configurações do seu MySQL ---
MYSQL_HOST = "localhost"
MYSQL_USER = "root"
MYSQL_PASSWORD = ""  
MYSQL_DATABASE = "smart_stroller" 

def buscar_produto_no_banco(tag_id):
    try:
        conexao_db = mysql.connector.connect(
            host="localhost",
            user="root",
            password= "",
            database="smart_stroller"
        )
        cursor = conexao_db.cursor()
        
        comando_sql = "SELECT nome, preco FROM produtos WHERE codigo_rfid = %s"
        cursor.execute(comando_sql, (tag_id,))
        
        resultado = cursor.fetchone()
        
        cursor.close()
        conexao_db.close()
        return resultado  

    except mysql.connector.Error as erro:
        print(f"❌ Erro ao conectar ou buscar no MySQL: {erro}")
        return None

# ==============================================================================
# FLUXO PRINCIPAL
# ==============================================================================
try:
    conexao_serial = serial.Serial(PORTA, BAUD_RATE, timeout=1)
    time.sleep(2) 
    
    print(f"✅ Conectado na porta {PORTA}!")
    print("⏳ Aguardando leitura de tags no MySQL... (Pressione Ctrl+C para fechar)\n")

    while True:
        if conexao_serial.in_waiting > 0:
            linha_bruta = conexao_serial.readline().decode('utf-8', errors='ignore').strip()
            
            if "ID da Tag detectada:" in linha_bruta:
                tag_com_espacos = linha_bruta.split("ID da Tag detectada:")[1]
                tag_limpa = tag_com_espacos.replace(" ", "").strip().upper()
                
                print(f"🔍 Tag identificada na Serial: {tag_limpa}")
                produto = buscar_produto_no_banco(tag_limpa)
                
                print("-" * 40)
                if produto:
                    nome_produto, preco_produto = produto
                    print(f"📦 PRODUTO ENCONTRADO NO MYSQL!")
                    print(f"   Nome:  {nome_produto}")
                    print(f"   Preço: R$ {preco_produto}")
                    
                    # --- ENVIA OS DADOS DE VOLTA PARA O ESP32 ---
                    # Formato: Nome|Preco (ex: "Arroz Sao Joao|25.00")
                    mensagem_arduino = f"{nome_produto}|{preco_produto}\n"
                    conexao_serial.write(mensagem_arduino.encode('utf-8'))
                    
                else:
                    print(f"❌ PRODUTO NÃO ENCONTRADO NO BANCO MYSQL.")
                    
                    # --- ENVIA MENSAGEM DE ERRO PARA O ESP32 ---
                    mensagem_arduino = "Produto Nao|Cadastrado!\n"
                    conexao_serial.write(mensagem_arduino.encode('utf-8'))
                    
                print("-" * 40 + "\n")

except serial.SerialException as e:
    print(f"❌ Erro de comunicação serial: {e}")
    print("Certifique-se de fechar o Monitor Serial da IDE do Arduino.")

except KeyboardInterrupt:
    print("\n🛑 Programa encerrado.")

finally:
    if 'conexao_serial' in locals() and conexao_serial.is_open:
        conexao_serial.close()
        print("🔌 Conexão serial fechada.")