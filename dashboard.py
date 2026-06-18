import streamlit as st
import mysql.connector
import pandas as pd

# Kamus terjemahan hari ke Bahasa Indonesia
nama_hari = {
    'Monday': 'Senin', 'Tuesday': 'Selasa', 'Wednesday': 'Rabu', 
    'Thursday': 'Kamis', 'Friday': 'Jumat', 'Saturday': 'Sabtu', 'Sunday': 'Minggu'
}

# Konfigurasi Halaman Web
st.set_page_config(page_title="Smart Maggot Farming", page_icon="🐛", layout="wide")

# --- REVISI BARU: FITUR LOGIN SIDEBAR ---
st.sidebar.markdown("## 🔐 Hak Akses Sistem")
role = st.sidebar.selectbox("Masuk Sebagai:", ["Pilih Akun...", "User (View Only)", "Admin (Full Access)"])

# Jika belum login, tahan tampilan web
if role == "Pilih Akun...":
    st.warning("⚠️ Silakan pilih akun (User/Admin) di menu sebelah kiri untuk masuk ke Dashboard.")
    st.stop()
st.sidebar.divider()

# 1. Fungsi Koneksi ke Database XAMPP
def get_db_connection():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="smart_maggot"
    )

# 2. Desain Header Web
st.markdown("<h1 style='text-align: center; color: #2e7d32;'>🌱 Dashboard Smart Maggot Farming</h1>", unsafe_allow_html=True)
st.markdown("<p style='text-align: center;'>Sekolah Alam Indonesia Cibinong</p>", unsafe_allow_html=True)
st.divider()

# Membagi layar jadi 2 kolom
col1, col2 = st.columns([2, 1])

with col1:
    st.subheader("📈 Monitoring Suhu & Kelembaban")
    try:
        db = get_db_connection()
        # Mengambil 50 data terbaru
        query = "SELECT waktu, suhu, kelembaban FROM log_sensor ORDER BY waktu DESC LIMIT 50"
        df_sensor = pd.read_sql(query, db)
        db.close()

        if not df_sensor.empty:
            # Membalik urutan data (terlama di kiri, terbaru di kanan)
            df_sensor = df_sensor.iloc[::-1]
            
            # REVISI 2: Menerjemahkan format jam menjadi nama hari
            df_sensor['waktu'] = pd.to_datetime(df_sensor['waktu'])
            df_sensor['Hari'] = df_sensor['waktu'].dt.day_name().map(nama_hari)
            
            # Menjadikan kolom 'Hari' sebagai sumbu X di grafik
            df_sensor.set_index('Hari', inplace=True)
            
            # Tampilkan grafik
            st.line_chart(df_sensor[['suhu', 'kelembaban']], color=["#ff5252", "#448aff"])
        else:
            st.info("Belum ada data sensor yang masuk ke database.")
    except Exception as e:
        st.error(f"Gagal memuat grafik: {e}")

