#include <WiFi.h>
#include <HTTPClient.h>         // Kita ganti PubSubClient (MQTT) jadi HTTPClient
#include "DHT.h"
#include <LiquidCrystal_I2C.h>

// Konfigurasi LCD dan Pin
LiquidCrystal_I2C lcd(0x27, 16, 2); 
#define BUZZER_PIN 27
#define DHTPIN 4      // Sensor DHT11 dicolok ke pin D4
#define DHTTYPE DHT11 // Jenis sensor yang dipakai
#define LED_PIN 5    // Pin untuk Lampu LED Alert

// Konfigurasi WiFi dan API CI4
const char* ssid = "PUNYA ORANG"; 
const char* password = "punyafathi22";
// PASTIKAN IP 192.168.100.14 INI BENAR ADALAH IP LAPTOP YOGA 6 KAMU!
const char* serverName = "http://192.168.100.14:8080/api/sensor"; 

DHT dht(DHTPIN, DHTTYPE);

void setup_wifi() {
  delay(10);
  Serial.println();
  Serial.print("Menghubungkan ke WiFi: ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.println("WiFi Terhubung!");
  Serial.print("IP Address ESP32: ");
  Serial.println(WiFi.localIP());
}

void setup() {
  Serial.begin(115200);
  
  pinMode(LED_PIN, OUTPUT);
  digitalWrite(LED_PIN, LOW); 
  
  setup_wifi();
  dht.begin();

  pinMode(BUZZER_PIN, OUTPUT);
  lcd.init();       
  lcd.backlight();  
  lcd.setCursor(0,0);
  lcd.print("Sistem Maggot");
}

void loop() {
  // Membaca data suhu dan kelembaban
  float t = dht.readTemperature();
  float h = dht.readHumidity();

  if (isnan(t) || isnan(h)) {
    Serial.println("Gagal membaca sensor DHT11! Cek kabel.");
    delay(2000);
    return;
  }

  // Tampilkan di Serial Monitor
  Serial.print("Suhu: ");
  Serial.print(t);
  Serial.print(" °C | Kelembaban: ");
  Serial.print(h);
  Serial.println(" %");

  // Tampilkan langsung ke LCD
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Suhu: ");
  lcd.print(t);
  lcd.print(" C");

  lcd.setCursor(0, 1);
  lcd.print("Lembab: ");
  lcd.print(h);
  lcd.print(" %");

  // Logika Peringatan (Alert) Lokal
  if (t > 30.0) {
    digitalWrite(LED_PIN, HIGH);
    digitalWrite(BUZZER_PIN, HIGH); // Buzzer bunyi
    Serial.println("🚨 AWAS: SUHU KANDANG TERLALU PANAS!");
  } else {
    digitalWrite(LED_PIN, LOW);
    digitalWrite(BUZZER_PIN, LOW);  // Buzzer mati
  }

  // =========================================================
  // PENGIRIMAN DATA KE WEB CI4
  // =========================================================
  if(WiFi.status() == WL_CONNECTED){
    HTTPClient http;
    http.begin(serverName);
    http.addHeader("Content-Type", "application/json");

    // Format JSON diubah menjadi suhu & kelembaban agar cocok dengan CI4
    String httpRequestData = "{\"suhu\":\"" + String(t) + "\",\"kelembaban\":\"" + String(h) + "\"}";
    
    int httpResponseCode = http.POST(httpRequestData);
    
    if (httpResponseCode > 0) {
      Serial.print("✅ Berhasil Kirim ke Web! HTTP Code: ");
      Serial.println(httpResponseCode); // Harusnya muncul 201
    } else {
      Serial.print("❌ Gagal Kirim ke Web. Error code: ");
      Serial.println(httpResponseCode);
    }
    http.end();
  } else {
    Serial.println("WiFi Terputus...");
  }

  // Kirim data setiap 5 detik (Jangan terlalu cepat agar web tidak ngelag)
  delay(5000); 
}