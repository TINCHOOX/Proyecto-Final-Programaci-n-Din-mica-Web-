@extends('layouts.app')

@section('titulo', 'Iniciar sesión · DinoMaps')

@section('contenido')
<main class="auth">
    <a class="boton boton-claro" href="{{ route('inicio') }}">← Volver</a>
    <h1>Iniciar sesión</h1>
    <form method="post" action="{{ route('login.store') }}">
        @csrf
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" maxlength="255" required>
        @error('email') <p class="error" role="alert">{{ $message }}</p> @enderror
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        @error('password') <p class="error" role="alert">{{ $message }}</p> @enderror
        <label class="checkbox"><input name="remember" type="checkbox" value="1" @checked(old('remember'))> Mantener la sesión</label>
        <button class="boton boton-claro" type="submit">Iniciar sesión</button>
    </form>
    <a href="{{ route('register') }}">Crear cuenta</a>
</main>
@endsection
