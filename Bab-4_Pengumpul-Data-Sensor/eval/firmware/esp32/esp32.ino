#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>

// Konfigurasi WiFi
const char* ssid = "";
const char* password = "";

// Jangan lupa ganti IP dan pastikan arahnya ke file PHP Bab 4
const String serverURL = "http://[IP_ADDRESS]/sensordata.php";

// Konfigurasi Sensor (TETAP SAMA)
#define DHTPIN 27
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);

#define LDRPIN 34
int pinLED = 2; // Onboard LED

void setup() {
  Serial.begin(115200);
  dht.begin();
  pinMode(pinLED, OUTPUT);
  
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Connected!");
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    // 1. Panen Semua Data (Suhu, Kelembapan, Cahaya)
    float h = dht.readHumidity();       // Baca Kelembapan
    float t = dht.readTemperature();    // Baca Suhu
    int ldrVal = analogRead(LDRPIN);    // Baca Lumen
    
    // 2. Logika Status Cahaya (Dari kodemu)
    String status;
    if (ldrVal > 210) status = "Terang";
    else if (ldrVal > 150) status = "Cerah";
    else if (ldrVal > 50) status = "Redup";
    else status = "Gelap";

    // 3. Rakit URL Paket Komplit
    HTTPClient http;
    // Format: ?suhu=xx&humidity=xx&lumen=xx&status=xx
    String url = serverURL + "?suhu=" + String(t) + "&humidity=" + String(h) + "&lumen=" + String(ldrVal) + "&status=" + status;
    
    http.begin(url);
    int httpCode = http.GET();
    
    if (httpCode > 0) {
      Serial.println("Data Terkirim: " + url);
      digitalWrite(pinLED, HIGH); // Indikator kirim berhasil
      delay(200);
      digitalWrite(pinLED, LOW);
    } else {
      Serial.println("Gagal koneksi ke PHP! Error code: " + String(httpCode));
    }
    http.end();
  }
  
  delay(5000); // Kirim data setiap 5 detik
}