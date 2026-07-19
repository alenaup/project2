<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class Login extends Component
{
    // * inisialisasi property unntuk menampung data
    public string $email = '';
    public string $password = '';

    /* menginisiasi Service Auth dengan menamakan $authService */
    // fungsi ini melakukan pembatasan percobaan login, validasi input, memanggil service untuk login, dan mengirimkan event ke frontend
    // input berupa object AuthService, output berupa void yang mengirimkan event ke frontend
    public function login(AuthService $authService)
    {
        // * untuk membatasi percobaan login, dengan menggunakan fitur RateLimiter bawaan laravel
        // * key ini akan menyimpan data percobaan login berdasarkan IP address pengguna
        $key = 'login-attempts:'.strtolower(trim($this->email)).'|'.request()->ip();

        // 1. Validasi dengan memanggil fungsi rules dan massege yang sidah dibuat
        $this->validate($this->rules(), $this->messages());

        // * jika percobaan login melebihi batas yang ditentukan, maka akan mengirimkan pesan error dan menghentikan proses login
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('login', "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.");

            return;
        }

        RateLimiter::hit($key, 60);

        // 2. Panggil service dan eksekusi function login
        $result = $authService->login(trim($this->email), $this->password);

        // 3. mengambil return dari service, jika gagal → tampilkan error, jika berhasil → kirim event ke frontend
        if (! $result['success']) {
            /* membersihkan rate limiter setelah login supaya tidak terblokir saat  */
            $this->addError('login', $result['message']);
            return;
        }

        RateLimiter::clear($key);

        // 4. Kalau berhasil → kirim event ke frontend (animasi)
        // pakai event karena kita tidak ingin melakukan redirect secara langsung, tapi ingin menampilkan animasi terlebih dahulu
        $this->dispatch('login-success', url: $result['redirect']);

    }

    /* fungsi untuk melakukan validasi ketika data diubah */
    public function updated(string $propertyName)
    {
        $this->validateOnly(
            $propertyName,
            $this->rules(),
            $this->messages()
        );
    }

    /* fungsi untuk meneentukan rules dari validasi */
    protected function rules(): array
    {
        // 1. rules untuk validasi
        return [
            'email' => 'required|email:rfc',
            'password' => 'required',
        ];
    }

    /* pesan yang dikirimkan berdasarkan rules */
    protected function messages(): array
    {
        // 1. pesan yang dikirimkan
        return [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ];
    }
}
