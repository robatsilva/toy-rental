#include <SoftwareSerial.h>
#include <LiquidCrystal_I2C.h>
//Carrega a biblioteca
#include <EEPROM.h>

// Inicializa o display no endereco 0x27
LiquidCrystal_I2C lcd(0x27,2,1,0,4,5,6,7,3, POSITIVE);

//////////////////////// Variáveis do wifi
String ssid = "BLOCO DOCE";
String password = "11111111";
SoftwareSerial esp8266 (2, 3); //WIFI
String path = "/toy/check/13";
String server = "sistema.dionellybrinquedos.com.br";
String getRequest = "GET " + path + " HTTP/1.1\r\n" + "Host: " + server + "\r\nConnection: keep-alive\r\n\r\n";
String getRequestLength = String(getRequest.length());
String response="";
/////////////////////////
///////////////////////// Variáveis controle
boolean debugger = false;
boolean sensorAtivo = false;
boolean wifiAtivo = false;
boolean cartaoAtivo = false;
boolean criaNovoLog = true;
boolean conectado = false;

unsigned long tempoUso = 0;
unsigned long tempoLog = 0;
String ultimaMensagem = "";

///////////////////////// Variaveis EEPROM
int endId = 0;
int endQtd = 2;
int endTempo = 4;

///////////////////////// Variáveis I/O
short iSensor = 5;
short iWifi = 6;

short oMotor = 12;

/////////////////////////

void setup() {
  setLCD();
  pinMode(LED_BUILTIN, OUTPUT); // placa
  pinMode(oMotor, OUTPUT); // motor
  pinMode(7, OUTPUT); // 5 VOLTS PARA TESTE
  digitalWrite(7, HIGH);
  
  pinMode(4, INPUT); // cartão / Interruptor da placa
  pinMode(iSensor, INPUT); // sensor
  pinMode(iWifi, INPUT); // HABILITA WIFI

  Serial.begin(9600);
  esp8266.begin(9600);
  delay(5000);
  Serial.flush();
  reset();
  connectWifi();
//  gravaInteiro(endId, 0);
//    gravaInteiro(endQtd, 0);
//    gravaInteiro(endTempo, 0);  
  Serial.println(leInteiro(endTempo));
  delay(5000);
  }

void loop() {
  // SE WIFI HABILITADO, BUSCA NO SISTEMA
  if(digitalRead(iWifi) == HIGH){
    if(!wifiAtivo){
      wifiAtivo = true;
      int qtd = leInteiro(endQtd);
      if(qtd > 0) criaNovoLog = true;
      String queryString = "?id=" + String(leInteiro(endId)) + "&qtd=" + String(leInteiro(endQtd)) + "&tempo=" + String(leInteiro(endTempo));
      path = "/toy/check/13" + queryString;
      getRequest = "GET " + path + " HTTP/1.1\r\n" + "Host: " + server + "\r\nConnection: keep-alive\r\n\r\n";
      getRequestLength = String(getRequest.length());
    }
    httpGet();
    myDelay(4000);
  } else {
    wifiAtivo = false;
    lerInputs();
  }
}

///////////////////////////LCD////////////////////////////////////////

void setLCD(){
  lcd.begin (16,2);
  lcd.setBacklight(HIGH);
}

void escreverLCD(String mensagemAcima, String mensagemAbaixo){
  db(mensagemAcima);
  db(mensagemAbaixo);
  if(ultimaMensagem == mensagemAcima + mensagemAbaixo) return;
  lcd.setCursor(0,0);
  lcd.print("                 ");
  lcd.setCursor(0,0);
  lcd.print(mensagemAcima);
  lcd.setCursor(0,1);
  lcd.print("                 ");
  lcd.setCursor(0,1);
  lcd.print(mensagemAbaixo);
  ultimaMensagem = mensagemAcima + mensagemAbaixo;
}

String nivelBateria(){
  int valor = analogRead(0); // Ler valor analógico
  double volt = (valor/1023.0) * 5.0; // Apenas verdade se vcc for igual a 5V
  // Imprimindo o nível de carga da bateria
  return "Bateria: " + String(volt); 
}

///////////////WI FI AREA/////////////////////////////
void httpGet(){
    if(!conectado){
      connectWifi();
    }
    response = "";
    esp8266.println("AT+CIPSTART=\"TCP\",\"" + server + "\",80");
    if(esp8266.find("OK") || esp8266.find("ALREADY CONNECTED")) {
      esp8266.println("AT+CIPSEND=" + getRequestLength);
      if(esp8266.find(">") || esp8266.find("ALREADY CONNECTED")) {
        esp8266.print(getRequest);
        if(esp8266.find("SEND OK")){
          espRead();
          if(response.indexOf("brinquedo desligar") != -1) {
            digitalWrite(LED_BUILTIN, LOW);
            escreverLCD("Disponivel", nivelBateria());
            criaNovoLogFunction();
          } else if(response.indexOf("brinquedo ligar") != -1) {
            escreverLCD("Alugado", "Retorno " + response.substring(36,41));
            digitalWrite(LED_BUILTIN, HIGH);
            criaNovoLogFunction();
          }
          
//            esp8266.println("AT+CIPCLOSE");
//            if(esp8266.find("OK")) Serial.println("Connection close"); 
          return;
          
        } 
      } 
    }
    if(digitalRead(LED_BUILTIN) == LOW) escreverLCD("Falha conexao", "Tentando de Novo"); 
}

