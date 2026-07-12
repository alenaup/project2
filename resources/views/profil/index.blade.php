@php
    $layout = 'layouts.karyawanOutsourcing';
    if (Auth::check()) {
        $role = Auth::user()->role->value;
        if ($role === 'admin_outsourcing') $layout = 'layouts.admin-outsourcing';
        elseif ($role === 'kepala_departemen') $layout = 'layouts.kepala-departement';
        elseif ($role === 'hr') $layout = 'layouts.hr';
    }
@endphp

@extends($layout)

@section('content')
    <!-- LIVEWIRE COMPONENT -->
    <livewire:profil.profil-index />
@endsection
