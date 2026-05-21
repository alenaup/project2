<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Services\AuthService;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public $showPassword = false;

    public function togglePassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function login(AuthService $authService)
    {
        // 1. Validasi
        $this->validate($this->rules(), $this->messages());

        // 2. Panggil service
        $result = $authService->login(trim($this->email), $this->password);

        // 3. Kalau gagal
        if (!$result['success']) {
            $this->addError('login', $result['message']);
            return;
        }

        // 4. Kalau berhasil → kirim event ke frontend (animasi)
        $this->dispatch('login-success', url: $result['redirect']);
    }

    public function updated($propertyName)
    {
        if (! array_key_exists($propertyName, $this->rules())) {
            return;
        }

        $this->validateOnly($propertyName, $this->rules(), $this->messages());
    }

    protected function rules(): array
    {
        return [
            'email' => 'required|email:rfc',
            'password' => 'required'
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi'
        ];
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
