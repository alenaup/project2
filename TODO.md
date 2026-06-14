# TODO

- [x] Analisis penyebab NIP/NIM tidak muncul di Dashboard HR
- [x] Cek view Livewire `resources/views/livewire/hr/dashboard-h-r.blade.php`
- [x] Cek model `app/Livewire/HR/DashboardHR.php` dan relasi `app/Models/Lembur.php`
- [x] Cek model `app/Models/User.php` dan field `nip`
- [x] Cek seeder `database/seeders/UserSeeder.php` dan `database/seeders/LemburSeeder.php`
- [ ] Update seeder `UserSeeder` agar role Karyawan mengisi `nip` (random 6 digit / string), bukan default 0
- [ ] (Opsional) Update `LemburSeeder` jika ternyata relasi karyawan salah (karyawan_id bukan user yang nip-nya terisi)
- [ ] Jalankan `php artisan migrate:fresh --seed` lalu cek kembali Dashboard HR

