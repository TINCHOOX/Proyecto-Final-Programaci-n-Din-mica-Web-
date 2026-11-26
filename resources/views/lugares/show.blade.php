@extends('layouts.app')

@section('titulo', $lugar->nombre.' · DinoMaps')

@push('head')
@if ($totalResenas)
<script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
<script defer src="{{ asset('js/estadisticas.js') }}"></script>
@endif
@endpush

@section('contenido')
<main class="contenido-catalogo ficha-detalle">
    <a class="regreso" href="{{ route('lugares.index', array_filter(['categoria' => $lugar->categoria->slug, 'actividad' => $lugar->actividad?->value])) }}">← {{ $lugar->categoria->nombre }}</a>
    <article class="ficha">
        <p>{{ $lugar->tipo }}</p>
        <h1>{{ $lugar->nombre }}</h1>
        <p>{{ $lugar->direccion }} · Neuquén capital</p>
        @if ($lugar->categoria->slug === 'turismo')
            @include('lugares.turismo-fotos')
            @if ($lugar->descripcion)
                <p>{{ $lugar->descripcion }}</p>
            @endif
            @include('lugares.turismo-indicadores')
        @endif
        <div class="acciones">
            <a class="boton" href="https://www.google.com/maps/dir/?api=1&amp;destination={{ urlencode($lugar->nombre.' '.$lugar->direccion.' Neuquén') }}" target="_blank" rel="noopener noreferrer">Cómo llegar</a>
            @if ($lugar->latitud !== null && $lugar->longitud !== null && config('services.geoapify.key'))
                <a class="boton boton-claro" href="{{ route('recorrido.mapa', $lugar) }}">Ver recorrido en DinoMaps</a>
            @endif
            @if ($lugar->categoria->slug === 'turismo')
                @include('lugares.turismo-en-mapa')
            @endif
            <a href="{{ $lugar->fuente }}" target="_blank" rel="noopener noreferrer">Consultar fuente</a>
        </div>
    </article>
    <div class="ficha-columnas">
        <section class="ficha" id="resenas">
            <h2>Reseñas</h2>
            @if (session('status')) <p class="mensaje-cuenta" role="status">{{ session('status') }}</p> @endif
            @foreach ($errors->all() as $error) <p class="error" role="alert">{{ $error }}</p> @endforeach
            @forelse ($reviews as $review)
                <article class="resena">
                    <strong>{{ $review->user->name }}</strong>
                    <span aria-label="{{ $review->rating }} de 5 estrellas">{{ $review->rating }} ★</span>
                    <p>{{ $review->comment }}</p>
                    <small>{{ $review->votes_count }} {{ $review->votes_count === 1 ? 'valoración útil' : 'valoraciones útiles' }}</small>
                    @if ($viewer && $review->user_id !== $viewer->id && !$review->viewer_voted)
                        <form action="{{ route('reviews.votes.store', $review) }}" method="post">
                            @csrf
                            <button class="boton boton-claro" type="submit">Me resultó útil</button>
                        </form>
                    @endif
                </article>
            @empty
                <p>Todavía no hay reseñas.</p>
            @endforelse
            {{ $reviews->links('lugares.paginas') }}
            @auth
                @unless ($yaReseno)
                    <form class="form-resena" action="{{ route('reviews.store', $lugar) }}" method="post">
                        @csrf
                        <h3>Escribí una reseña</h3>
                        <label for="calificacion">Calificación</label>
                        <select id="calificacion" name="rating" required>
                            @for ($estrella = 5; $estrella >= 1; $estrella--)
                                <option value="{{ $estrella }}" @selected((int) old('rating', 5) === $estrella)>{{ $estrella }} / 5</option>
                            @endfor
                        </select>
                        <label for="comentario">Comentario</label>
                        <textarea id="comentario" name="comment" rows="3" maxlength="500" required>{{ old('comment') }}</textarea>
                        <button class="boton" type="submit">Publicar reseña</button>
                    </form>
                @endunless
            @else
                <a class="boton" href="{{ route('login') }}">Iniciar sesión para participar</a>
            @endauth
        </section>
        @if ($totalResenas)
            <section class="ficha estadisticas">
                <h2>Calificaciones</h2>
                <p>{{ number_format($promedio, 1, ',', '.') }} de 5 · {{ $totalResenas }} {{ $totalResenas === 1 ? 'reseña' : 'reseñas' }}</p>
                <canvas id="grafico-resenas" data-valores="{{ json_encode(array_values($distribucion)) }}" aria-hidden="true"></canvas>
                <table>
                    <caption>Distribución de calificaciones</caption>
                    <thead><tr><th scope="col">Estrellas</th><th scope="col">Reseñas</th></tr></thead>
                    <tbody>
                    @foreach ($distribucion as $estrellas => $cantidad)
                        <tr><th scope="row">{{ $estrellas }} ★</th><td>{{ $cantidad }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </section>
        @endif
    </div>
</main>
@endsection
