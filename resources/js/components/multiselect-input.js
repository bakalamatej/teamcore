export default (config = {}) => ({
    open: false,
    dropdownUp: false,
    selected: (config.selected ?? []).map((value) => String(value)),
    placeholder: config.placeholder ?? 'Select options',
    updateDropdownPlacement() {
        this.$nextTick(() => {
            const triggerRect = this.$el.getBoundingClientRect();
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            const dropdownHeight = 240;
            const spaceBelow = viewportHeight - triggerRect.bottom - 8;
            const spaceAbove = triggerRect.top;

            this.dropdownUp = spaceBelow < dropdownHeight && spaceAbove > spaceBelow;
        });
    },
    toggleDropdown() {
        this.open = !this.open;
        if (this.open) {
            this.updateDropdownPlacement();
            requestAnimationFrame(() => this.updateDropdownPlacement());
        }
    },
    toggle(value) {
        const normalizedValue = String(value);
        const current = (this.selected ?? []).map((selectedValue) => String(selectedValue));

        if (current.includes(normalizedValue)) {
            this.selected = current.filter((selectedValue) => selectedValue !== normalizedValue);
        } else {
            this.selected = [...current, normalizedValue];
        }

        this.$nextTick(() => {
            this.$el.dispatchEvent(new Event('input', { bubbles: true }));
        });
    },
    label(options) {
        if (this.selected.length === 0) {
            return this.placeholder;
        }

        return this.selected.map((value) => options[String(value)] ?? value).join(', ');
    }
});
