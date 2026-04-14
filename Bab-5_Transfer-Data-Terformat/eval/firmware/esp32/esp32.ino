#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// --- Konfigurasi ---
const char* ssid = "";
const char* password = "";

// Ganti IP dengan IP laptop kamu (Port 8000 karena kamu pakai port itu)
const char* serverUrl = "http://10.120.231.214:8000/api.php"; 

int pinLED = 2; // LED Onboard ESP32 (sebagai simulasi Lampu)

void setup() {
  Serial.begin(115200);
  pinMode(pinLED, OUTPUT);
  digitalWrite(pinLED, LOW); // Matikan LED di awal

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
    Serial.println("\n[HTTP] Mengecek instruksi Smart Home...");
    
    http.begin(serverUrl);
    int httpCode = http.GET();

    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK) {
        String payload = http.getString();
        
        // --- Proses Parsing JSON (Nested) ---
        // Karena JSON-nya lebih besar, kita alokasikan memori 384 byte
        StaticJsonDocument<384> doc;
        DeserializationError error = deserializeJson(doc, payload);

        if (error) {
          Serial.print("Gagal parsing JSON: ");
          Serial.println(error.c_str());
          return;
        }

        // --- Ekstrak Nilai dari Blok "Lampu" ---
        const char* namaLampu = doc["Lampu"]["namaSensor"];
        int statusLampu = doc["Lampu"]["statusSensor"]; // Otomatis jadi integer

        // --- Ekstrak Nilai dari Blok "Pompa" ---
        const char* namaPompa = doc["Pompa"]["namaSensor"];
        int statusPompa = doc["Pompa"]["statusSensor"];

        // --- Tampilkan Info ke Serial Monitor ---
        Serial.println(">>> STATUS ALAT SAAT INI <<<");
        Serial.printf("- %s : %s\n", namaLampu, (statusLampu == 1 ? "MENYALA" : "MATI"));
        Serial.printf("- %s : %s\n", namaPompa, (statusPompa == 1 ? "MENYALA" : "MATI"));

        // --- EKSEKUSI PERINTAH (HARDWARE) ---
        // Kita simulasikan Lampu dengan LED Onboard
        if (statusLampu == 1) {
            digitalWrite(pinLED, HIGH); 
        } else {
            digitalWrite(pinLED, LOW);
        }

      }
    } else {
      Serial.printf("[HTTP] GET gagal, error: %s\n", http.errorToString(httpCode).c_str());
    }
    http.end();
  } else {
    Serial.println("WiFi Putus!");
  }

  delay(3000); // Cek instruksi baru setiap 3 detik biar responsif
}