document.addEventListener('DOMContentLoaded', () => {
    const openNewAddress = document.getElementById('open-new-address');
    const useExistingAddress = document.getElementById('use-existing-address');
    const newAddressFields = document.getElementById('new-address-fields');
    const existingAddressBlock = document.getElementById('existing-address-block');
    const addressSelect = document.getElementById('address_id');

    if (!openNewAddress || !useExistingAddress || !newAddressFields || !existingAddressBlock || !addressSelect) {
        return;
    }

    openNewAddress.addEventListener('click', () => {
        newAddressFields.classList.remove('hidden');
        existingAddressBlock.classList.add('hidden');
        addressSelect.value = '';
    });

    useExistingAddress.addEventListener('click', () => {
        newAddressFields.classList.add('hidden');
        existingAddressBlock.classList.remove('hidden');
        addressSelect.focus();
    });
});