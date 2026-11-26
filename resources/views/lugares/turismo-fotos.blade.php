@php($fotosTurismo = config('dinomaps.lugares.'.$lugar->slug, []))
@if ($fotosTurismo)
    <div class="fotos-turismo">
        @foreach (array_slice($fotosTurismo, 0, ($soloPortadaTurismo ?? false) ? 1 : 3) as $foto)
            <img src="{{ asset('images/dinomaps/lugares/'.$foto.'.webp') }}" alt="" loading="lazy">
        @endforeach
    </div>
@else
    <div class="foto-turismo-vacia" aria-label="Sin fotos disponibles" role="img">
        <img src="{{ asset('images/dinomaps/categorias/turismo.svg') }}" alt="">
    </div>
@endif
