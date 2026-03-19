export default (config = {}) => ({
    show: config.show ?? false,
    focusable: config.focusable ?? false,
    init() {
        this.$watch('show', (value) => {
            if (value) {
                document.body.classList.add('overflow-y-hidden');

                if (this.focusable) {
                    setTimeout(() => {
                        this.firstFocusable()?.focus();
                    }, 100);
                }

                return;
            }

            document.body.classList.remove('overflow-y-hidden');
        });
    },
    focusables() {
        const selector = "a, button, input:not([type='hidden']), textarea, select, details, [tabindex]:not([tabindex='-1'])";
        return [...this.$el.querySelectorAll(selector)].filter((element) => !element.hasAttribute('disabled'));
    },
    firstFocusable() {
        return this.focusables()[0];
    },
    lastFocusable() {
        return this.focusables().slice(-1)[0];
    },
    nextFocusable() {
        return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable();
    },
    prevFocusable() {
        return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable();
    },
    nextFocusableIndex() {
        return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1);
    },
    prevFocusableIndex() {
        return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1;
    }
});