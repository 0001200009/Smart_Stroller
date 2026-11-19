# Especificação Técnica Consolidada & Conclusões de Engenharia - Smart Stroller (AGGLY)

**Projeto de Aprendizagem SENAI**  
**Autores & Fundadores:** Alefe Henrique e Yasmim de Souza  
**Data da Última Atualização:** Novembro de 2026  
**Status do Sistema:** Auditado, Estabilizado e Especificado (Pronto para Defesa de Banca e Evolução Comercial)

---

## 1. Visão Geral Executiva e Defesa de Tese (Pitch)

O **Smart Stroller** é uma solução de IoT e software para o varejo supermercadista desenvolvida pela AGGLY, focada em resolver o vazamento invisível de receita causado por filas, quebra operacional e retrabalho no checkout.

### Números e Métricas Chave do Negócio
- **Perda por Abandono de Fila:** 15% das compras no varejo tradicional são abandonadas por excesso de espera (cerca de R$ 300.000,00/mês perdidos em uma loja com faturamento de R$ 2M/mês).
- **Quebra Operacional (Furtos / Extravios):** Prevenção de perdas consome em média 1,3% da receita bruta no varejo alimentício.
- **Redução do Tempo de Atendimento:** O tempo médio de fila e passagem cai de ~7 minutos para menos de 30 segundos por cliente no caixa express.
- **Conversão de Vendas Perdidas:** Até 90% das compras anteriormente desistidas são convertidas em receita efetiva.

---

## 2. Decisões de Arquitetura e Engenharia (Sessão Grill-Me)

Abaixo estão formalizadas as decisões técnicas e operacionais estratégicas consolidadas com os fundadores:

### 2.1 Gestão de Produtos Pesáveis (Hortifrúti, Açougue e Padaria)
- **Solução Adotada:** Balança com impressora térmica RFID nos setores de pesagem.
- **Funcionamento:** O atendente ou cliente pesa o produto na balança convencional do setor (ex: maçãs, carne), que imprime uma etiqueta adesiva contendo um chip RFID UHF dinâmico já codificado com o código do produto, peso e preço calculado.
- **Comportamento no Carrinho:** Ao ser colocado no cesto, o produto é lido instantaneamente como qualquer item industrializado pré-embalado.

### 2.2 Detecção Direcional de Itens ("Entrada" vs "Saída")
- **Solução Adotada:** Sequenciamento temporal e zoneamento geométrico das antenas RFID UHF.
- **Funcionamento:**
  - Antena A (borda superior externa do cesto).
  - Antenas B e C (laterais internas do cesto blindado).
  - **Entrada:** Disparo no padrão Antena A $\rightarrow$ Antenas B/C registra o evento `acao: 'Entrada'`.
  - **Saída:** Disparo no padrão Antenas B/C $\rightarrow$ Antena A registra o evento `acao: 'Saida'`.
