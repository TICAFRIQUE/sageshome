<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />



<div class="dropdown ms-2 header-item">
    <button type="button" class="btn btn-icon btn-topbar position-relative" id="notifDropdown" data-bs-toggle="dropdown">
        <i class="bx bx-bell fs-20"></i>
        <span id="notif-count-reservations"
            class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle p-1">...</span>
    </button>

    <div class="dropdown-menu dropdown-menu-end dropdown-menu-lg p-0" aria-labelledby="notifDropdown">
        <div class="p-3 border-bottom">
            <h6 class="m-0">Notifications</h6>
        </div>
        <div id="notif-list-reservations" style="max-height:300px; overflow:auto;">
            <div class="p-3 text-center text-muted">Chargement...</div>
        </div>
        <div class="dropdown-divider"></div>
        <div class="p-2 text-center">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-link">Voir toutes</a>
        </div>
    </div>
</div>



<script>
    function checkNewReservations() {
        $.ajax({
            url: "{{ route('admin.bookings.new-bookings') }}",
            method: "GET",
            success: function(data) {
                if (data.count > 0) {
                    // Mettre à jour le badge
                    $("#notif-count-reservations").text(data.count || 0);
                    // Vider et reconstruire la liste
                    const listContainer = $("#notif-list-reservations");
                    listContainer.empty();

                    data.bookings.forEach(function(booking) {
                        const bookingHtml = `
                        <a class="dropdown-item d-flex align-items-start" href="${booking.show_url || '#'}">
                            <div class="me-2"><i class="bi bi-clock-history text-success"></i></div>
                            <div>
                                <div class="small fw-semibold">${booking.reference} - ${booking.guest_name}</div>
                                <div class="small text-muted">${booking.created_at}</div>
                                <div class="small text-success fw-bold">${(booking.total_amount || 0).toLocaleString('fr-FR')} FCFA</div>
                            </div>
                        </a>
                    `;
                        listContainer.append(bookingHtml);
                    });

                    // Jouer le son directement
                    const alertSound = new Audio("{{ asset('audio/notify_bell.wav') }}");
                    alertSound.play();
                } else {
                    // Si aucune nouvelle réservation, masquer le badge
                    $("#notif-count-reservations").text('0');
                    $("#notif-list-reservations").html(
                        '<div class="p-3 text-center text-muted">Aucune nouvelle réservation</div>');
                }
            },
            error: function(xhr) {
                console.error("Erreur lors de la récupération des réservations.");
            },
        });
    }

    // Vérifier les nouvelles réservations toutes les 10 secondes
    setInterval(checkNewReservations, 10000);
</script>
















{{-- <script>
(function(){
    const endpoint = "{{ route('commandes.newOrderCount') }}";
    const intervalMs = 10000; // 10s

    function updateNotifications(data) {
        const badge = document.getElementById('notif-count-reservations');
        const list = document.getElementById('notif-list-reservations');

        if (badge) {
            const prevCount = parseInt(badge.textContent || '0', 10);
            badge.textContent = data.count || 0;
            badge.style.display = (data.count && data.count > 0) ? 'inline-block' : 'none';

            if (data.count > prevCount) {
                badge.classList.add('animate__animated', 'animate__bounceIn');
                setTimeout(() => badge.classList.remove('animate__animated', 'animate__bounceIn'), 1000);
            }
        }

        if (list) {
            list.innerHTML = '';
            (data.orders || []).forEach(o => {
                const a = document.createElement('a');
                a.className = 'dropdown-item d-flex align-items-start';
                a.href = o.url || '#';
                a.innerHTML = `
                    <div class="me-2"><i class="bi bi-bag-check text-success"></i></div>
                    <div>
                        <div class="small fw-semibold">Commande ${o.code}</div>
                        <div class="small text-muted">${o.created_at}</div>
                    </div>
                `;
                list.appendChild(a);
            });

            if ((data.orders || []).length === 0) {
                const empty = document.createElement('div');
                empty.className = 'p-3 text-center text-muted';
                empty.textContent = 'Aucune notification';
                list.appendChild(empty);
            }
        }
        
    }

    async function poll() {
        try {
            const res = await fetch(endpoint, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (!res.ok) return;
            const data = await res.json();
            updateNotifications(data);
        } catch (err) {
            console.error('Polling error', err);
        }
    }

    poll();
    setInterval(poll, intervalMs);
})();
</script> --}}
