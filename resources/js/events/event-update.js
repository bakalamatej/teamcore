import { validateEventForm } from './event-validation.js';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('updateEventForm');
    if (!form) return;

    const action = form.dataset.action;

    const errorBox = document.getElementById('formErrorBox');
    const errorMsg = document.getElementById('formErrorMessage');
    const closeBtn = document.getElementById('formErrorClose');

    const showError = (msg) => {
        errorMsg.textContent = msg;
        errorBox.classList.add('show');
    };

    const hideError = () => {
        errorMsg.textContent = '';
        errorBox.classList.remove('show');
    };

    if (closeBtn) {
        closeBtn.addEventListener('click', hideError);
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        hideError();

        const result = validateEventForm(form);
        if (!result.valid) {
            showError(result.messages[0]);
            return;
        }

        const formData = new FormData(form);
        formData.set('_method', 'PATCH');

        try {
            const response = await fetch(action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData
            });

            if (!response.ok) {
                if (response.status === 422) {
                    const data = await response.json();
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0][0];
                        showError(firstError);
                        return;
                    }
                }

                showError('Unknown error');
                return;
            }

            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'update-event' }));
        } catch {
            showError('Unknown error');
        }
    });
});
