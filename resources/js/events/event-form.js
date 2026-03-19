export default () => ({
    openSport: false,
    selectedSport: '',
    previousSport: '',
    sportOptions: {},
    openEventType: false,
    selectedEventType: '',
    eventTypesBySport: {},
    selectedClubIds: [],
    clubsBySport: {},
    get availableEventTypes() {
        if (!this.selectedSport) return {};
        return this.eventTypesBySport[this.selectedSport] ?? {};
    },
    get availableClubs() {
        if (!this.selectedSport) return {};
        return this.clubsBySport[this.selectedSport] ?? {};
    },
    syncSportChange() {
        if (this.selectedSport !== this.previousSport) {
            this.selectedEventType = '';
            this.selectedClubIds = [];
            this.previousSport = this.selectedSport;
        }
    }
});