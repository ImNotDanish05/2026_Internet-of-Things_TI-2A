# 🚀 Bab 3: NodeMCU Mode Client (IoT Database Access)

> Dibuat dengan penuh keringat, logika, dan uap timah solder oleh **ImNotDanish05** & **Tara (AI Assistant)** 🛠️✨

Proyek ini adalah implementasi praktis dari **Buku Ajar 1: Internet of Things (Bab 3)**. Proyek ini mendemonstrasikan bagaimana mikrokontroler NodeMCU (ESP8266) bertindak sebagai **Client** untuk menarik data status sensor secara *real-time* dari database **MySQL** melalui perantara **PHP** (Zero-Dependency REST API).

Berbeda dengan versi buku, repositori ini sudah di-**refactor** untuk efisiensi:
- Menggunakan 1 tabel database yang fleksibel.
- Menerapkan file `.env` untuk keamanan kredensial.
- Script PHP yang dioptimalkan tanpa *framework* berat.
- Kodingan Arduino C++ yang lebih bersih (*clean code*) dengan perbaikan *bug* dari buku aslinya.

---

## 📂 Struktur Repositori

Bab-3_Mikrokontroller-Mode-Client/
├── backend/
│   ├── .env.example          # Template environment variables
│   ├── db_init.php           # Seeder: Pembuat Database & Tabel
│   ├── db_seed.php           # Seeder: Pengisi Data Dummy
│   ├── sensorsingle.php      # Endpoint untuk baca 1 data (Single)
│   └── sensormulti.php       # Endpoint untuk baca semua data (Multi)
├── firmware/
│   ├── NodeMCU_Single/
│   │   └── NodeMCU_Single.ino # Kode Arduino untuk 1 LED
│   └── NodeMCU_Multi/
│       └── NodeMCU_Multi.ino  # Kode Arduino untuk 4 LED sekaligus
└── README.md

---

## 🛠️ Persyaratan Sistem (Prerequisites)

- **Hardware:** NodeMCU ESP8266 (V1/V2/V3), 4x LED, 4x Resistor 220 Ohm, Kabel Jumper, Breadboard.
- **Software:** OS Linux (Ubuntu/Debian dkk direkomendasikan), PHP 7.4+, MySQL/MariaDB, Arduino IDE.

---

## 🚀 Cara Menjalankan Proyek (Execution Guide)

### TAHAP 1: Setup Backend & Database (Linux Environment)

1. **Siapkan Konfigurasi `.env`**
   Masuk ke folder `backend/`, buat file bernama `.env` (atau *rename* dari `.env.example`). Isi dengan kredensial MySQL lokal kamu:
   \`\`\`env
   DB_HOST=localhost
   DB_DATABASE=eslolin
   DB_USERNAME=root
   DB_PASSWORD=password_mysql_kamu
   DB_TABLE=sensor
   \`\`\`

2. **Jalankan Database Seeder**
   Buka terminal di dalam folder `backend/` dan jalankan perintah ini secara berurutan untuk membuat database dan mengisi data *dummy*:
   \`\`\`bash
   php db_init.php
   php db_seed.php
   \`\`\`
   *Output yang diharapkan: "✅ Tabel 'sensor' berhasil dibuat!" dan "🚀 Data berhasil di-push!"*

3. **Nyalakan Local Web Server**
   Agar NodeMCU bisa mengakses PHP, nyalakan server bawaan PHP yang *listen* ke semua IP di jaringan WiFi (host `0.0.0.0`):
   \`\`\`bash
   php -S 0.0.0.0:8000
   \`\`\`
   *(Biarkan terminal ini tetap terbuka).*

---

### TAHAP 2: Setup Firmware (NodeMCU)

1. **Cek IP Address Laptop Server**
   Buka terminal baru, ketik `hostname -I` (atau `ip a`). Catat IP lokal kamu (contoh: `192.168.1.15`). Pastikan Laptop dan NodeMCU berada di **satu jaringan WiFi yang sama**.

2. **Wiring Hardware**
   Rakit komponen dengan konfigurasi pin berikut:
   - LED 1 -> Pin **D1** (GPIO 5) -> Resistor -> GND
   - LED 2 -> Pin **D2** (GPIO 4) -> Resistor -> GND
   - LED 3 -> Pin **D3** (GPIO 0) -> Resistor -> GND
   - LED 4 -> Pin **D4** (GPIO 2) -> Resistor -> GND

3. **Konfigurasi Arduino IDE**
   Buka file `NodeMCU_Multi.ino` (atau Single). Ubah 3 variabel global di bagian paling atas kode:
   \`\`\`cpp
   const char* ssid = "NAMA_WIFI_KAMU";
   const char* password = "PASSWORD_WIFI_KAMU";
   const String serverIP = "http://192.168.1.15:8000"; // GANTI DENGAN IP LAPTOPMU
   \`\`\`

4. **Flash / Upload ke NodeMCU**
   - Pastikan *Board* (NodeMCU 1.0) dan *Port* sudah terpilih di Arduino IDE.
   - **Tips Pengguna Linux:** Jika terkena error `Permission denied` pada port, jalankan ini di terminal:
     \`\`\`bash
     sudo chmod 666 /dev/ttyUSB0
     \`\`\`
     *(Ganti `ttyUSB0` sesuai port yang terdeteksi).*
   - Klik **Upload**.

---

### TAHAP 3: Testing & Monitoring

1. Buka **Serial Monitor** di Arduino IDE. Set *Baud Rate* ke **115200**.
2. Perhatikan log koneksi. Jika berhasil, NodeMCU akan menampilkan:
   \`\`\`text
   Connecting to [Nama WiFi]
   .....
   WiFi connected
   192.168.1.xx
   \`\`\`
3. NodeMCU akan mulai melakukan request `GET` ke file PHP. Jika data berhasil di-*parsing*, 4 LED pada *breadboard* akan menyala/mati sesuai nilai `0` atau `1` yang ada di database MySQL!

---

## ⚠️ Troubleshooting (Pengecekan Korslet)

- **NodeMCU tidak bisa request ke PHP (HTTP Code -1)?**
  Cek *Firewall* Linux kamu. Pastikan port 8000 terbuka: `sudo ufw allow 8000/tcp`.
- **Lampu LED tidak sesuai status Database?**
  Periksa apakah kamu menggunakan LED biasa atau modul *Relay active-low*. Jika status terbalik, ubah logika penugasan dari `OFF : ON` menjadi `ON : OFF` pada bagian `digitalWrite` di dalam fungsi `loop()`.

---
*Happy Hacking & Soldering! Jangan lupa matikan power kalau tercium bau gosong! 🔥*
