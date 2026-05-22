{{-- mengatur layout HTML dasar --}}
@extends('layouts.Auth')

{{-- mengatur style --}}
@push('styles')
    <link rel="stylesheet" href={{ asset('css/login.css') }}>
@endpush
    
{{-- mengatur isi dari body layout --}}
@section('content')
    <livewire:auth.login />
@endsection

{{-- mengatur script pada layout --}}
@push('scripts')
    <script src="{{ asset('js/login.js') }}"></script>
@endpush

