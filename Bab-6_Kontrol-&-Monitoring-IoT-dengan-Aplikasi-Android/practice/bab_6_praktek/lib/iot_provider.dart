import 'package:flutter/material.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_database/firebase_database.dart';

class IoTProvider extends ChangeNotifier {
  // Pastikan URL ini sesuai dengan yang ada di firebase_options.dart atau console kamu
  final DatabaseReference _dbRef = FirebaseDatabase.instanceFor(
    app: Firebase.app(),
    databaseURL:
        'https://esiot2022-8bf65-default-rtdb.asia-southeast1.firebasedatabase.app/',
  ).ref();

  // State Variables
  double _suhu = 0.0;
  double _kelembapan = 0.0;
  int _cahaya = 0;
  int _led = 0;

  // Getters
  double get suhu => _suhu;
  double get kelembapan => _kelembapan;
  int get cahaya => _cahaya;
  int get led => _led;

  IoTProvider() {
    _listenToSensors();
  }

  // Fungsi untuk mendengarkan perubahan data di Firebase (Real-time)
  void _listenToSensors() {
    _dbRef.child('suhu').onValue.listen((event) {
      if (event.snapshot.value != null) {
        _suhu = double.parse(event.snapshot.value.toString());
        notifyListeners(); // Update UI
      }
    });

    _dbRef.child('kelembapan').onValue.listen((event) {
      if (event.snapshot.value != null) {
        _kelembapan = double.parse(event.snapshot.value.toString());
        notifyListeners();
      }
    });

    _dbRef.child('cahaya').onValue.listen((event) {
      if (event.snapshot.value != null) {
        _cahaya = int.parse(event.snapshot.value.toString());
        notifyListeners();
      }
    });

    _dbRef.child('led').onValue.listen((event) {
      if (event.snapshot.value != null) {
        _led = int.parse(event.snapshot.value.toString());
        notifyListeners();
      }
    });
  }

  // Fungsi Evaluasi 1: Mengontrol LED
  void toggleLed(bool isNyala) {
    int value = isNyala ? 1 : 0;
    _dbRef.child('led').set(value);
    // Kita tidak perlu set _led secara manual di sini karena listener 'led' di atas akan otomatis menangkap perubahannya
  }
}
