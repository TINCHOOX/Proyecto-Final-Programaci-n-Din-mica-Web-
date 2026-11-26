@if ($lugar->reviews_avg_rating !== null)
    @php($promedioTarjeta = round((float) $lugar->reviews_avg_rating, 1))
    <span class="calificacion-lugar" role="img" aria-label="{{ number_format($promedioTarjeta, 1, ',', '') }} de 5 estrellas">
        <span class="estrellas-lugar" style="--estrellas-llenas: {{ $promedioTarjeta * 20 }}%" aria-hidden="true">★★★★★</span>
        <span aria-hidden="true">{{ number_format($promedioTarjeta, 1, ',', '') }}</span>
    </span>
@endif
