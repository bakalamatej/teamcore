export function validateEventForm(form) {
    let valid = true;
    let messages = [];

    form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));

    const title = form.querySelector('[name="title"]');
    if (!title.value || title.value.length < 5 || title.value.length > 80) {
        title.classList.add('border-red-500');
        valid = false;
        messages.push('Title must be between 5 and 80 characters.');
    }

    const start = form.querySelector('[name="start_date"]');
    const end = form.querySelector('[name="end_date"]');

    if (!start.value) {
        start.classList.add('border-red-500');
        valid = false;
        messages.push('Start date is required.');
    }

    if (!end.value) {
        end.classList.add('border-red-500');
        valid = false;
        messages.push('End date is required.');
    }

    if (start.value && end.value) {
        const startDate = new Date(start.value);
        const endDate = new Date(end.value);
        if (endDate < startDate) {
            end.classList.add('border-red-500');
            valid = false;
            messages.push('End date must be after or equal to start date.');
        }
    }

    const description = form.querySelector('[name="description"]');
    if (description && description.value && description.value.length < 10) {
        description.classList.add('border-red-500');
        valid = false;
        messages.push('Description must be at least 10 characters if provided.');
    }

    return { valid, messages };
}
