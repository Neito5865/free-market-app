@if (session('success'))
    <div class="message__success">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="message__error">
        {{ session('error') }}
    </div>
@endif
@if (session('info'))
    <div class="message__info">
        {{ session('info') }}
    </div>
@endif
