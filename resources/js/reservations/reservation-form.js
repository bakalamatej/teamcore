export default () => ({
    openSport: false,
    openSportField: false,
    openClub: false,
    openMembership: false,
    selectedSport: '',
    previousSport: '',
    selectedSportField: '',
    selectedClub: '',
    previousClub: '',
    selectedMembership: '',
    sportOptions: {},
    sportFieldsBySport: {},
    clubsBySport: {},
    membershipsByClub: {},
    get availableSportFields() {
        if (!this.selectedSport) return {};
        return this.sportFieldsBySport[this.selectedSport] ?? {};
    },
    get availableClubs() {
        if (!this.selectedSport) return {};
        return this.clubsBySport[this.selectedSport] ?? {};
    },
    get availableMemberships() {
        if (!this.selectedClub) return {};
        return this.membershipsByClub[this.selectedClub] ?? {};
    },
    syncSportChange() {
        if (this.selectedSport !== this.previousSport) {
            this.selectedSportField = '';
            this.selectedClub = '';
            this.selectedMembership = '';
            this.previousSport = this.selectedSport;
        }
    },
    syncClubChange() {
        if (this.selectedClub !== this.previousClub) {
            this.selectedMembership = '';
            this.previousClub = this.selectedClub;
        }
    }
});