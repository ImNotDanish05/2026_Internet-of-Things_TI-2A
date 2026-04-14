#include <WiFi.h>
#include <HTTPClient.h>

// --- VARIABEL GLOBAL ---
const char* ssid = "";     // Ganti pake SSID WiFi lu
const char* password = ""; // Ganti pake Password WiFi lu
const String serverIP = "http://[IP_ADDRESS]";
int pinLED = 2; // Pin D1 pada NodeMCU

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
    if(WiFi.status() == WL_CONNECTED){
        HTTPClient http;
        
        // Gabungin URL
        String fullURL = serverIP + "/sensorsingle.php";
        http.begin(fullURL);

        int httpCode = http.GET();
        
        if(httpCode > 0){
            if(httpCode == HTTP_CODE_OK) {
                String status = http.getString();
                
                // --- TARA'S DEBUGGING LOG ---
                status.trim(); // Hapus spasi/enter liar dari PHP
                Serial.print(">>> Respon Server: [");
                Serial.print(status);
                Serial.println("]"); 

                if(status == "1") {
                    digitalWrite(pinLED, ON);
                    Serial.println(">>> STATUS: NYALA (HIGH)");
                } 
                else if(status == "0") {
                    digitalWrite(pinLED, OFF);
                    Serial.println(">>> STATUS: MATI (LOW)");
                } 
                else {
                    Serial.println(">>> ERROR: Data bukan 0 atau 1!");
                }
                // ----------------------------

            } else {
                Serial.printf("[HTTP] Error Code: %d\n", httpCode);
            }
        } else {
            Serial.printf("[HTTP] GET failed, error: %s\n", http.errorToString(httpCode).c_str());
        }
        http.end();
    } else {
        Serial.println("WiFi Disconnected!");
    }
    
    delay(2000); // Kasih jeda 2 detik biar terminal nggak pusing baca log-nya
}