export default () => ({
    openMember: false,
    openClub: false,
    openSport: false,
    selectedMember: '',
    selectedClub: '',
    previousClub: '',
    selectedSport: '',
    memberOptions: {},
    clubOptions: {},
    sportsByClub: {},
    get availableSports() {
        if (!this.selectedClub) return {};
        return this.sportsByClub[this.selectedClub] ?? {};
    },
    syncClubChange() {
        if (this.selectedClub !== this.previousClub) {
            this.selectedSport = '';
            this.previousClub = this.selectedClub;
        }
    }
});