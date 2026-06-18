#include <WiFi.h>
#include <PubSubClient.h>
#include "DHT.h"
#include <LiquidCrystal_I2C.h>

// Konfigurasi LCD dan Pin
LiquidCrystal_I2C lcd(0x27, 16, 2); 
#define BUZZER_PIN 27
#define DHTPIN 4      // Sensor DHT11 dicolok ke pin D4
#define DHTTYPE DHT11 // Jenis sensor yang dipakai
#define LED_PIN 5    // Pin untuk Lampu LED Alert

// Konfigurasi WiFi dan MQTT
const char* ssid = "AAFATHI"; 
const char* password = "mamahfathi";
const char* mqtt_server = "192.168.100.14"; 

WiFiClient espClient;
PubSubClient client(espClient);
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
}

void reconnect() {
  while (!client.connected()) {
    Serial.print("Mencoba terhubung ke MQTT Broker... ");
    if (client.connect("ESP32Client")) {
      Serial.println("BERHASIL!");
    } else {
      Serial.print("Gagal, status=");
      Serial.print(client.state());
      Serial.println(" -> Coba lagi dalam 5 detik");
      delay(5000);
    }
  }
}

// INI FUNGSI YANG TADI TIDAK SENGAJA TERHAPUS
void setup() {
  Serial.begin(115200);
  
  pinMode(LED_PIN, OUTPUT);
  digitalWrite(LED_PIN, LOW); 
  
  setup_wifi();
  client.setServer(mqtt_server, 1883); 
  dht.begin();

  pinMode(BUZZER_PIN, OUTPUT);
  lcd.init();       
  lcd.backlight();  
  lcd.setCursor(0,0);
  lcd.print("Sistem Maggot");
}

void loop() {
  if (!client.connected()) {
    reconnect();
  }
  client.loop();

  // Membaca data suhu dan kelembaban
  float t = dht.readTemperature();
  float h = dht.readHumidity();

  if (isnan(t) || isnan(h)) {
    Serial.println("Gagal membaca sensor DHT11!");
    delay(2000);
    return;
  }

  // Mengirim payload JSON ke MQTT Laptop
  String payload = "{\"temp\":" + String(t) + ",\"hum\":" + String(h) + "}";
  client.publish("iot/sensor", payload.c_str());

  // Tampilkan di Serial Monitor
  Serial.print("Suhu: ");
  Serial.print(t);
  Serial.print(" °C | Kelembaban: ");
  Serial.print(h);
  Serial.println(" %");

  // Tampilkan langsung ke LCD tanpa harus menunggu delay
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Suhu: ");
  lcd.print(t);
  lcd.print(" C");

  lcd.setCursor(0, 1);
  lcd.print("Lembab: ");
  lcd.print(h);
  lcd.print(" %");

  // Logika Peringatan (Alert)
  if (t > 30.0) {
    digitalWrite(LED_PIN, HIGH);
    digitalWrite(BUZZER_PIN, HIGH); // Buzzer bunyi
    Serial.println("🚨 AWAS: SUHU KANDANG TERLALU PANAS!");
  } else {
    digitalWrite(LED_PIN, LOW);
    digitalWrite(BUZZER_PIN, LOW);  // Buzzer mati
  }

  delay(3000); 
}