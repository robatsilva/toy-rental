#include <SoftwareSerial.h>
#include <LiquidCrystal_I2C.h>

// Inicializa o display no endereco 0x27
LiquidCrystal_I2C lcd(0x27,2,1,0,4,5,6,7,3, POSITIVE);

//////////////////////// Variáveis do wifi
const byte rxPin = 2; 
const byte txPin = 3; 
String ssid = "bloco doce";
String password = "kira2017";
SoftwareSerial esp8266 (rxPin, txPin);
String path = "/toy/check/13";
String server = "sistema.dionellybrinquedos.com.br";
String getRequest = "GET " + path + " HTTP/1.1\r\n" + "Host: " + server + "\r\nConnection: keep-alive\r\n\r\n";
String getRequestLength = String(getRequest.length());
String response="";
/////////////////////////

void setup() {
  setLCD();
  pinMode(LED_BUILTIN, OUTPUT); // placa
  pinMode(12, OUTPUT); // motor

  pinMode(4, INPUT); // cartão
  pinMode(5, INPUT); // sensor

  Serial.begin(9600);
  esp8266.begin(9600);
  delay(5000);
  Serial.flush();
  reset();
  //setMode("1");
  connectWifi();
  }

void loop() {
  // SE NÃO ESTÁ LIGADO MANUALMENTE, VERIFICA EM SISTEMA
  if(digitalRead(4) == HIGH){
    httpGet();
  }
  lerInputs();
  delay(100);
}

void setLCD(){
  lcd.begin (16,2);
  lcd.setBacklight(HIGH);
}

void escreverLCD(String mensagemAcima, String mensagemAbaixo){
  lcd.setCursor(0,0);
  lcd.print("                 ");
  lcd.setCursor(0,0);
  lcd.print(mensagemAcima);
  lcd.setCursor(0,1);
  lcd.print("                 ");
  lcd.setCursor(0,1);
  lcd.print(mensagemAbaixo);
}

String nivelBateria(){
  int valor = analogRead(0); // Ler valor analógico
  double volt = (valor/1023.0) * 5.0; // Apenas verdade se vcc for igual a 5V
  // Imprimindo o nível de carga da bateria
  return "Bateria: " + String(volt); 
}

///////////////WI FI AREA/////////////////////////////
void httpGet(){
    response = "";
    if(!esp8266.available()){
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
            } else if(response.indexOf("brinquedo ligar") != -1) {
              escreverLCD("Alugado", "Retorno " + response.substring(36,41));
              digitalWrite(LED_BUILTIN, HIGH);
            } else {
              Serial.println(response);  
            }
            Serial.println(millis());
            Serial.println(response);
            
//            esp8266.println("AT+CIPCLOSE");
//            if(esp8266.find("OK")) Serial.println("Connection close"); 
            
          } else Serial.println("sent fail - " + response);
        } else Serial.println("sending fail - " + response);
      } else Serial.println("Conection fail - " + response);
    } else espClear();
}

void espRead() {
  response = "";

  boolean ctrl = false;
  
  while(!esp8266.available()){}

  long int time = millis();
  
  while ( (time + 2000) > millis())
  {
    while (esp8266.available())
    {
      // The esp has data so display its output to the serial window
      char c = esp8266.read(); // read the next character.
      if(c == '{') ctrl = true;

      if(ctrl) response += c;

      if(c == '}') ctrl = false;
    }
  }
}

void espClear() {
  while (esp8266.available())
  {
    esp8266.read(); // read the next character.;
  }
}

void reset() {
  escreverLCD("Iniciando...", "");
  delay(1000);
  esp8266.println("AT+RST");
  if(esp8266.find("OK")) escreverLCD("Iniciando", "Passo 2");
  delay(1000);
}

void connectWifi() {
  espClear();
  escreverLCD("Conectando...", "");
  String CMD = "AT+CWJAP=\"" +ssid+"\",\"" + password + "\"";
  esp8266.println(CMD);
  delay(5000);
  if(esp8266.find("OK")) escreverLCD("Conectado!!!!", "");
  else connectWifi();
}
/////////////////////////////////////////////////////////////
////////////////////////////////Inputs Area /////////////////
void lerInputs(){
  lerCartao();
  lerSensor();
}

// pino 4
void lerCartao(){
  if(digitalRead(4) == LOW){
    digitalWrite(13, HIGH);
    escreverLCD("Ligado", "xx vez");
  } else {
    digitalWrite(13, LOW);
    escreverLCD("Desligado", "");
  }
}

// pino 5
void lerSensor(){
  if(digitalRead(13) == HIGH){
    if(digitalRead(5) == LOW){
      digitalWrite(12, HIGH);
      escreverLCD("Parado", "Obstaculo");
    } else {
      digitalWrite(12, LOW);
      escreverLCD("Liberado", "");
    }
  }
}




