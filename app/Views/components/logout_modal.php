<!-- Logout Confirmation Modal -->
<div class="logout-confirm-modal" id="logout-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="logout-modal-title" hidden>
    <div class="logout-modal-backdrop"></div>
    <article class="logout-modal-card" tabindex="-1">
        <div class="logout-modal-icon">
            <span class="material-symbols-rounded" aria-hidden="true">logout</span>
        </div>
        <h2 id="logout-modal-title">Confirm Logout</h2>
        <p>Are you sure you want to log out of your account?</p>
        <div class="logout-modal-actions">
            <button class="button secondary" type="button" data-logout-cancel>Cancel</button>
            <button class="button logout-confirm-btn" type="button" data-logout-confirm>Logout</button>
        </div>
    </article>
</div>

<script>
(() => {
    const logoutModal = document.getElementById('logout-confirm-modal');
    let logoutForm = null;

    document.querySelectorAll('form[action*="logout"]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            logoutForm = form;
            logoutModal.hidden = false;
            document.body.classList.add('preview-open');
            logoutModal.querySelector('.logout-confirm-btn')?.focus();
        });
    });

    logoutModal?.querySelector('[data-logout-cancel]')?.addEventListener('click', () => {
        logoutModal.hidden = true;
        document.body.classList.remove('preview-open');
        logoutForm = null;
    });

    logoutModal?.querySelector('[data-logout-confirm]')?.addEventListener('click', () => {
        logoutModal.hidden = true;
        document.body.classList.remove('preview-open');
        logoutForm?.submit();
        logoutForm = null;
    });

    logoutModal?.querySelector('.logout-modal-backdrop')?.addEventListener('click', () => {
        logoutModal.hidden = true;
        document.body.classList.remove('preview-open');
        logoutForm = null;
    });

    // Escape key closes modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !logoutModal?.hidden) {
            logoutModal.hidden = true;
            document.body.classList.remove('preview-open');
            logoutForm = null;
        }
    });

    // Focus trap: keep Tab inside modal when open
    logoutModal?.addEventListener('keydown', (e) => {
        if (e.key !== 'Tab' || logoutModal.hidden) return;
        const focusable = logoutModal.querySelectorAll('button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])');
        if (focusable.length === 0) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    });
})();
</script>
