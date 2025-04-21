# 📢 Sistem Otomatisasi Notifikasi Tugas PT. XYZ
Sistem ini dikembangkan untuk mengotomatisasi proses notifikasi tugas di lingkungan kerja PT. XYZ. Tujuannya adalah untuk mempermudah alur distribusi dan pelaporan tugas yang diberikan antar pegawai. Sistem berbasis web ini dilengkapi dengan berbagai fitur utama yang mendukung efektivitas kerja tim.

## 🚀 Fitur

**📝 Form Pengajuan Tugas**  
  Memungkinkan pemberi tugas untuk mengisi form digital yang otomatis masuk ke dalam daftar pekerjaan. Form ini dilengkapi dengan opsi:
  - Tingkat prioritas (tinggi, sedang, rendah)
  - Batas waktu penyelesaian
  Sehingga penerima tugas dapat mengetahui skala prioritas pengerjaan.

**📊 Laporan Tugas Bulanan**  
  Fitur ini memungkinkan pembuatan laporan bulanan berdasarkan tugas-tugas yang telah diselesaikan, guna memudahkan pemantauan dan evaluasi pekerjaan.

**🔔 Notifikasi Otomatis via Slack**  
  Fitur utama dari sistem ini adalah notifikasi real-time yang terintegrasi dengan aplikasi Slack melalui bot.  
  Notifikasi akan dikirimkan secara otomatis setiap kali:
  - Tugas baru diajukan
  - Tugas diselesaikan  
  Dengan demikian, baik pemberi maupun penerima tugas selalu mendapatkan pembaruan terbaru terkait status tugas mereka.

## 🛠️ Teknologi yang Digunakan
- **Backend**: [PHP Slim](https://www.slimframework.com/)  
- **Database**: MySQL  
- **Template Engine**: Twig  
- **Autentikasi**: JWT (JSON Web Token)  
- **Integrasi**: Slack API untuk bot notifikasi

## 📌 Instalasi  
1. **Klon repositori**  
   ```sh
   git clone https://github.com/ceceprokani/Tugasin-Panembangan-BE.git  
   cd Tugasin-Panembangan-BE