void espRead() {
  response = "";

  boolean ctrl = false;
  db("vai aguardar estar available");
  while(!esp8266.available()){ lerSensor(); }
  db("available");
  long int time = millis();
  
  while ( (time + 2000) > millis())
  {
    lerSensor();
    while (esp8266.available())
    {
      // The esp has data
      char c = esp8266.read(); // read the next character.
      
      if(c == '{') ctrl = true;

      if(ctrl) response += c;

      if(c == '}') ctrl = false;
    }
  }
  db(response);
}

void espClear() {
  while (esp8266.available())
  {
    esp8266.read(); // read the next character.;
  }
}

void reset() {
  db("iniciando reset");
  escreverLCD("Iniciando...", "");
  delay(1000);
  db("enviou para wifi at + rst");
  esp8266.println("AT+RST");
  if(esp8266.find("OK")) { 
    espRead();
    escreverLCD("Iniciando", "Passo 2");
  } else {
    db("não encontrou ok no reset");
    espRead();  
  }
  delay(1000);
}

void connectWifi() {
  if(digitalRead(iWifi) == LOW) return;
  db("conectando no wifi");
  espClear();
  escreverLCD("Conectando...", "");
  String CMD = "AT+CWJAP=\"" +ssid+"\",\"" + password + "\"";
  db(CMD);
  esp8266.println(CMD);
  delay(5000);
  if(esp8266.find("OK")) {
    db("CONECTADO");
    escreverLCD("Conectado!!!!", "");
    conectado = true;
  }
  else {
    db("não conectado, tentando de novo");
    espRead();
    connectWifi();
    return;
  }
  delay(2000);
}
/////////////////////////////////////////////////////////////
////////////////////////////////Inputs Area /////////////////
void lerInputs(){
  lerCartao();
  if(digitalRead(4) == HIGH) lerSensor();
}

// pino 4
void lerCartao(){
  if(digitalRead(4) == HIGH){
    if(!cartaoAtivo){
      cartaoAtivo = true;
      digitalWrite(LED_BUILTIN, HIGH);
      tempoUso = millis();  
      tempoLog = tempoUso;
      
      int qtd = leInteiro(endQtd);
      qtd++;
      gravaInteiro(endQtd, qtd);
      escreverLCD("Ligacao manual", String(qtd) + " vez(es)");  
      delay(2000);
    }
    salvaTempoUso(false);
    
  } else {
    if(cartaoAtivo){
      cartaoAtivo = false;  
      salvaTempoUso(true);
    }
    
    digitalWrite(LED_BUILTIN, LOW);
    escreverLCD("Desligado", nivelBateria());
  }
}

// pino 5
void lerSensor(){
  if(digitalRead(LED_BUILTIN) == HIGH){
    if(digitalRead(iSensor) == HIGH){
      sensorAtivo = true;
      digitalWrite(oMotor, HIGH);
      escreverLCD("Parado", "Obstaculo");
      while(digitalRead(iSensor) == HIGH){}
    } else {
      if(sensorAtivo){
        escreverLCD("Liberado", "");
        digitalWrite(oMotor, LOW);
        delay(2000);
        sensorAtivo = false;
      }
    }
  }
}

/////////////////////////////////////////////////////////////////
/////////////////////////////////////EEPROM
void salvaTempoUso(boolean salvarAgora){
  int tempo = (millis() - tempoLog) / 1000 / 60;
  int tempoLigado = (millis() - tempoUso) / 1000 / 60;
  escreverLCD("Tempo de uso:", String(tempoLigado) + " min");
  if((tempo >= 5) || salvarAgora){
    int ultimoTempo = leInteiro(endTempo);
    gravaInteiro(endTempo, tempo + ultimoTempo);  
    tempoLog = millis();
  }
}

int leInteiro(int p){
  int parte1 = EEPROM.read(p);
  int parte2 = EEPROM.read(p+1);
  int valor_original = (parte1 * 256) + parte2;
  return valor_original;
}

void gravaInteiro(int p, int numero){
   // Grava a primeira parte do numero em endereco1
  EEPROM.write(p, numero/256);
  
  // Grava a segunda parte do numero em endereco2
  EEPROM.write(p + 1, numero%256);
}

void criaNovoLogFunction(){
  if(criaNovoLog){
    criaNovoLog = false;
    
    int id = leInteiro(endId);
    gravaInteiro(endId, ++id);
    
    gravaInteiro(endQtd, 0);
    gravaInteiro(endTempo, 0);  
  }
}
/////////////////////////////////////AUXILIARES /////////////////

void myDelay(int _delay){
  unsigned long tempo = millis();
  while ( (tempo + _delay) > millis()){
    lerSensor();
  }
}

void db(String texto){
  if(debugger){
    Serial.println(texto);
  }
}