with col2:
    # ----------------------------------------------------
    # KONDISI 1: JIKA LOGIN SEBAGAI ADMIN
    # ----------------------------------------------------
    if role == "Admin (Full Access)":
        st.subheader("📝 Form Input Harian (Admin)")
        st.info("Akses Penuh: Tambah, Hapus, dan Proses Data.")
        
        # [C]REATE: Form Input untuk tabel log_harian
        with st.form("form_harian", clear_on_submit=True):
            berat = st.number_input("Berat Maggot (Kg)", min_value=0.0, step=0.1)
            berat_pakan = st.number_input("Berat Pakan (Kg)", min_value=0.0, step=0.1)
            submit_btn = st.form_submit_button("Simpan Data")

            if submit_btn:
                try:
                    db = get_db_connection()
                    cursor = db.cursor()
                    sql = "INSERT INTO log_harian (berat_maggot, berat_pakan) VALUES (%s, %s)"
                    cursor.execute(sql, (berat, berat_pakan))
                    db.commit()
                    db.close()
                    st.success("✅ Data Harian Berhasil Disimpan!")
                    st.rerun() # Refresh otomatis setelah simpan
                except Exception as e:
                    st.error(f"Gagal menyimpan data: {e}")
        
        # --- REVISI: TOMBOL PROSES PRD DOSEN (MENGHITUNG FCR) ---
        st.markdown("---")
        st.subheader("⚙️ Proses PRD: Hitung FCR")
        st.caption("Feed Conversion Ratio (Efisiensi Pakan)")
        
        if st.button("🚀 Hitung Efisiensi Pakan (FCR)"):
            try:
                db = get_db_connection()
                cursor = db.cursor()
                # Mengambil total pakan dan total maggot dari database
                cursor.execute("SELECT SUM(berat_pakan), SUM(berat_maggot) FROM log_harian")
                result = cursor.fetchone()
                db.close()

                # Mencegah error jika database masih kosong
                total_pakan = float(result[0] or 0.0)
                total_maggot = float(result[1] or 0.0)

                if total_maggot > 0:
                    fcr = total_pakan / total_maggot
                    st.success("✅ Proses Kalkulasi FCR Berhasil Dijalankan!")
                    
                    # Menampilkan metrik hasil yang keren
                    col_a, col_b, col_c = st.columns(3)
                    col_a.metric("Total Pakan", f"{total_pakan:.1f} Kg")
                    col_b.metric("Total Panen", f"{total_maggot:.1f} Kg")
                    col_c.metric("Nilai FCR", f"{fcr:.2f}")
                    
                    # Memberikan kesimpulan otomatis berdasarkan standar nilai FCR Maggot
                    if fcr < 1.5:
                        st.info("🌟 FCR Sangat Baik! Pakan dikonversi menjadi maggot dengan sangat efisien.")
                    elif fcr <= 2.5:
                        st.info("👍 FCR Normal/Ideal. Efisiensi pakan terpantau baik.")
                    else:
                        st.warning("⚠️ FCR Tinggi! Terlalu banyak pakan yang diberikan dibandingkan hasil panen.")
                else:
                    st.error("Gagal menghitung: Total panen maggot masih 0 Kg. Masukkan data panen terlebih dahulu.")

            except Exception as e:
                st.error(f"Terjadi kesalahan saat menghitung FCR: {e}")

        # [D]ELETE: Melengkapi fungsi CRUD untuk Admin
        st.markdown("---")
        st.subheader("🗑️ Hapus Data Terbaru")
        if st.button("❌ Hapus 1 Data Terakhir"):
            try:
                db = get_db_connection()
                cursor = db.cursor()
                cursor.execute("SELECT id FROM log_harian ORDER BY id DESC LIMIT 1")
                id_terakhir = cursor.fetchone()
                if id_terakhir:
                    cursor.execute(f"DELETE FROM log_harian WHERE id = {id_terakhir[0]}")
                    db.commit()
                    st.success(f"🗑️ Data ID {id_terakhir[0]} berhasil dihapus!")
                    st.rerun() # Refresh otomatis setelah hapus
                else:
                    st.warning("Tidak ada data yang bisa dihapus.")
                db.close()
            except Exception as e:
                st.error(f"Gagal menghapus data: {e}")

    # ----------------------------------------------------
    # KONDISI 2: JIKA LOGIN SEBAGAI USER
    # ----------------------------------------------------
    elif role == "User (View Only)":
        st.subheader("🔒 Mode User")
        st.warning("Anda hanya bisa melihat grafik dan riwayat data (View Only). Fitur Input dan PRD dikunci.")

    # ----------------------------------------------------
    # MENAMPILKAN RIWAYAT INPUT (Dilihat Semua Role)
    # ----------------------------------------------------
    st.markdown("---")
    st.subheader("📋 Riwayat Input Harian")
    try:
        db = get_db_connection()
        query_harian = "SELECT tanggal, berat_maggot AS 'Berat Maggot (Kg)', berat_pakan AS 'Berat Pakan (Kg)' FROM log_harian ORDER BY id DESC LIMIT 5"
        df_harian = pd.read_sql(query_harian, db)
        db.close()
        
        if not df_harian.empty:
            # Menampilkan sebagai tabel interaktif tanpa nomor index
            st.dataframe(df_harian, use_container_width=True, hide_index=True)
        else:
            st.caption("Belum ada data harian.")
    except Exception as e:
        st.error(f"Gagal memuat data harian: {e}")

st.divider()
st.caption("Project Engineer IoT & Sistem | Kelompok 5 - PNJ")