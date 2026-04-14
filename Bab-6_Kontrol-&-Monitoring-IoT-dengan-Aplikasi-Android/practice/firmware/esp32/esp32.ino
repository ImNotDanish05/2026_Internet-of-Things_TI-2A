#include <WiFi.h>
#include <FirebaseESP32.h>
#include <DHT.h>

// 1. Kredensial Firebase (Ambil dari Project Settings)
#define FIREBASE_HOST "https://your-api-firebase" 
#define FIREBASE_AUTH "your-token-:D"

// 2. Kredensial WiFi
#define WIFI_SSID ""
#define WIFI_PASSWORD ""

// 3. Konfigurasi Sensor
#define DHTPIN 27
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);

#define LDRPIN 34
#define LED_PIN 2

// Objek Firebase
FirebaseData firebaseData;
FirebaseAuth auth;
FirebaseConfig config;

void setup() {
  Serial.begin(115200);
  dht.begin();
  pinMode(LED_PIN, OUTPUT);

  // Konek WiFi
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  while (WiFi.status() != WL_CONNECTED) { delay(500); Serial.print("."); }
  Serial.println("\nWiFi Connected!");

  // --- SETUP FIREBASE ---
  config.database_url = FIREBASE_HOST;
  config.signer.tokens.legacy_token = FIREBASE_AUTH;

  Firebase.begin(&config, &auth);
  Firebase.reconnectWiFi(true);
}

void loop() {
  // --- A. MONITORING (Kirim ke Firebase) ---
  float h = dht.readHumidity();
  float t = dht.readTemperature();
  int ldr = analogRead(LDRPIN);

  if (!isnan(h) && !isnan(t)) {
    // KEMBALI PAKAI SINTAKS LAMA (Tanpa .RTDB dan &)
    Firebase.setFloat(firebaseData, "/suhu", t);
    Firebase.setFloat(firebaseData, "/kelembapan", h);
    Firebase.setInt(firebaseData, "/cahaya", ldr);
    Serial.println("Data terkirim ke Firebase!");
  }

  // --- B. KONTROL (Baca dari Firebase) ---
  if (Firebase.getInt(firebaseData, "/led")) {
    int statusLED = firebaseData.intData();
    digitalWrite(LED_PIN, statusLED == 1 ? HIGH : LOW);
    Serial.print("Status LED dari Cloud: ");
    Serial.println(statusLED);
  } else {
    Serial.println(firebaseData.errorReason());
  }

  delay(2000); // Update setiap 2 detik
}