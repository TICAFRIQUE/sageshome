/* Sages Home — interactions communes de l'administration */
(function () {
    'use strict';

    var doc = document;

    /* ---------- Barre de progression ---------- */
    var bar = doc.getElementById('sh-progress');
    function start() {
        if (!bar) return;
        bar.classList.remove('is-done');
        void bar.offsetWidth;
        bar.classList.add('is-loading');
    }
    function done() {
        if (!bar) return;
        bar.classList.remove('is-loading');
        bar.classList.add('is-done');
    }
    window.addEventListener('pageshow', function () {
        done();
        // Réactiver les boutons après un retour arrière
        doc.querySelectorAll('.btn.is-loading').forEach(function (b) { b.classList.remove('is-loading'); b.disabled = false; });
    });
    window.SH = window.SH || {};
    window.SH.progress = { start: start, done: done };

    doc.addEventListener('click', function (e) {
        var a = e.target.closest('a[href]');
        if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
        if ((a.target && a.target !== '_self') || a.hasAttribute('download') || a.dataset.bsToggle || a.dataset.noProgress !== undefined) return;
        var href = a.getAttribute('href');
        if (!href || href.charAt(0) === '#' || /^(javascript:|mailto:|tel:)/i.test(href)) return;
        if (/\/export(\?|$)/.test(href)) return; // téléchargements
        try {
            if (new URL(a.href, location.href).origin !== location.origin) return;
        } catch (err) { return; }
        start();
    });

    /* ---------- Lignes de tableau cliquables ---------- */
    doc.addEventListener('click', function (e) {
        var row = e.target.closest('tr[data-href]');
        if (!row || e.target.closest('a, button, input, select, label, form, .dropdown')) return;
        if (e.metaKey || e.ctrlKey) { window.open(row.dataset.href, '_blank'); return; }
        start();
        window.location.href = row.dataset.href;
    });

    /* ---------- Boîte de confirmation (SH.confirm) ---------- */
    var DANGER_WORDS = /supprim|annul|irr[ée]versible|d[ée]finitiv|retir|d[ée]sactiv|effac/i;

    /**
     * SH.confirm(message, options) -> Promise<boolean>
     * options : title, confirmText, icon, danger (bool)
     */
    function confirmDialog(message, opts) {
        opts = opts || {};
        var text = String(message || '').replace(/^[\s⚠️❌✅]+/u, '').trim();
        var danger = opts.danger !== undefined ? opts.danger : DANGER_WORDS.test(text);
        if (!window.Swal) return Promise.resolve(window.confirm(text));

        // Première phrase en titre, le reste en détail
        var title = opts.title, detail = text;
        if (!title) {
            var m = text.match(/^(.{8,120}?[?!.])\s+(.+)$/s);
            if (m) { title = m[1]; detail = m[2]; } else { title = text; detail = ''; }
        }

        return window.Swal.fire({
            title: title,
            text: detail || undefined,
            icon: opts.icon || (danger ? 'warning' : 'question'),
            showCancelButton: true,
            confirmButtonText: opts.confirmText || (danger ? 'Oui, confirmer' : 'Confirmer'),
            cancelButtonText: 'Annuler',
            reverseButtons: true,
            focusCancel: danger,
            buttonsStyling: false,
            customClass: {
                popup: 'sh-swal',
                confirmButton: 'btn ' + (danger ? 'btn-danger' : 'btn-primary') + ' px-4',
                cancelButton: 'btn btn-light px-4',
                actions: 'gap-2'
            },
            showClass: { popup: 'swal2-show' }
        }).then(function (r) { return r.isConfirmed; });
    }
    window.SH.confirm = confirmDialog;

    // Conversion automatique des confirm() natifs écrits en ligne :
    // onsubmit="return confirm('…')" / onclick="return confirm('…')"
    function upgradeInlineConfirms(root) {
        (root || doc).querySelectorAll('[onsubmit*="confirm("], [onclick*="confirm("]').forEach(function (el) {
            var attr = el.hasAttribute('onsubmit') ? 'onsubmit' : 'onclick';
            var code = el.getAttribute(attr) || '';
            var m = code.match(/^\s*return\s+confirm\(\s*(['"])([\s\S]*)\1\s*\)\s*;?\s*$/);
            if (!m) return;
            var message = m[2].replace(/\\(['"\\])/g, '$1').replace(/\\n/g, ' ');
            var form = attr === 'onsubmit' ? el : el.form;
            el.removeAttribute(attr);
            if (form && (el.tagName === 'FORM' || el.type === 'submit')) {
                form.dataset.confirm = message;
            } else {
                el.dataset.confirmClick = message; // lien ou bouton hors formulaire
            }
        });
    }
    upgradeInlineConfirms();
    window.SH.upgradeConfirms = upgradeInlineConfirms;

    doc.addEventListener('click', function (e) {
        var el = e.target.closest('[data-confirm-click]');
        if (!el || el.dataset.confirmed) return;
        e.preventDefault();
        e.stopImmediatePropagation();
        confirmDialog(el.dataset.confirmClick).then(function (ok) {
            if (!ok) return;
            el.dataset.confirmed = '1';
            el.click();
            setTimeout(function () { delete el.dataset.confirmed; }, 0);
        });
    }, true);

    /* ---------- Confirmation avant envoi de formulaire (data-confirm) ---------- */
    doc.addEventListener('submit', function (e) {
        var form = e.target;
        var message = form.dataset.confirm;
        if (message && !form.dataset.confirmed) {
            e.preventDefault();
            var ask = confirmDialog(message, {
                title: form.dataset.confirmTitle,
                confirmText: form.dataset.confirmButton,
                icon: form.dataset.confirmIcon,
                danger: form.dataset.confirmDanger !== undefined ? form.dataset.confirmDanger !== 'false' : undefined
            });
            var submitter = e.submitter && e.submitter.form === form ? e.submitter : null;
            ask.then(function (ok) {
                if (ok) {
                    form.dataset.confirmed = '1';
                    if (form.requestSubmit) form.requestSubmit(submitter || undefined); else form.submit();
                    // Redemander la prochaine fois (formulaires envoyés en AJAX)
                    setTimeout(function () { delete form.dataset.confirmed; }, 0);
                }
            });
            return;
        }

        // Validation HTML5 échouée : on ne bloque pas le bouton
        if (form.noValidate === false && form.checkValidity && !form.checkValidity()) return;
        if (form.classList.contains('needs-validation') && !form.checkValidity()) return;

        // Anti double-envoi + indicateur de chargement
        if (form.method && form.method.toLowerCase() === 'post') {
            var btn = e.submitter || form.querySelector('[type="submit"]');
            if (btn && !btn.classList.contains('dropdown-item')) {
                setTimeout(function () {
                    if (e.defaultPrevented) return;
                    btn.classList.add('is-loading');
                    btn.disabled = true;
                }, 0);
            }
        }
        if (form.target !== '_blank') {
            setTimeout(function () { if (!e.defaultPrevented) start(); }, 0);
        }
    }, true);

    /* ---------- Filtres : envoi automatique ---------- */
    doc.querySelectorAll('form[data-auto-submit]').forEach(function (form) {
        var timer;
        form.querySelectorAll('select, input[type="date"]').forEach(function (el) {
            el.addEventListener('change', function () { submitFilters(form); });
        });
        form.querySelectorAll('input[type="search"], input[data-debounce]').forEach(function (el) {
            el.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () { submitFilters(form); }, 500);
            });
        });
    });
    function submitFilters(form) {
        // Ne pas envoyer les champs vides (URL plus propres)
        form.querySelectorAll('input, select').forEach(function (el) {
            if (el.name && el.value === '') el.disabled = true;
        });
        start();
        form.submit();
    }

    /* ---------- Copier dans le presse-papiers (data-copy) ---------- */
    doc.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-copy]');
        if (!btn || !navigator.clipboard) return;
        var original = btn.innerHTML;
        navigator.clipboard.writeText(btn.dataset.copy).then(function () {
            btn.innerHTML = '<i class="ri-check-line text-success me-1"></i>Copié';
            setTimeout(function () { btn.innerHTML = original; }, 1800);
        });
    });

    /* ---------- Raccourci clavier : « / » pour rechercher ---------- */
    doc.addEventListener('keydown', function (e) {
        if (e.key !== '/' || /input|textarea|select/i.test(e.target.tagName) || e.target.isContentEditable) return;
        var search = doc.querySelector('[data-search-shortcut]');
        if (search) { e.preventDefault(); search.focus(); search.select(); }
    });

    /* ---------- Alertes : disparition automatique ---------- */
    setTimeout(function () {
        doc.querySelectorAll('.alert-dismissible.alert-success').forEach(function (el) {
            if (window.bootstrap) window.bootstrap.Alert.getOrCreateInstance(el).close();
        });
    }, 6000);

    /* ---------- Info-bulles ---------- */
    if (window.bootstrap) {
        doc.querySelectorAll('[data-bs-toggle="tooltip"], [title][data-tip]').forEach(function (el) {
            window.bootstrap.Tooltip.getOrCreateInstance(el);
        });
    }
})();
