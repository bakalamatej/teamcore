export default (delay = 2000) => ({
    show: true,
    init() {
        setTimeout(() => {
            this.show = false;
        }, delay);
    }
});
