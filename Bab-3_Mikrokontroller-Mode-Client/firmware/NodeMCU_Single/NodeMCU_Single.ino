#include <WiFi.h>
#include <HTTPClient.h>

// --- VARIABEL GLOBAL ---
const char* ssid = "NAMA_WIFI_LU";     // Ganti pake SSID WiFi lu
const char* password = "PASSWORD_WIFI"; // Ganti pake Password WiFi lu
const String serverIP = "http://10.26.33.214";
int pinLED = 5; // Pin D1 pada NodeMCU

#define ON HIGH
#define OFF LOW

void setup() {
    // Konfigurasi Serial Monitor
    Serial.begin(115200);

    // Proses Connect ke WiFi
    Serial.print("Connecting to ");
    Serial.println(ssid);
    WiFi.begin(ssid, password);

    // Jeda loading biar koneksi stabil
    for(uint8_t t = 4; t > 0; t--) {
        Serial.printf("[SETUP] WAIT %d...\n", t);
        Serial.flush();
        delay(1000);
    }

    Serial.println("");
    Serial.println("WiFi connected");
    Serial.println(WiFi.localIP());

    // Deklarasi PIN LED sebagai Output
    pinMode(pinLED, OUTPUT);
}

void loop() {
    // Cek apakah masih konek WiFi
    if(WiFi.status() == WL_CONNECTED){
        HTTPClient http;

        Serial.print("[HTTP] begin...\n");
        
        // --- NOTE: Ganti IP sesuai IP Laptop Linux lu! ---
        // Contoh: http://192.168.1.15:8000/sensorsingle.php
        http.begin(serverIP + "/sensorsingle.php");

        Serial.print("[HTTP] GET...\n");
        int httpCode = http.GET(); // Ambil data dari PHP
        
        if(httpCode > 0){
            Serial.printf("[HTTP] GET... code: %d\n", httpCode);

            // Jika file ditemukan di server (HTTP 200 OK)
            if(httpCode == HTTP_CODE_OK) {
                String status = http.getString(); // Baca isi dari PHP (0 atau 1)
                Serial.println("Data dari DB: " + status);

                // Logika: Jika dari DB dapet "0", LED MATI. Selain itu NYALA.
                if(status.indexOf("0") != -1){
                    digitalWrite(pinLED, OFF);
                    Serial.println("LED Posisi OFF");
                } else {
                    digitalWrite(pinLED, ON);
                    Serial.println("LED Posisi ON");
                }
            } else {
                Serial.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
            }
        }
        http.end(); // Tutup koneksi HTTP
    } else {
        Serial.println("Waiting for WiFi...");
    }
    
    delay(1000); // Tunggu 1 detik sebelum nanya lagi ke server
}