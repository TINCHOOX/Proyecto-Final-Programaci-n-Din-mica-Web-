@if (session('status')) <p class="mensaje-cuenta" role="status">{{ session('status') }}</p> @endif
@foreach ($errors->all() as $error) <p class="error" role="alert">{{ $error }}</p> @endforeach
