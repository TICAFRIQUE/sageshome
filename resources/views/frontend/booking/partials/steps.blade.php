{{-- Indicateur d'étapes compact. @param int $current (1 à 3) --}}
@php
    $steps = [1 => 'Vos informations', 2 => 'Paiement', 3 => 'Confirmation'];
@endphp
<ol class="booking-steps" aria-label="Étapes de la réservation">
    @foreach ($steps as $n => $label)
        <li @class(['is-done' => $n < $current, 'is-current' => $n === $current]) @if ($n === $current) aria-current="step" @endif>
            <span class="num">
                @if ($n < $current)
                    <i class="fas fa-check" aria-hidden="true"></i>
                @else
                    {{ $n }}
                @endif
            </span>
            <span class="label">{{ $label }}</span>
        </li>
    @endforeach
</ol>
