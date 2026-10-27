/* Sages Home — interactions globales du front-office */
(function () {
    'use strict';

    var doc = document;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Header : ombre au scroll + bouton retour en haut ---------- */
    var header = doc.querySelector('.site-header');
    var backToTop = doc.getElementById('backToTop');
    var ticking = false;

    function onScroll() {
        var y = window.pageYOffset || doc.documentElement.scrollTop;
        if (header) header.classList.toggle('is-scrolled', y > 8);
        if (backToTop) backToTop.classList.toggle('show', y > 500);
        ticking = false;
    }
    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(onScroll);
            ticking = true;
        }
    }, { passive: true });
    onScroll();

    if (backToTop) {
        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
        });
    }

    /* ---------- Barre de progression pendant la navigation ---------- */
    var progress = doc.getElementById('page-progress');

    function startProgress() {
        if (!progress) return;
        progress.classList.remove('is-done');
        // force reflow pour relancer la transition
        void progress.offsetWidth;
        progress.classList.add('is-loading');
    }
    function stopProgress() {
        if (!progress) return;
        progress.classList.remove('is-loading');
        progress.classList.add('is-done');
    }

    doc.addEventListener('click', function (e) {
        var link = e.target.closest('a[href]');
        if (!link || e.defaultPrevented) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
        if (link.target && link.target !== '_self') return;
        if (link.hasAttribute('download') || link.dataset.bsToggle) return;
        var href = link.getAttribute('href');
        if (!href || href.charAt(0) === '#' || /^(mailto:|tel:|javascript:|https?:\/\/wa\.me)/i.test(href)) return;
        var url;
        try { url = new URL(link.href, window.location.href); } catch (err) { return; }
        if (url.origin !== window.location.origin) return;
        if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return;
        startProgress();
    });

    doc.addEventListener('submit', function (e) {
        if (!e.defaultPrevented && e.target.method && e.target.target !== '_blank') {
            // laisser le temps aux autres gestionnaires d'annuler l'envoi
            setTimeout(function () { if (!e.defaultPrevented) startProgress(); }, 0);
        }
    });

    // Retour arrière (bfcache) : on nettoie la barre
    window.addEventListener('pageshow', stopProgress);

    /* ---------- Images : fondu à l'affichage ---------- */
    function markLoaded(img) { img.classList.add('is-loaded'); }
    function watchImages(root) {
        (root || doc).querySelectorAll('img.img-fade:not(.is-loaded)').forEach(function (img) {
            if (img.complete && img.naturalWidth !== 0) {
                markLoaded(img);
            } else {
                img.addEventListener('load', function () { markLoaded(img); }, { once: true });
                img.addEventListener('error', function () { markLoaded(img); }, { once: true });
            }
        });
    }
    watchImages();
    window.SagesHome = window.SagesHome || {};
    window.SagesHome.watchImages = watchImages;

    /* ---------- Apparition au scroll ---------- */
    var revealEls = doc.querySelectorAll('[data-reveal]');
    if (!('IntersectionObserver' in window) || reduceMotion) {
        revealEls.forEach(function (el) { el.classList.add('is-visible'); });
    } else {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        revealEls.forEach(function (el) { io.observe(el); });
    }

    /* ---------- Formulaires de recherche : dates + communes ---------- */
    var villesCommunes = window.SAGES_VILLES_COMMUNES || {};

    function toISO(d) {
        var m = d.getMonth() + 1, day = d.getDate();
        return d.getFullYear() + '-' + (m < 10 ? '0' : '') + m + '-' + (day < 10 ? '0' : '') + day;
    }

    doc.querySelectorAll('[data-search-form]').forEach(function (form) {
        var checkIn = form.querySelector('[name="check_in"]');
        var checkOut = form.querySelector('[name="check_out"]');
        var ville = form.querySelector('[name="ville"]');
        var commune = form.querySelector('[name="commune"]');
        var today = toISO(new Date());

        if (checkIn) {
            if (!checkIn.min) checkIn.min = today;
            var syncCheckout = function () {
                if (!checkIn.value || !checkOut) return;
                var next = new Date(checkIn.value + 'T00:00:00');
                next.setDate(next.getDate() + 1);
                checkOut.min = toISO(next);
                if (!checkOut.value || checkOut.value <= checkIn.value) {
                    checkOut.value = toISO(next);
                }
            };
            checkIn.addEventListener('change', syncCheckout);
            if (checkIn.value && checkOut) {
                var n = new Date(checkIn.value + 'T00:00:00');
                n.setDate(n.getDate() + 1);
                checkOut.min = toISO(n);
            }
        }
        if (checkOut && !checkOut.min) checkOut.min = today;

        if (ville && commune) {
            var selected = commune.dataset.selected || '';
            var addOption = function (parent, value, current) {
                var opt = doc.createElement('option');
                opt.value = value;
                opt.textContent = niceName(value);
                if (value === current) opt.selected = true;
                parent.appendChild(opt);
            };
            var fill = function () {
                var current = commune.value || selected;
                var villes = Object.keys(villesCommunes).filter(function (v) { return (villesCommunes[v] || []).length; });
                commune.innerHTML = '<option value="">Toutes</option>';

                if (ville.value) {
                    (villesCommunes[ville.value] || []).forEach(function (c) { addOption(commune, c, current); });
                } else if (villes.length === 1) {
                    villesCommunes[villes[0]].forEach(function (c) { addOption(commune, c, current); });
                } else {
                    // Aucune ville choisie : communes groupées par ville
                    villes.forEach(function (v) {
                        var group = doc.createElement('optgroup');
                        group.label = niceName(v);
                        villesCommunes[v].forEach(function (c) { addOption(group, c, current); });
                        commune.appendChild(group);
                    });
                }
                commune.disabled = commune.options.length <= 1;
                selected = '';
                markFilled(commune);
            };
            ville.addEventListener('change', fill);
            // Choisir une commune renseigne automatiquement sa ville
            commune.addEventListener('change', function () {
                if (ville.value || !commune.value) return;
                Object.keys(villesCommunes).some(function (v) {
                    if ((villesCommunes[v] || []).indexOf(commune.value) !== -1) {
                        ville.value = v;
                        markFilled(ville);
                        return true;
                    }
                    return false;
                });
            });
            fill();
        }

        // Mise en évidence des champs renseignés
        form.querySelectorAll('select, input[type="number"]').forEach(function (field) {
            markFilled(field);
            field.addEventListener('change', function () { markFilled(field); });
            field.addEventListener('input', function () { markFilled(field); });
        });
    });

    function niceName(s) {
        return String(s).toLowerCase().replace(/(^|[\s'’-])(\S)/g, function (m, sep, ch) { return sep + ch.toUpperCase(); });
    }
    function markFilled(field) {
        if (!field || field.name === 'guests') return;
        field.classList.toggle('is-filled', field.value !== '' && !field.disabled);
    }

    /* ---------- Menu mobile : se ferme quand on choisit un lien ---------- */
    var mobileMenu = doc.getElementById('mobileMenu');
    if (mobileMenu) {
        mobileMenu.addEventListener('click', function (e) {
            if (e.target.closest('a[href]') && window.bootstrap) {
                window.bootstrap.Offcanvas.getOrCreateInstance(mobileMenu).hide();
            }
        });
    }

    /* ---------- Ouverture du panneau de recherche : focus 1er champ ---------- */
    var panel = doc.getElementById('searchPanel');
    if (panel) {
        panel.addEventListener('shown.bs.collapse', function () {
            var first = panel.querySelector('input, select');
            if (first) first.focus({ preventScroll: true });
        });
        doc.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && panel.classList.contains('show') && window.bootstrap) {
                window.bootstrap.Collapse.getOrCreateInstance(panel).hide();
            }
        });
    }
})();
