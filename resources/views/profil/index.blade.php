@php
    $layout = 'layouts.karyawanOutsourcing';
    if (Auth::check()) {
        $role = Auth::user()->role->value;
        if ($role === 'admin_outsourcing') $layout = 'layouts.admin-outsourcing';
        elseif ($role === 'kepala_departemen') $layout = 'layouts.kepala-departement';
        // Note: Untuk HR dan Super Admin jika belum menggunakan system extends layout,
        // profile page ini mungkin butuh penyesuaian untuk role tersebut.
    }
@endphp

@extends($layout)

@section('content')
    <!-- LIVEWIRE COMPONENT -->
    <livewire:profil.profil-index />
@endsection
