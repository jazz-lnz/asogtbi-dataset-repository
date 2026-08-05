<script>
(() => {
    const menus = Array.from(document.querySelectorAll('[data-notification-menu]'));
    const dropdowns = Array.from(document.querySelectorAll('details.account-menu, details.portal-account-menu, details[data-notification-menu]'));
    const expandables = Array.from(document.querySelectorAll('details'));
    const toast = document.querySelector('[data-live-toast]');
    if (menus.length === 0) return;

    const replayDisclosureAnimation = (disclosure) => {
        disclosure.classList.remove('disclosure-animating');
        void disclosure.offsetWidth;
        disclosure.classList.add('disclosure-animating');
        window.setTimeout(() => disclosure.classList.remove('disclosure-animating'), 220);
    };

    expandables.forEach((disclosure) => {
        disclosure.querySelector(':scope > summary')?.addEventListener('click', () => {
            if (!disclosure.open) replayDisclosureAnimation(disclosure);
        });
    });

    const closeOtherDropdowns = (active) => {
        dropdowns.forEach((dropdown) => {
            if (dropdown !== active) dropdown.open = false;
        });
    };

    dropdowns.forEach((dropdown) => {
        dropdown.querySelector(':scope > summary')?.addEventListener('click', () => {
            if (!dropdown.open) closeOtherDropdowns(dropdown);
        });

        dropdown.addEventListener('toggle', () => {
            if (!dropdown.open) return;
            closeOtherDropdowns(dropdown);
        });
    });

    let latestId = Math.max(...menus.map((menu) => Number(menu.dataset.latestId || 0)));
    let audioContext = null;
    let audioReady = false;

    const unlockAudio = () => {
        audioReady = true;
        audioContext ??= new (window.AudioContext || window.webkitAudioContext)();
    };
    window.addEventListener('pointerdown', unlockAudio, { once: true });
    window.addEventListener('keydown', unlockAudio, { once: true });

    const beep = () => {
        if (!audioReady || !audioContext) return;
        const oscillator = audioContext.createOscillator();
        const gain = audioContext.createGain();
        oscillator.type = 'sine';
        oscillator.frequency.value = 880;
        gain.gain.setValueAtTime(0.0001, audioContext.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.07, audioContext.currentTime + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, audioContext.currentTime + 0.24);
        oscillator.connect(gain).connect(audioContext.destination);
        oscillator.start();
        oscillator.stop(audioContext.currentTime + 0.25);
    };

    const updateBadges = (count) => {
        document.querySelectorAll('[data-notification-count]').forEach((badge) => {
            badge.textContent = String(count);
            badge.hidden = count < 1;
        });
    };

    const showToast = (latest) => {
        if (!toast) return;
        toast.querySelector('[data-live-toast-title]').textContent = latest?.title || 'New activity';
        toast.querySelector('[data-live-toast-message]').textContent = latest?.message || 'Open notifications for details.';
        toast.hidden = false;
        window.setTimeout(() => { toast.hidden = true; }, 7000);
    };

    const poll = async () => {
        try {
            const response = await fetch('<?= site_url('notifications/poll') ?>', { headers: { 'Accept': 'application/json' } });
            if (!response.ok) return;
            const data = await response.json();
            updateBadges(Number(data.unreadCount || 0));
            const incomingId = Number(data.latest?.id || 0);
            if (incomingId > latestId) {
                if (latestId > 0) {
                    showToast(data.latest);
                    beep();
                }
                latestId = incomingId;
                menus.forEach((menu) => { menu.dataset.latestId = String(incomingId); });
            }
        } catch (error) {
            // The next polling cycle quietly retries.
        }
    };

    window.setInterval(poll, 25000);
})();
</script>
