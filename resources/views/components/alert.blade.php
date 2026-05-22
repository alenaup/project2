@props([ 'jenis_pesan', 'text' ])
{{-- Container toast dikelola oleh JS (alert.js) --}}
<div id="toast-container" role="region" aria-live="polite" aria-label="Notifikasi"></div>

{{-- Flash session support (Laravel) --}}
@if($jenis_pesan === 'success')
    <div id="flash-message" data-message="{{ session('success') }}" data-type="success" hidden></div>
@elseif($jenis_pesan === 'error')
    <div id="flash-message" data-message="{{ session('error') }}" data-type="error" hidden>{{ $slot }}</div>
@elseif(session('warning'))
    <div id="flash-message" data-message="{{ session('warning') }}" data-type="warning" hidden></div>
@elseif(session('info'))
    <div id="flash-message" data-message="{{ session('info') }}" data-type="info" hidden></div>
@endif


