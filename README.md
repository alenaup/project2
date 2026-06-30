<!-- Animated SVG Header Banner -->
<div align="center">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 260" width="100%" style="border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.35); max-width: 100%;">
    <defs>
      <!-- Gradients -->
      <linearGradient id="titleGrad" x1="0%" y1="0%" x2="100%" y2="0%">
        <stop offset="0%" stop-color="#38ef7d" />
        <stop offset="50%" stop-color="#11998e" />
        <stop offset="100%" stop-color="#38ef7d" />
      </linearGradient>
      <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#0f2027" />
        <stop offset="40%" stop-color="#203a43" />
        <stop offset="100%" stop-color="#2c5364" />
      </linearGradient>
      <!-- Keyframe Animations -->
      <style>
        @keyframes rotateLeaf {
          0% { transform: translate(0px, 0px) rotate(0deg) scale(1); }
          50% { transform: translate(25px, 15px) rotate(180deg) scale(1.1); }
          100% { transform: translate(0px, 0px) rotate(360deg) scale(1); }
        }
        @keyframes floatY {
          0% { transform: translateY(0px); }
          50% { transform: translateY(-8px); }
          100% { transform: translateY(0px); }
        }
        @keyframes glowText {
          0%, 100% { text-shadow: 0 0 10px rgba(56, 239, 125, 0.4), 0 0 20px rgba(56, 239, 125, 0.2); }
          50% { text-shadow: 0 0 20px rgba(56, 239, 125, 0.8), 0 0 30px rgba(17, 153, 142, 0.6); }
        }
        @keyframes dash {
          to { stroke-dashoffset: -40; }
        }
        .anim-leaf-1 { animation: rotateLeaf 10s infinite ease-in-out; transform-origin: 100px 80px; }
        .anim-leaf-2 { animation: rotateLeaf 14s infinite ease-in-out; transform-origin: 750px 180px; }
        .anim-float { animation: floatY 4s infinite ease-in-out; }
        .anim-text { animation: glowText 3s infinite ease-in-out; }
        .border-glow { stroke-dasharray: 8; animation: dash 6s linear infinite; }
      </style>
    </defs>
    <!-- Background Card -->
    <rect width="850" height="260" rx="12" fill="url(#bgGrad)" />
    
    <!-- Neon Border -->
    <rect x="5" y="5" width="840" height="250" rx="10" fill="none" stroke="url(#titleGrad)" stroke-width="2.5" opacity="0.85" />
    <rect x="9" y="9" width="832" height="242" rx="8" fill="none" stroke="#ffffff" stroke-width="1" class="border-glow" opacity="0.15" />
    
    <!-- Tech Grid Effect -->
    <path d="M 0,65 L 850,65 M 0,130 L 850,130 M 0,195 L 850,195" stroke="rgba(255,255,255,0.04)" stroke-width="1" />
    <path d="M 170,0 L 170,260 M 340,0 L 340,260 M 510,0 L 510,260 M 680,0 L 680,260" stroke="rgba(255,255,255,0.04)" stroke-width="1" />
    
    <!-- Anime Sakura Petals & Eco Leaves -->
    <g class="anim-leaf-1">
      <path d="M 90 70 C 85 55, 65 65, 80 85 C 95 65, 95 85, 90 70 Z" fill="#38ef7d" opacity="0.75" />
      <circle cx="85" cy="73" r="1.5" fill="#fff" opacity="0.5"/>
    </g>
    
    <g class="anim-leaf-2">
      <path d="M 740 170 C 730 160, 725 175, 740 190 C 755 175, 750 160, 740 170 Z" fill="#ff7eb3" opacity="0.8" />
    </g>
    
    <!-- Floating Logo Mascot -->
    <g class="anim-float" transform="translate(10, 0)">
      <circle cx="120" cy="130" r="45" fill="none" stroke="rgba(56, 239, 125, 0.2)" stroke-width="8" stroke-dasharray="10 5" />
      <path d="M 120 110 L 135 120 L 135 138 L 120 150 L 105 138 L 105 120 Z" fill="none" stroke="#38ef7d" stroke-width="3" />
      <path d="M 120 110 L 120 148" stroke="#38ef7d" stroke-width="2" />
      <path d="M 120 120 C 126 118, 129 123, 120 130 Z" fill="#38ef7d" />
      <path d="M 120 128 C 114 126, 111 131, 120 138 Z" fill="#ff7eb3" />
    </g>

    <!-- Main Title -->
    <text x="495" y="125" font-family="'Outfit', 'Segoe UI', sans-serif" font-weight="900" font-size="52" fill="url(#titleGrad)" text-anchor="middle" class="anim-text" letter-spacing="4">EcoStaff</text>
    
    <!-- Subtitle -->
    <text x="495" y="165" font-family="'Segoe UI', sans-serif" font-weight="600" font-size="14" fill="#a0aec0" text-anchor="middle" letter-spacing="3">E - O U T S O U R C I N G   S Y S T E M</text>
    
    <!-- Status Pill -->
    <rect x="425" y="188" width="140" height="26" rx="13" fill="rgba(56, 239, 125, 0.1)" stroke="rgba(56, 239, 125, 0.3)" stroke-width="1" />
    <circle cx="440" cy="201" r="4" fill="#38ef7d" />
    <text x="498" y="206" font-family="'Segoe UI', sans-serif" font-weight="700" font-size="11" fill="#38ef7d" text-anchor="middle">DEPLOYED STATUS</text>
  </svg>
