export function validateClubForm(form) {
    let valid = true;
    let messages = [];

    form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));

    const name = form.querySelector('[name="name"]');
    if (!name.value || name.value.length < 2 || name.value.length > 30) {
        name.classList.add('border-red-500');
        valid = false;
        messages.push('Name must be between 2 and 30 characters.');
    }

    const phone = form.querySelector('[name="phone"]');
    const phonePattern = /^[0-9+\s\-()]{5,20}$/;
    if (!phone.value || !phonePattern.test(phone.value)) {
        phone.classList.add('border-red-500');
        valid = false;
        messages.push('Phone must be valid.');
    }

    const email = form.querySelector('[name="email"]');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value || !emailPattern.test(email.value) || email.value.length > 56) {
        email.classList.add('border-red-500');
        valid = false;
        messages.push('Email must be valid.');
    }

    const webpage = form.querySelector('[name="webpage"]');
    if (webpage && webpage.value) {
        try {
            new URL(webpage.value);
        } catch (_) {
            webpage.classList.add('border-red-500');
            valid = false;
            messages.push('Webpage must be a valid URL.');
        }
    }

    const address = form.querySelector('[name="address_id"]');
    if (address && address.value && isNaN(parseInt(address.value))) {
        address.classList.add('border-red-500');
        valid = false;
        messages.push('Selected address is invalid.');
    }

    return { valid, messages };
}
