#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>

// Konfigurasi WiFi
const char* ssid = "";
const char* password = "";

// Konfigurasi Server (Ambil dari .env secara konsep, di sini kita hardcode IP-nya)
const String serverURL = "http://[IP_ADDRESS]/input_evaluasi.php";

// Konfigurasi Sensor
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
    float h = dht.readTemperature();
    int ldrVal = analogRead(LDRPIN);
    
    // Logika Status Cahaya (Soal No. 1)
    String status;
    if (ldrVal > 210) status = "Terang";
    else if (ldrVal > 150) status = "Cerah";
    else if (ldrVal > 50) status = "Redup";
    else status = "Gelap";

    // Kirim Data ke Server via HTTP GET
    HTTPClient http;
    String url = serverURL + "?suhu=" + String(h) + "&ldr=" + String(ldrVal) + "&status=" + status;
    
    http.begin(url);
    int httpCode = http.GET();
    
    if (httpCode > 0) {
      Serial.println("Data Terkirim: " + url);
      digitalWrite(pinLED, HIGH); // Indikator kirim berhasil
      delay(200);
      digitalWrite(pinLED, LOW);
    }
    http.end();
  }
  delay(5000); // Kirim data setiap 5 detik
}