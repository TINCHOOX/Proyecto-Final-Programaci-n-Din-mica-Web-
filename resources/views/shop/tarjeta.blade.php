<article class="tienda-tarjeta">
    <div class="tienda-arte">
        @if ($dinosaur->image_path)
            <img src="{{ asset($dinosaur->image_path) }}" alt="" loading="lazy">
        @else
            <span>Ilustración pendiente</span>
        @endif
    </div>
    <h2>{{ $dinosaur->name }}</h2>
    @if (!$coleccion)
        <p>{{ $dinosaur->is_starter ? 'Disponible desde el inicio' : $dinosaur->price.' puntos' }}</p>
    @endif
    @if ($coleccion && $dinosaur->id === $equippedId)
        <span class="boton boton-claro">En uso</span>
    @elseif (!$coleccion && in_array($dinosaur->id, $ownedIds, true))
        <a class="boton boton-claro" href="{{ route('shop.collection') }}">En tu colección</a>
    @elseif ($coleccion)
        <form action="{{ route('shop.equip', $dinosaur) }}" method="post">
            @csrf
            <button class="boton" type="submit">Equipar en mi perfil</button>
        </form>
    @elseif ($points < $dinosaur->price)
        <button class="boton" type="button" disabled>Puntos insuficientes</button>
    @else
        <a class="boton"
           href="{{ route('shop.index', ['canjear' => $dinosaur->slug]) }}#confirmar-canje"
           data-canje-abrir
           data-canje-nombre="{{ $dinosaur->name }}"
           data-canje-precio="{{ $dinosaur->price }}"
           data-canje-imagen="{{ $dinosaur->image_path ? asset($dinosaur->image_path) : '' }}"
           data-canje-destino="{{ route('shop.redeem', $dinosaur) }}">
            Canjear por {{ $dinosaur->price }} puntos
        </a>
    @endif
</article>
