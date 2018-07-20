#include <SoftwareSerial.h>

const byte rxPin = 2; 
const byte txPin = 3; 
String ssid = "toy";
String password = "12345678";
SoftwareSerial esp8266 (rxPin, txPin);
String path = "/toy/check/13";
String server = "sistema.dionellybrinquedos.com.br";
String getRequest = "GET " + path + " HTTP/1.0\r\n" + "Host: " + server + "\r\n\r\n";
String getRequestLength = String(getRequest.length());
String response="";
String c = "x";
void setup() {
  pinMode(LED_BUILTIN, OUTPUT);
  Serial.begin(9600);
  esp8266.begin(9600);
  delay(1000);
  reset();
  //setMode("1");
  connectWifi();
  }

void loop() {
  httpGet(); 
  delay(10000);
}

///////////////WI FI AREA/////////////////////////////
void httpGet(){
    espSend("AT+CIPSTART=\"TCP\",\"" + server + "\",80");
    esp8266.println("AT+CIPSEND=" + getRequestLength);
    if(esp8266.find(">")) {
      Serial.print("");
      esp8266.print(getRequest);
      }
    if(esp8266.find("SEND OK")) Serial.print("");
    delay(1000);
  
    espRead();
    
    Serial.print("");
    if(c.indexOf("brinquedo desligar") != -1) {
      Serial.println("desligar");
      digitalWrite(LED_BUILTIN, LOW);
    }
    if(c.indexOf("brinquedo ligar") != -1) {
      Serial.println("ligar");
      digitalWrite(LED_BUILTIN, HIGH);
    }
    Serial.println(millis());
    c = "x";
}

void espSend(String cmd) {
  espClear();
  esp8266.println(cmd);
  delay(1000);
  while(esp8266.available()) {
    esp8266.readString();
    Serial.println("");
  }
}

void espRead() {
  while(esp8266.available()) {
    c = esp8266.readString();
  }
}

void espClear() {
  while(esp8266.available()) {
    esp8266.read();
    }
}

void reset() {
  Serial.println("Resetting WiFi");
  esp8266.println("AT+RST");
  delay(1000);
  if(esp8266.find("OK")) Serial.println("Reset!");
}

void connectWifi() {
  espClear();
  Serial.println("Connecting...");
  String CMD = "AT+CWJAP=\"" +ssid+"\",\"" + password + "\"";
  espSend(CMD);
  delay(10000);
  Serial.println(esp8266.readString());
}

void setMode(String mode) {
    Serial.println("Setting Mode = " + mode);
    esp8266.println("AT+CWMODE=" + mode);
    delay(1000);
    espRead();
}
