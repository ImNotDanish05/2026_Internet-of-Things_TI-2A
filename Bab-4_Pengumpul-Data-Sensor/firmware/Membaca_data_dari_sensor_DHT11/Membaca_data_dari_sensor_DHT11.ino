#include <DHT.h>

// Deklarasi pin dan tipe sensor
#define DHTPIN T2
#define DHTTYPE DHT11

DHT dht(DHTPIN, DHTTYPE);

void setup() {
  Serial.begin(9600);
  dht.begin();
}

void loop() {
  // Membaca kelembapan (h) dan suhu (t)
  float h = dht.readHumidity();
  float t = dht.readTemperature(); 
  
  // Menampilkan ke Serial Monitor
  Serial.print(F("Humidity: "));
  Serial.print(h);
  Serial.print("\n");
  
  Serial.print(F("Temperature: "));
  Serial.print(t);
  Serial.print(F("°C \n"));
  
  // Jeda 2 detik sebelum membaca lagi
  delay(2000);
}