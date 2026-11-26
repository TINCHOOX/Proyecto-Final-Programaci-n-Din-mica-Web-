@extends('layouts.app')

@section('titulo', 'Crear cuenta · DinoMaps')

@section('contenido')
<main class="auth">
    <a class="boton boton-claro" href="{{ route('inicio') }}">← Volver</a>
    <h1>Crear cuenta</h1>
    <form method="post" action="{{ route('register.store') }}">
        @csrf
        <label for="name">Nombre</label>
        <input id="name" name="name" autocomplete="name" value="{{ old('name') }}" maxlength="255" required>
        @error('name') <p class="error" role="alert">{{ $message }}</p> @enderror
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" maxlength="255" required>
        @error('email') <p class="error" role="alert">{{ $message }}</p> @enderror
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
        @error('password') <p class="error" role="alert">{{ $message }}</p> @enderror
        <label for="password_confirmation">Repetí la contraseña</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
        <button class="boton boton-claro" type="submit">Crear cuenta</button>
    </form>
    <a href="{{ route('login') }}">Ya tengo cuenta</a>
</main>
@endsection
