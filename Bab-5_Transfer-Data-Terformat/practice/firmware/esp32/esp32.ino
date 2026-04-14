#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// --- Konfigurasi ---
const char* ssid = "";
const char* password = "";
const char* serverUrl = "http://[IP_ADDRESS]/Sensormulti-json.php"; // Ganti dengan IP-mu!

int pinLED = 2; // LED indikator (bisa D2 atau onboard)

void setup() {
  Serial.begin(115200);
  pinMode(pinLED, OUTPUT);

  Serial.println("\nConnecting to WiFi...");
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Connected! IP: " + WiFi.localIP().toString());
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    Serial.println("[HTTP] Requesting data...");

    http.begin(serverUrl);
    int httpCode = http.GET();

    if (httpCode > 0) {
      Serial.printf("[HTTP] GET code: %d\n", httpCode);

      if (httpCode == HTTP_CODE_OK) {
        String payload = http.getString();
        Serial.println("Raw JSON: " + payload);

        // --- Parsing JSON ---
        // Kita alokasikan memori secukupnya (200 bytes cukup untuk contoh ini)
        StaticJsonDocument<200> doc;
        DeserializationError error = deserializeJson(doc, payload);

        if (error) {
          Serial.print("deserializeJson() failed: ");
          Serial.println(error.c_str());
          return;
        }

        // --- Ekstrak Nilai ---
        const char* idSensor = doc["idSensor"];
        const char* namaSensor = doc["namaSensor"];
        const char* statusSensor = doc["statusSensor"];
        const char* add_0 = doc["add"]["0"];
        const char* add_1 = doc["add"]["1"];

        // --- Tampilkan Hasil ---
        Serial.println("\n--- Ekstrak JSON Berhasil ---");
        Serial.println("Nama Sensor  : " + String(namaSensor));
        Serial.println("ID Sensor    : " + String(idSensor));
        Serial.println("Status Sensor: " + String(statusSensor));
        Serial.println("Suhu (add_0) : " + String(add_0));
        Serial.println("Lumen (add_1): " + String(add_1));
        Serial.println("---------------------------\n");

        // Contoh kontrol sederhana: Nyalakan LED jika statusSensor "1"
        if (String(statusSensor) == "1") {
            digitalWrite(pinLED, HIGH);
        } else {
            digitalWrite(pinLED, LOW);
        }
      }
    } else {
      Serial.printf("[HTTP] GET failed, error: %s\n", http.errorToString(httpCode).c_str());
    }
    http.end();
  } else {
    Serial.println("WiFi Disconnected!");
  }

  delay(5000); // Minta data setiap 5 detik
}