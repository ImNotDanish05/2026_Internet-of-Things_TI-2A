#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>

// --- VARIABEL GLOBAL ---
const char* ssid = "NAMA_WIFI_LU";     // Ganti ke WiFi lu
const char* password = "PASSWORD_LU";   // Ganti ke Password lu
const String serverIP = "http://[IP_ADDRESS]";

// Inisialisasi PIN (Gue pake 'D' gede biar standar Arduino IDE)
int led1 = D1; 
int led2 = D2;
int led3 = D3;
int led4 = D4;

#define ON HIGH
#define OFF LOW

int firstVal, secondVal;

void setup() {
    Serial.begin(115200);

    // Proses Konek WiFi
    Serial.print("Connecting to ");
    Serial.println(ssid);
    WiFi.begin(ssid, password);
    
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    
    Serial.println("");
    Serial.println("WiFi connected");
    Serial.println(WiFi.localIP());

    // Setting Pin LED jadi Output
    pinMode(led1, OUTPUT);
    pinMode(led2, OUTPUT);
    pinMode(led3, OUTPUT);
    pinMode(led4, OUTPUT);
}

// --- FUNGSI PARSING (Buat pecah data koma) ---
String getValue(String data, char separator, int index) {
    int found = 0;
    int strIndex[] = {0, -1};
    int maxIndex = data.length() - 1;

    for (int i = 0; i <= maxIndex && found <= index; i++) {
        if (data.charAt(i) == separator || i == maxIndex) {
            found++;
            strIndex[0] = strIndex[1] + 1;
            strIndex[1] = (i == maxIndex) ? i + 1 : i;
        }
    }
    return found > index ? data.substring(strIndex[0], strIndex[1]) : "";
}

void loop() {
    if(WiFi.status() == WL_CONNECTED){
        HTTPClient http;

        // --- NOTE: Ganti IP & Port sesuai server Linux lu! ---
        http.begin(serverIP + "/sensormulti.php"); 
        
        int httpCode = http.GET();
        if(httpCode > 0){
            String status = http.getString(); // Misal isinya: "1,0,1,0,"
            
            // --- TARA FIX: Perbaikan variabel Print agar tidak tertukar ---
            String nilai1 = getValue(status, ',', 0);
            Serial.print("LED 1 --> "); Serial.println(nilai1);
            
            String nilai2 = getValue(status, ',', 1);
            Serial.print("LED 2 --> "); Serial.println(nilai2);
            
            String nilai3 = getValue(status, ',', 2);
            Serial.print("LED 3 --> "); Serial.println(nilai3);
            
            String nilai4 = getValue(status, ',', 3);
            Serial.print("LED 4 --> "); Serial.println(nilai4);
            
            Serial.println("------------------------");

            // --- LOGIKA KONTROL (Sesuai Buku: 1=OFF, 0=ON) ---
            digitalWrite(led1, (nilai1 == "1") ? OFF : ON);
            digitalWrite(led2, (nilai2 == "1") ? OFF : ON);
            digitalWrite(led3, (nilai3 == "1") ? OFF : ON);
            digitalWrite(led4, (nilai4 == "1") ? OFF : ON);
        }
        http.end();
    } else {
        Serial.println("Reconnecting WiFi...");
    }
    delay(500); // Cek tiap setengah detik
}s