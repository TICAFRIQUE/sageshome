{{--
    Récapitulatif du séjour.
    @param \App\Models\Residence $residence
    @param string|\Carbon\Carbon $checkIn, $checkOut
    @param int $guests, $nights
    @param float $pricePerNight, $subtotal, $tax, $total
    @param string|null $reference
--}}
@php
    $in = \Carbon\Carbon::parse($checkIn);
    $out = \Carbon\Carbon::parse($checkOut);
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $location = collect([$residence->commune, $residence->ville])->filter()->map(fn ($s) => ucwords(strtolower($s)))->implode(', ');
@endphp

<aside class="summary-card">
    <div class="summary-residence">
        @if ($residence->primaryImage)
            <img src="{{ $residence->primaryImage->full_url }}" alt="" width="72" height="72" loading="lazy">
        @endif
        <div class="min-w-0">
            <div class="summary-title">{{ $residence->name }}</div>
            <div class="summary-sub">{{ $residence->type_display }}@if ($location) · {{ $location }}@endif</div>
            @if (!empty($reference))
                <div class="summary-sub">Réf. <strong>{{ $reference }}</strong></div>
            @endif
        </div>
    </div>

    <dl class="summary-list">
        <div><dt>Arrivée</dt><dd>{{ ucfirst($in->translatedFormat('D j M Y')) }}</dd></div>
        <div><dt>Départ</dt><dd>{{ ucfirst($out->translatedFormat('D j M Y')) }}</dd></div>
        <div><dt>Voyageurs</dt><dd>{{ $guests }}</dd></div>
        <div><dt>Durée</dt><dd>{{ $nights }} {{ $nights > 1 ? 'nuits' : 'nuit' }}</dd></div>
    </dl>

    <dl class="summary-list summary-prices">
        <div><dt>{{ $fmt($pricePerNight) }} FCFA × {{ $nights }} {{ $nights > 1 ? 'nuits' : 'nuit' }}</dt><dd>{{ $fmt($subtotal) }} FCFA</dd></div>
        @if ((float) $tax > 0)
            <div><dt>Taxes et frais</dt><dd>{{ $fmt($tax) }} FCFA</dd></div>
        @endif
        <div class="summary-total">
            <dt>Total</dt>
            <dd>{{ $fmt($total) }} FCFA<small>≈ {{ number_format(fcfa_to_eur($total), 2, ',', ' ') }} €</small></dd>
        </div>
    </dl>
</aside>
