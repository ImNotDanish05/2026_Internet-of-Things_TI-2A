#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

const char* ssid = "NAMA_WIFI_KAMU";
const char* password = "PASSWORD_WIFI";

// GANTI 192.168.x.x DENGAN IP LAPTOPMU DAN TAMBAHKAN PORT 8080
const char* serverName = "http://192.168.x.x:8080/api.php"; 

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500); Serial.print(".");
  }
  Serial.println("\nWiFi Terhubung!");
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    
    // --- 1. PROSES BACA DATA JSON DARI DATABASE ---
    http.begin(serverName);
    int httpResponseCode = http.GET();
    
    if (httpResponseCode > 0) {
      String payload = http.getString();
      Serial.println("Data Mentah JSON: " + payload);
      
      // Mengurai (Parsing) JSON yang masuk
      DynamicJsonDocument doc(1024);
      deserializeJson(doc, payload);
      
      int statusSensor = doc["statussensor"]; // Mengambil nilai statussensor
      Serial.print("Status Sensor di Database: ");
      Serial.println(statusSensor);
      
      // Jika status 1, maka kirim data
      if (statusSensor == 1) {
          kirimDataSensor();
      }
    } else {
      Serial.print("Error GET. HTTP Code: ");
      Serial.println(httpResponseCode);
    }
    http.end();
  }
  delay(5000); // Ulangi setiap 5 detik
}

void kirimDataSensor() {
    HTTPClient http;
    http.begin(serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    
    // Simulasi nilai sensor
    float nilaiSensor = 28.5; 
    String httpRequestData = "idSensor=1&nilaiData=" + String(nilaiSensor);
    
    int httpResponseCode = http.POST(httpRequestData);
    Serial.print("Kode Response Kirim Data: ");
    Serial.println(httpResponseCode);
    
    http.end();
}