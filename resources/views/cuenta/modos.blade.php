<div class="modos-color" data-modos-color>
    @foreach (\App\Models\User::COLOR_MODES as $valor => $etiqueta)
        @php($activo = (auth()->user()?->color_mode ?? 'estandar') === $valor)
        <form class="opcion-cuenta formulario-color" action="{{ route('cuenta.accesibilidad') }}" method="post">
            @csrf
            @method('PATCH')
            <strong>{{ $etiqueta }}</strong>
            <button class="valor-cuenta" name="color_mode" value="{{ $valor }}" type="submit"
                aria-label="{{ $etiqueta }}" aria-pressed="{{ $activo ? 'true' : 'false' }}"
                @guest disabled @endguest>{{ $activo ? 'Activo' : 'Elegir' }}</button>
        </form>
    @endforeach
    <p class="modo-estado" data-modo-estado role="status"></p>
</div>
