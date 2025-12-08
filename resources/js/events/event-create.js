import { validateEventForm } from './event-validation.js';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('eventCreateForm');
    if (!form) return;

    const action = form.dataset.action;

    const errorBox = document.getElementById('formErrorBox');
    const errorMsg = document.getElementById('formErrorMessage');
    const closeBtn = document.getElementById('formErrorClose');

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            errorBox.classList.add('show');
            errorBox.classList.remove('show');
            errorMsg.textContent = '';
        });
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        errorBox.classList.add('show');
        errorBox.classList.remove('show');
        errorMsg.textContent = '';

        const result = validateEventForm(form);
        if (!result.valid) {
            errorMsg.textContent = result.messages[0];
            errorBox.classList.remove('show');
            errorBox.classList.add('show');
            return;
        }

        const formData = new FormData(form);
        
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
                        errorMsg.textContent = firstError;
                        errorBox.classList.remove('show');
                        errorBox.classList.add('show');
                        return;
                    }
                }

                const errorText = await response.text();
                throw new Error(errorText);
            }

            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-event' }));
            form.reset();

        } catch (err) {
            errorMsg.textContent = 'Unknown error';
            errorBox.classList.remove('show');
            errorBox.classList.add('show');
        }
    });
});
