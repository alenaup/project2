## Code Review & Audit Rules (Standardized)
Saat diminta untuk melakukan pemeriksaan halaman atau modul (seperti Super Admin Dashboard), selalu lakukan audit komprehensif tanpa mengubah kode secara langsung, dengan mengecek aspek-aspek berikut:
1. **Performa & Queries (Lag Issue):**
   - Periksa penggunaan query Eloquent. Pastikan tidak ada N+1 query.
   - Periksa apakah operasi agregat (seperti `.count()`) menggunakan query builder (misal `->count()`) dan bukan di-load ke memory collection (`->get()->count()`).
2. **Keamanan File Upload:**
   - Periksa validasi ukuran maksimal file (mimes, max).
   - Pastikan tidak ada celah di mana client (terutama lewat payload Livewire) dapat memanipulasi class yang diinstansiasi (Arbitrary Class Instantiation vulnerability).
3. **Logika & Bug Tersembunyi:**
   - Periksa edge-case: apakah ada field yang terlewat (misal: Kepala Departemen butuh departemen_id tapi tidak di-import dari Excel).
   - Pastikan perlindungan terhadap aksi destruktif (misal: mencegah user Super Admin menghapus akunnya sendiri).
4. **Peluang Pengembangan (Improvements):**
   - Berikan rekomendasi untuk scaling, UI/UX handling (seperti loading indicator), dan keamanan ekstra.