</div>

<br>

<p align="center">
  <a href="https://ecostaff.ct.ws" target="_blank">
    <img src="https://img.shields.io/badge/Live_Deploy-https%3A%2F%2Fecostaff.ct.ws-38ef7d?style=for-the-badge&logo=google-chrome&logoColor=white&labelColor=203a43" alt="Deployment Link">
  </a>
  <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Version">
  <img src="https://img.shields.io/badge/Livewire-3.x-4e56a6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire Version">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind Version">
</p>

<hr>

<h3 align="center">🌸 Konnichiwa! Selamat Datang di EcoStaff 🌸</h3>

<p align="center">
  <b>EcoStaff</b> adalah sistem manajemen <i>E-Outsourcing</i> modern yang dirancang untuk mengelola penjadwalan, perizinan, lembur, dan absensi karyawan secara real-time. Dengan antarmuka yang bersih, responsif, dan alur kerja yang terstruktur berdasarkan peran pengguna.
</p>

<div align="center">
  <p><i>"Managing employees has never been this aesthetic and efficient! Let's build a productive team together! ~ 💫"</i></p>
</div>

---

### 🚀 Link Deploys Aplikasi
Aplikasi ini telah berhasil dideploy dan dapat diakses secara publik melalui link berikut:
👉 **[https://ecostaff.ct.ws](https://ecostaff.ct.ws)**

> [!NOTE]
> Anda dapat mencoba login dengan role yang diinginkan untuk melihat fitur-fitur interaktif di dalamnya.

---

### 🛡️ Guild Ranks (Peran Sistem / Users)
Sistem ini menggunakan pembagian peran (roles) layaknya sistem faksi pada anime untuk menjaga keamanan dan efisiensi koordinasi:

| Ranks | Emoji | Role Name | Keterangan / Deskripsi Tugas |
| :--- | :---: | :--- | :--- |
| **Rank SS** | 👑 | **Super Admin** | Memegang kontrol tertinggi atas seluruh konfigurasi sistem, parameter global, dan hak akses. |
| **Rank S** | 🛠️ | **Admin Outsourcing** | Mengelola onboarding karyawan baru, validasi pengajuan akun, dan koordinasi dengan vendor luar. |
| **Rank A** | 👔 | **Kepala Departemen** | Mengatur plotting jadwal shift mingguan, menyetujui lembur (overtime), dan melacak laporan performa divisi. |
| **Rank B** | 📊 | **HR User** | Memverifikasi ajuan data karyawan, mengunduh rekap detail kehadiran, dan memantau kesehatan operasional. |
| **Rank C** | 🏃‍♂️ | **Karyawan Outsourcing** | Mengisi absensi harian, mengajukan izin/cuti, melihat jadwal shift pribadi, dan mengunduh jadwal dalam format PDF. |

---

### 🎮 Fitur Utama & Interaktif

Gunakan menu di bawah ini untuk melihat fitur-fitur seru dari setiap faksi:

<details>
<summary><b>👑 Fitur Super Admin (Rank SS)</b> [Klik untuk buka]</summary>
<br>

- **Dashboard Admin:** Statistik visual jumlah seluruh pengguna, status server, dan notifikasi log sistem.
- **Pengaturan Global:** Mengatur preferensi sistem, data departemen, dan aturan default outsourcing.
- **System Monitoring:** Mengawasi aktivitas user dan audit trail untuk keamanan data.
</details>

<details>
<summary><b>🛠️ Fitur Admin Outsourcing (Rank S)</b> [Klik untuk buka]</summary>
<br>

- **Kelola Karyawan:** Menambah, memperbarui, dan menonaktifkan data karyawan outsourcing yang masuk ke sistem.
- **Pengajuan Akun & Karyawan:** Melakukan approval terhadap pendaftaran akun baru secara instan.
- **Departemen API Integration:** Sinkronisasi data departemen dengan vendor penyedia tenaga kerja.
</details>

<details>
<summary><b>👔 Fitur Kepala Departemen (Rank A)</b> [Klik untuk buka]</summary>
<br>

- **Sistem Penjadwalan Cerdas:** Plotting jadwal shift karyawan secara interaktif dengan antarmuka kalender.
- **Template & Export Excel:** Unduh template jadwal kosong dan lakukan ekspor jadwal tim dengan sekali klik.
- **Approval Pengajuan:** Menyetujui atau menolak permohonan lembur dan perizinan dari anggota divisi.
- **Atur Lokasi Absensi:** Menentukan titik koordinat GPS kantor/lapangan untuk pembatasan absensi karyawan.
</details>

<details>
<summary><b>📊 Fitur HR User (Rank B)</b> [Klik untuk buka]</summary>
<br>

- **Dashboard HR Komprehensif:** Visualisasi metrik kehadiran karyawan, pengajuan lembur aktif, dan rasio kehadiran.
- **Rekapan Detail & Ajuan Data:** Memeriksa kesesuaian berkas identitas karyawan, NIP/NIM, dan validasi seeder.
- **Data Vendor & Karyawan API:** Sinkronisasi API untuk menyajikan laporan real-time ke pihak manajemen.
</details>

<details>
<summary><b>🏃‍♂️ Fitur Karyawan Outsourcing (Rank C)</b> [Klik untuk buka]</summary>
<br>

- **Interactive Personal Dashboard:** Informasi shift hari ini, jam masuk, jam pulang, dan sisa kuota cuti.
- **Pengajuan Perizinan & Lembur:** Form pengajuan izin sakit, cuti tahunan, atau klaim jam lembur ekstra.
- **Download Jadwal PDF:** Fitur unduh kalender shift kerja pribadi dalam format PDF siap cetak.
</details>

---

### 🎨 Tech Stack & Tools
Proyek ini dibangun menggunakan teknologi mutakhir untuk memastikan performa yang cepat dan pengalaman pengguna yang luar biasa:

* **Backend Framework:** [Laravel 10.x](https://laravel.com) - *Elegant PHP Web Framework* 🐘
* **Frontend Reactivity:** [Livewire v3](https://livewire.laravel.com) - *Dynamic Reactive Frontend for Laravel* ⚡
* **Styling System:** [Tailwind CSS v3](https://tailwindcss.com) - *Utility-First CSS Framework for Aesthetic Designs* 🎨
* **Database Engine:** MySQL - *Relational Database Storage* 💾
* **Bundler & Server:** [Vite](https://vitejs.dev) - *Super-fast frontend tooling* 🚀
* **PDF Generator:** [DomPDF](https://github.com/barryvdh/laravel-dompdf) - *Convert HTML views to neat PDF files* 📄

---

### ⚙️ Cara Menjalankan Proyek Secara Lokal

Ingin mencoba menjalankan di komputer Anda? Ikuti langkah-langkah mudah berikut:

1. **Clone Repositori**
   ```bash
   git clone https://github.com/username/E-outsourcing-trpl210.git
   cd E-outsourcing-trpl210
   ```

2. **Instal Dependensi PHP (Composer)**
   ```bash
   composer install
   ```

3. **Instal Dependensi Node (NPM)**
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**
   Salin berkas `.env.example` menjadi `.env` lalu sesuaikan kredensial database Anda.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Migrasi Database & Seeder**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Jalankan Aplikasi**
   Buka terminal terpisah untuk menjalankan server Laravel dan kompilasi aset Vite secara bersamaan:
   ```bash
   # Terminal 1: Laravel Server
   php artisan serve

   # Terminal 2: Vite Compiler
   npm run dev
   ```

---

<div align="center">
  <p>Dibuat dengan penuh dedikasi dan cinta 💚 oleh Tim EcoStaff.</p>
  <p><i>~ Arigatou Gozaimasu! ~</i></p>
  <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Hand%20gestures/Writing%20Hand.png" alt="Writing" width="40" />
</div>
