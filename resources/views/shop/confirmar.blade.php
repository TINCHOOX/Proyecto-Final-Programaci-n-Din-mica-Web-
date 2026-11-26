<h2 id="{{ $tituloCanje }}">Confirmar canje</h2>
<div class="tienda-canje-resumen">
    <div class="tienda-canje-arte">
        <img data-canje-vista-imagen
             @if ($producto?->image_path) src="{{ asset($producto->image_path) }}" @else hidden @endif
             alt="">
        <span data-canje-sin-imagen @if ($producto?->image_path) hidden @endif>Ilustración pendiente</span>
    </div>
    <div>
        <p class="tienda-canje-nombre" data-canje-vista-nombre>{{ $producto?->name }}</p>
        <p class="tienda-canje-costo" data-canje-vista-precio>
            @if ($producto) Costo: {{ $producto->price }} puntos @endif
        </p>
    </div>
</div>
<div class="tienda-canje-acciones">
    @if ($esDialogo)
        <form method="dialog">
            <button class="boton boton-claro" type="submit" autofocus>Cancelar</button>
        </form>
    @else
        <a class="boton boton-claro" href="{{ route('shop.index') }}">Cancelar</a>
    @endif
    <form method="post" data-canje-formulario @if ($producto) action="{{ route('shop.redeem', $producto) }}" @endif>
        @csrf
        <button class="boton" type="submit">Confirmar canje</button>
    </form>
</div>
