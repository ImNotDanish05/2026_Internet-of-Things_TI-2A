#define LDRPIN 33

void setup() {
  Serial.begin(115200);
  pinMode(LDRPIN, INPUT);
  Serial.println("LDR Test Started...");
}

void loop() {
  int nilaiLDR = analogRead(LDRPIN);
  
  Serial.print("Nilai Analog: ");
  Serial.println(nilaiLDR);

  // Logika sederhana untuk indikasi status
  if (nilaiLDR < 500) {
    Serial.println("Status: GELAP");
  } else {
    Serial.println("Status: TERANG");
  }

  delay(500); // Update setiap 0.5 detik agar terbaca jelas
}