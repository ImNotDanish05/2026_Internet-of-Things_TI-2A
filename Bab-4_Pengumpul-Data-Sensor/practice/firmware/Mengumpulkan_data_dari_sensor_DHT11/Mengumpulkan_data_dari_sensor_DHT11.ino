#include <WiFi.h>
#include <HTTPClient.h>
#include "DHT.h"

#define DHTPIN 2
#define DHTTYPE DHT11

DHT dht(DHTPIN, DHTTYPE);
const char* ssid = "NAMA_WIFI_KAMU";
const char* password = "PASSWORD_WIFI_KAMU";
// Sesuaikan IP Laptop/Server kamu
const char* serverName = "http://192.168.xx.xx/eslolin/sensordata.php"; 

void setup() {
  Serial.begin(115200);
  dht.begin();
  
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Connected!");
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    float h = dht.readHumidity();
    float t = dht.readTemperature();

    if (isnan(h) || isnan(t)) {
      Serial.println("Gagal baca sensor!");
      return;
    }

    // Buat URL lengkap dengan data GET
    String url = String(serverName) + "?humidity=" + String(h) + "&suhu=" + String(t);
    
    http.begin(url);
    int httpCode = http.GET();

    if (httpCode > 0) {
      Serial.printf("[HTTP] Code: %d\n", httpCode);
      if (httpCode == HTTP_CODE_OK) {
        Serial.println("Data Terkirim!");
      }
    } else {
      Serial.printf("[HTTP] Gagal, error: %s\n", http.errorToString(httpCode).c_str());
    }
    http.end();
  }
  delay(5000); // Kirim tiap 5 detik
}