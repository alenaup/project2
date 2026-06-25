@extends('layouts.admin-outsourcing')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 p-4 md:p-8 font-sans relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-green-50/50 blur-3xl pointer-events-none z-0"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-50/50 blur-3xl pointer-events-none z-0"></div>

    <div class="relative z-10 max-w-7xl mx-auto">
        @livewire('admin-outsourcing.pengajuan-karyawan')
    </div>
</div>

<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
