export default (config = {}) => ({
    open: false,
    dropdownUp: false,
    options: config.options ?? {},
    search: '',
    selected: config.selected ?? null,
    placeholderText: config.placeholderText ?? '',
    inputPlaceholderText: config.inputPlaceholderText ?? '',
    isDisabled: config.isDisabled ?? false,
    searchable: config.searchable ?? true,

    dropdownStyle: {},

    init() {
        this.search = this.selected !== '' && this.selected !== null
            ? (this.options[this.selected] ?? '')
            : '';
    },

    getTriggerElement() {
        return this.$refs.trigger ?? this.$el;
    },

    updateDropdownPlacement() {
        this.$nextTick(() => {
            const trigger = this.getTriggerElement();
            if (!trigger) {
                return;
            }

            const rect = trigger.getBoundingClientRect();
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            const estimatedDropdownHeight = 240;
            const gap = 4;

            const spaceBelow = viewportHeight - rect.bottom - gap;
            const spaceAbove = rect.top - gap;

            this.dropdownUp = spaceBelow < estimatedDropdownHeight && spaceAbove > spaceBelow;

            const top = this.dropdownUp
                ? Math.max(8, rect.top - Math.min(estimatedDropdownHeight, spaceAbove) - gap)
                : rect.bottom + gap;

            this.dropdownStyle = {
                position: 'fixed',
                left: `${rect.left}px`,
                top: `${top}px`,
                width: `${rect.width}px`,
                minWidth: `${rect.width}px`,
                zIndex: '10000',
                boxSizing: 'border-box'
            };
        });
    },

    openDropdown() {
        if (this.isDisabled) {
            return;
        }

        this.open = true;
        this.updateDropdownPlacement();
        requestAnimationFrame(() => this.updateDropdownPlacement());
    },

    toggleDropdown() {
        if (this.isDisabled) {
            return;
        }

        this.open = !this.open;

        if (this.open) {
            this.updateDropdownPlacement();
            requestAnimationFrame(() => this.updateDropdownPlacement());
        }
    },

    get filteredOptions() {
        if (!this.searchable) {
            return this.options;
        }

        if (!this.search) {
            return this.options;
        }

        const query = this.search.toLowerCase();

        return Object.fromEntries(
            Object.entries(this.options).filter(([, label]) =>
                String(label).toLowerCase().includes(query)
            )
        );
    },

    choose(val) {
        this.selected = val;
        this.open = false;
        this.search = this.options[val] ?? '';

        this.$nextTick(() => {
            const hiddenInput = this.$refs.hiddenInput;

            if (hiddenInput) {
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            this.$el.dispatchEvent(new Event('input', { bubbles: true }));
            this.$el.dispatchEvent(new Event('change', { bubbles: true }));

            if (this.$el.hasAttribute('data-submit-on-choose')) {
                this.$nextTick(() => {
                    const form = this.$el.closest('form');
                    if (form && form.tagName === 'FORM') {
                        form.requestSubmit ? form.requestSubmit() : form.submit();
                    }
                });
            }
        });
    },

    clearAndOpen() {
        if (!this.open) {
            this.selected = '';
            this.search = '';

            this.$nextTick(() => {
                this.$el.dispatchEvent(new Event('input', { bubbles: true }));
            });
        }

        this.openDropdown();
    },

    handleInput() {
        this.selected = '';
        this.open = true;
        this.updateDropdownPlacement();
    },

    closeDropdown() {
        this.open = false;

        if (this.searchable) {
            this.search = this.selected !== '' && this.selected !== null
                ? (this.options[this.selected] ?? '')
                : '';
        }
    }
});