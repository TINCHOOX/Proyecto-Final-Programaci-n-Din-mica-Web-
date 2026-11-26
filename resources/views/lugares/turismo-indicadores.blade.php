@if ($lugar->actividad && $lugar->indicadores)
    <dl class="indicadores-turismo">
        @foreach ($lugar->actividad->etiquetasIndicadores() as $clave => $etiqueta)
            @if (isset($lugar->indicadores[$clave]))
                <div>
                    <dt>{{ $etiqueta }}</dt>
                    <dd>{{ $lugar->indicadores[$clave] }}</dd>
                </div>
            @endif
        @endforeach
    </dl>
@endif