- O controller [ArduinoController.php](file:///c:/Users/Aluno_Tarde/Downloads/laragon-portable/www/SmartStroller/app/Http/Controllers/ArduinoController.php) decrementa a quantidade e remove o item quando a quantidade chega a zero, recalculando o subtotal.

### 2.3 Modelo de Checkout: Caixa Express com Código Consolidado
- **Por que não cobrar diretamente no carrinho com maquininha/POS?**
  1. **Redução de CapEx:** Evita o custo astronômico de homologação bancária (PCI-PTS/TEF) e hardware para dezenas de carrinhos, além de poupar bateria.
  2. **Controle de Fraude:** O operador do caixa express realiza uma rápida inspeção visual comparando o volume do cesto com o resumo impresso no cupom.
  3. **Universalidade de Pagamento:** Permite dinheiro em espécie, vale-alimentação, Pix e cartões em um único ponto.
- **Fluxo:** O carrinho emite o cupom Code128 (`STROLLER-{id_compra}`). O operador bipa uma única vez e liquida o pagamento em menos de 20 segundos.

### 2.4 Câmera HD Interna e Visão Computacional Antifraude
- **Solução Adotada:** Validação cruzada volumétrica (IA embarcada + RFID).
- **Funcionamento:** O microcomputador Raspberry Pi roda um modelo leve de visão computacional (ex: OpenCV/YOLO) monitorando a ocupação volumétrica do cesto.
- **Gatilho de Alerta:** Se o volume do cesto aumentar sem que nenhuma tag RFID correspondente tenha sido registrada pelas antenas, o sistema dispara um alerta imediato de divergência para a central de segurança e acende um sinal na tela.

### 2.5 Resiliência de Conectividade (Queda de Wi-Fi / Zonas de Sombra)
- **Solução Adotada:** Arquitetura *Offline-First* com SQLite Local.
- **Funcionamento:** O Raspberry Pi possui uma réplica local leve do banco. Se a conexão Wi-Fi oscilar nas gôndolas metálicas, o carrinho continua registrando produtos, atualizando a tela do cliente sem travar e enfileira as requisições em buffer local.
- **Sincronização:** Assim que o sinal for restabelecido, o script envia o lote de sincronização para a API central em Laravel.

### 2.6 Identificação de Frotas de Carrinhos
- **Solução Adotada:** Parâmetro de Dispositivo no Modo Kiosk do Raspberry Pi.
- **Funcionamento:** Cada carrinho inicia o navegador em tela cheia com sua própria URL de inicialização (ex: `/sistema?stroller_id=X`), de modo que o formulário de boas-vindas já associa automaticamente a compra ao ID correto daquele carrinho sem risco de sobreposição de sessões.

### 2.7 Segurança de Acesso ao Painel Administrativo
- **Solução Adotada:** Autenticação Nativa do Laravel (Guard, Session e Middleware).
- **Funcionamento:** As rotas administrativas (`/admin/*`) serão protegidas por middleware `auth` com senhas criptografadas com `bcrypt`, eliminando validações frágeis de JavaScript no navegador.

### 2.8 Tratamento de Abandono de Carrinho
- **Solução Adotada:** Temporizador de Inatividade Inteligente (Timeout de 15 minutos).
- **Funcionamento:** Se o carrinho permanecer 15 minutos parado (verificado via ausência de leituras e telemetria de acelerômetro), o sistema emite um aviso sonoro/visual, altera o status da compra para `'Cancelada'` no banco e envia uma notificação para o painel de reposição alertando os funcionários para recolher os itens.

---

## 3. Mapeamento Atual de Rotas e Endpoints

### 3.1 Endpoints de API REST (`routes/api.php`)
- `POST /api/rfid/read`: Endpoint de telemetria de tags.
  - Body: `{"codigo_rfid": "string", "id_stroller": int, "acao": "Entrada|Saida"}`
  - Resposta: `{"status": "sucesso", "produto": "Nome", "preco": "0.00", "total": 0.00}`
- `GET /api/carrinho/{id_compra}`: Endpoint consumido a cada 1,5s pelo JavaScript da tela.
  - Resposta: `{"itens": [{"nome": "...", "quantidade": int, "preco_unitario": "0,00", "subtotal": "0,00"}], "valor_total": "0,00"}`
- `GET /api/carrinho/dados/{id_compra}`: Retorna dados brutos das instâncias Eloquent.

### 3.2 Rotas Web (`routes/web.php`)
- `GET /`: Boas-vindas ao cliente / Acesso administrativo.
- `GET /sistema`: Totem de autoatendimento com suporte a Login (Nome + CPF) ou Novo Cadastro (Nome, CPF, E-mail e Telefone opcionais).
- `POST /compra/iniciar`: Inicia sessão com suporte duplo: 'login' (Nome + CPF) ou 'cadastro' (Nome + CPF obrigatórios, E-mail e Telefone opcionais), ativando o carrinho e gerando a compra.
- `GET /layout`: Tela do carrinho em tempo real no tablet.
- `GET /recibo/imprimir/{id}`: Conclusão da compra e emissão do recibo com código Code128.
- `GET /admin`: Dashboard com métricas ativas de itens, receita e bateria.
- `GET /admin/produtos`: Gestão de produtos e tags RFID.
- `GET /admin/clientes`: Gestão da base de usuários cadastrados.
- `GET /admin/compras`: Auditoria histórica de transações.
- `GET /admin/carrinhos`: Status de telemetria da frota.
- `GET /admin/alertas`: Central de notificações e quebras operacionais.

---

## 4. Histórico de Bugs Auditados e Corrigidos

| Componente | Problema Identificado | Correção Implementada |
| :--- | :--- | :--- |
| **Model LeiturasRfid** | Erro 500: Classe não encontrada ao bipar tags | Criado `LeiturasRfid.php` com propriedades `$fillable` e timestamps. `Leituras.php` estendido como alias. |
| **Namespaces PSR-4** | `namespace app\Models` em minúsculo | Padronizado para `namespace App\Models` em todos os models. |
| **Sincronização JS** | Erro 404 contínuo no polling do carrinho | Rota `/api/carrinho/{id_compra}` registrada em `api.php` ligada ao `ArduinoController@buscarCarrinho`. |
| **Clique na Tela Inicial** | Interceptador global no `document` bloqueava o botão de login | Filtro `if (e.target.closest('.btn-admin')) return;` implementado em `bemVindo.blade.php`. |
| **Campos em Compras Admin** | Inputs `id` e `valor` salvavam dados vazios | Renomeados para `id_usuario` e `valor_total` em `admin/compras.blade.php`. |
| **Dashboard Estático** | Dados de carrinho e bateria eram textos fixos | Integradas variáveis Blade dinâmicas (`$itensNoCarrinho`, `$valorTotal`, `$nivelBateria`). |
| **Tabela de Produtos** | Tags `<th>` indevidas dentro do `<thead>` para o loop de produtos | Corrigido para `<tbody>` e células `<td>` semânticas com diálogo de confirmação. |
| **Model Pagamentos** | Nomes de colunas divergentes (`metodo_pagamento` e `valor_pago`) | Alinhado com o SQL para `metodo` e `valor`. |
| **Display LCD ESP32** | Acentos UTF-8 corrompiam no visor 16x2 | Mensagens da API ajustadas para ASCII puro (`'Produto nao encontrado'`, `'Sem compra ativa'`). |
| **Seeds de Produtos SQL** | Tabela `produtos` estava vazia no script | Adicionado lote de 20 produtos com tags RFID reais no arquivo SQL. |

---

## 5. Próximos Passos de Implementação (Roadmap Técnico)

1. **Sprint 1 (Segurança):** Migrar a rota `/login` para autenticação padrão do Laravel (`Auth::attempt`), criando migration para a tabela `users` com `role: 'admin'`.
2. **Sprint 2 (Multi-dispositivo):** Parametrizar a rota `/compra/iniciar` para receber dinamicamente o `id_stroller` via URL/query string em vez do valor fixo 1.
3. **Sprint 3 (Sugestão de Promoções):** Adicionar um card lateral em `layout.blade.php` exibindo produtos com `status = 'em promocao'` para incentivar aumento de ticket médio.
4. **Sprint 4 (Otimização de Polling):** Implementar Server-Sent Events (SSE) ou Laravel Reverb no lugar do `setInterval(..., 1500)` para reduzir consumo de CPU no Raspberry Pi.

---

## 6. Diretrizes de Interface e Qualidade de Código

1. **Padrão Estrito de Interface (Sem Emojis/Emotes):** É terminantemente proibido o uso de emojis ou emotes em templates Blade, scripts ou estilos. Qualquer elemento visual deve utilizar ícones vetoriais SVG profissionais e padronizados (inline ou bibliotecas vetoriais).
2. **Identificadores Limpos:** Manter rotas, controllers e retornos de API limpos, previsíveis e padronizados.
