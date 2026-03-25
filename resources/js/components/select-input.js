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

    init() {
        this.search = this.selected !== '' && this.selected !== null
            ? (this.options[this.selected] ?? '')
            : '';
    },

    updateDropdownPlacement() {
        this.$nextTick(() => {
            const triggerRect = this.$el.getBoundingClientRect();
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            const dropdownHeight = 240;
            const spaceBelow = viewportHeight - triggerRect.bottom - 8;
            const spaceAbove = triggerRect.top;

            this.dropdownUp = spaceBelow < dropdownHeight && spaceAbove > spaceBelow;

            const dropdown = this.$refs.dropdown;
            if (dropdown) {
                dropdown.style.zIndex = 10000;
            }
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
            this.$el.dispatchEvent(new Event('input', { bubbles: true }));

            if (this.$el.hasAttribute('data-submit-on-choose')) {
                this.$nextTick(() => {
                    const form = this.$el.closest('form');
                    if (form && form.tagName === 'FORM') {
                        form.submit();
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