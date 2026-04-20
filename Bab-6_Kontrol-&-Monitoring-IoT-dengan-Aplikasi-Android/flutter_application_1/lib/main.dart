import 'package:flutter/material.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:provider/provider.dart';
import 'package:getwidget/getwidget.dart';
import 'firebase_options.dart';
import 'iot_provider.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp(options: DefaultFirebaseOptions.currentPlatform);

  runApp(
    // Mendaftarkan Provider di Root
    MultiProvider(
      providers: [ChangeNotifierProvider(create: (_) => IoTProvider())],
      child: const MyApp(),
    ),
  );
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'IoT Dashboard Bab 6',
      theme: ThemeData(primarySwatch: Colors.blue),
      home: const DashboardScreen(),
    );
  }
}

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: GFAppBar(
        title: const Text("IoT Dashboard Bab 6"),
        centerTitle: true,
      ),
      // Consumer akan otomatis me-rebuild UI setiap kali ada perubahan data (notifyListeners dipanggil)
      body: Consumer<IoTProvider>(
        builder: (context, iotData, child) {
          return SingleChildScrollView(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              children: [
                // --- KARTU SUHU ---
                GFCard(
                  boxFit: BoxFit.cover,
                  title: GFListTile(
                    avatar: const GFAvatar(
                      backgroundColor: Colors.redAccent,
                      child: Icon(Icons.thermostat, color: Colors.white),
                    ),
                    titleText: 'Suhu Lingkungan',
                    subTitleText: 'Sensor DHT11',
                  ),
                  content: Column(
                    children: [
                      GFTypography(
                        text: '${iotData.suhu} °C',
                        type: GFTypographyType.typo1,
                        showDivider: false,
                      ),
                      const SizedBox(height: 10),
                      GFProgressBar(
                        percentage: iotData.suhu / 50.0, // Asumsi max suhu 50C
                        lineHeight: 20,
                        backgroundColor: Colors.black12,
                        progressBarColor: iotData.suhu > 30
                            ? GFColors.DANGER
                            : GFColors.SUCCESS,
                      ),
                    ],
                  ),
                ),

                // --- KARTU KELEMBAPAN ---
                GFCard(
                  boxFit: BoxFit.cover,
                  title: GFListTile(
                    avatar: const GFAvatar(
                      backgroundColor: Colors.blueAccent,
                      child: Icon(Icons.water_drop, color: Colors.white),
                    ),
                    titleText: 'Kelembapan Udara',
                    subTitleText: 'Sensor DHT11',
                  ),
                  content: Column(
                    children: [
                      GFTypography(
                        text: '${iotData.kelembapan} %',
                        type: GFTypographyType.typo1,
                        showDivider: false,
                      ),
                      const SizedBox(height: 10),
                      GFProgressBar(
                        percentage: iotData.kelembapan / 100.0,
                        lineHeight: 20,
                        backgroundColor: Colors.black12,
                        progressBarColor: GFColors.INFO,
                      ),
                    ],
                  ),
                ),

                // --- KARTU CAHAYA (LDR) ---
                GFCard(
                  boxFit: BoxFit.cover,
                  title: GFListTile(
                    avatar: const GFAvatar(
                      backgroundColor: Colors.amber,
                      child: Icon(Icons.light_mode, color: Colors.white),
                    ),
                    titleText: 'Intensitas Cahaya',
                    subTitleText: 'Sensor LDR (Pin 34)',
                  ),
                  content: Column(
                    children: [
                      GFTypography(
                        text: '${iotData.cahaya} ADC',
                        type: GFTypographyType.typo1,
                        showDivider: false,
                      ),
                      const SizedBox(height: 10),
                      GFProgressBar(
                        percentage:
                            iotData.cahaya /
                            4095.0, // Max ADC ESP32 adalah 4095
                        lineHeight: 20,
                        backgroundColor: Colors.black12,
                        progressBarColor: GFColors.WARNING,
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 20),

                // --- EVALUASI 1: KONTROL LED ---
                GFCard(
                  title: GFListTile(
                    avatar: GFAvatar(
                      backgroundColor: iotData.led == 1
                          ? GFColors.SUCCESS
                          : GFColors.SECONDARY,
                      child: const Icon(Icons.lightbulb, color: Colors.white),
                    ),
                    titleText: 'Kontrol LED ESP32',
                    subTitleText: 'Menyalakan LED pada Pin 2',
                  ),
                  content: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        iotData.led == 1 ? "Status: MENYALA" : "Status: MATI",
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      GFToggle(
                        onChanged: (val) {
                          // Memanggil fungsi dari Provider untuk mengubah data di Firebase
                          if (val != null) {
                            iotData.toggleLed(val);
                          }
                        },
                        value: iotData.led == 1,
                        type: GFToggleType.ios,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
