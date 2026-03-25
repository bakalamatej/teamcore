export default () => ({
    openSport: false,
    selectedSport: '',
    previousSport: '',
    sportOptions: {},
    openEventType: false,
    openSportField: false,
    selectedEventType: '',
    eventTypesBySport: {},
    selectedClubs: [],
    clubsBySport: {},
    selectedSportField: '',
    sportFieldsBySport: {},
    
    get availableEventTypes() {
        return Object.values(this.eventTypesBySport).reduce((acc, types) => ({
            ...acc,
            ...types,
        }), {});
    },
    get availableClubs() {
        const sportId = this.getSportIdByEventType(this.selectedEventType);
        if (!sportId) return {};
        return this.clubsBySport[sportId] ?? {};
    },

    get availableSportFields() {
        const sportId = this.getSportIdByEventType(this.selectedEventType);
        if (!sportId) return {};
        return this.sportFieldsBySport[sportId] ?? {};
    },
    getSportIdByEventType(eventTypeId) {
        const typeId = String(eventTypeId);
        for (const [sportId, types] of Object.entries(this.eventTypesBySport)) {
            if (Object.prototype.hasOwnProperty.call(types, typeId)) {
                return String(sportId);
            }
        }
        return '';
    },
    syncSportChange() {
        const eventTypeSport = this.getSportIdByEventType(this.selectedEventType);

        if (eventTypeSport) {
            this.selectedSport = eventTypeSport;
        }

        if (this.previousSport === '') {
            this.previousSport = this.selectedSport;
            return;
        }

        if (this.selectedSport !== this.previousSport) {
            this.selectedClubs = [];
            this.selectedSportField = '';
            this.previousSport = this.selectedSport;
        }
    }
});