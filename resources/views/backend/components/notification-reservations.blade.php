<div class="dropdown ms-1 header-item">
    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle position-relative"
        id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Nouvelles réservations">
        <i class="bx bx-bell fs-22"></i>
        <span id="notif-count-reservations"
            class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger d-none">0</span>
    </button>

    <div class="dropdown-menu dropdown-menu-end dropdown-menu-lg p-0" aria-labelledby="notifDropdown">
        <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0">Nouvelles réservations</h6>
            <span class="text-muted small">Mise à jour auto</span>
        </div>
        <div id="notif-list-reservations" style="max-height:320px; overflow:auto;">
            <div class="p-4 text-center text-muted small">Aucune nouvelle réservation</div>
        </div>
        <div class="p-2 border-top text-center">
            <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="btn btn-sm btn-soft-primary w-100">
                Voir les réservations en attente
            </a>
        </div>
    </div>
</div>

<script>
    (function () {
        var endpoint = "{{ route('admin.bookings.new-bookings') }}";
        var sound = "{{ asset('audio/notify_bell.wav') }}";
        var known = new Set();
        var first = true;
        var timer = null;

        function esc(s) {
            return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }

        function render(data) {
            var badge = document.getElementById('notif-count-reservations');
            var list = document.getElementById('notif-list-reservations');
            if (!badge || !list) return;

            var count = data.count || 0;
            badge.textContent = count;
            badge.classList.toggle('d-none', count === 0);

            if (!count) {
                list.innerHTML = '<div class="p-4 text-center text-muted small">Aucune nouvelle réservation</div>';
                return;
            }

            list.innerHTML = (data.bookings || []).map(function (b) {
                return '<a class="dropdown-item d-flex gap-2 py-2 px-3 border-bottom" href="' + esc(b.show_url) + '">' +
                    '<span class="sh-avatar"><i class="ri-calendar-check-line"></i></span>' +
                    '<span class="flex-grow-1 min-w-0">' +
                    '<span class="d-block fw-semibold small text-truncate">' + esc(b.reference) + ' · ' + esc(b.guest_name) + '</span>' +
                    '<span class="d-block small text-muted text-truncate">' + esc(b.residence_name) + ' · ' + esc(b.created_at) + '</span>' +
                    '<span class="d-block small fw-semibold text-success">' + esc(b.total_amount) + '</span>' +
                    '</span></a>';
            }).join('');

            // Son uniquement pour les réservations jamais vues
            var hasNew = false;
            (data.bookings || []).forEach(function (b) {
                if (!known.has(b.reference)) { hasNew = true; known.add(b.reference); }
            });
            if (hasNew && !first) {
                try { new Audio(sound).play().catch(function () {}); } catch (e) {}
            }
        }

        function poll() {
            if (document.hidden) return;
            fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (data) { if (data) { render(data); first = false; } })
                .catch(function () {});
        }

        poll();
        timer = setInterval(poll, 20000);
        document.addEventListener('visibilitychange', function () { if (!document.hidden) poll(); });
    })();
</script>
