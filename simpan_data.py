import paho.mqtt.client as mqtt
import mysql.connector
import json

# 1. Buka Koneksi ke Database MySQL (bawaan XAMPP)
db = mysql.connector.connect(
    host="localhost",
    user="root",        # Username bawaan XAMPP
    password="",        # Password bawaan XAMPP biasanya kosong
    database="smart_maggot"
)
cursor = db.cursor()

# 2. Fungsi Saat Data MQTT dari ESP32 Masuk
def on_message(client, userdata, msg):
    try:
        # Menerjemahkan data JSON
        payload = msg.payload.decode('utf-8')
        data = json.loads(payload)
        suhu = data['temp']
        kelembaban = data['hum']
        
        print(f"📥 Data Masuk - Suhu: {suhu} °C | Kelembaban: {kelembaban} %")
        
        # 3. Menyimpan Data ke Tabel MySQL
        sql = "INSERT INTO log_sensor (suhu, kelembaban) VALUES (%s, %s)"
        val = (suhu, kelembaban)
        cursor.execute(sql, val)
        db.commit() # Wajib pakai commit agar data tersimpan permanen
        
        print("✅ Tersimpan di Database!")
        print("-" * 40)
        
    except Exception as e:
        print(f"❌ Error: {e}")

# 4. Pengaturan Server MQTT Mosquitto
client = mqtt.Client(mqtt.CallbackAPIVersion.VERSION1)

# ⚠️ PENTING: Pastikan IP Address ini SAMA dengan IP Address di kodingan Arduino/Laptopmu sekarang!
client.connect("192.168.100.14", 1883) 

client.subscribe("iot/sensor")
client.on_message = on_message

print("⏳ Menunggu data dari ESP32...")
client.loop_forever() # Program akan standby terus menerus