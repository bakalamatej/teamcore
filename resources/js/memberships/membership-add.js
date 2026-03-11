import Alpine from 'alpinejs';

Alpine.data('membershipAddForm', () => ({
    selectedSports: [],
    allClubsData: {},
    allSportsData: {},
    newClubId: '',
    newSportId: '',
    openClub: false,
    openSport: false,

    init() {
        this.selectedSports = JSON.parse(this.$el.dataset.sportIds);
        this.allClubsData   = JSON.parse(this.$el.dataset.clubs);
        this.allSportsData  = JSON.parse(this.$el.dataset.sports);
    },

    toggleSport(id) {
        id = String(id);
        const i = this.selectedSports.indexOf(id);
        if (i >= 0) this.selectedSports.splice(i, 1);
        else this.selectedSports.push(id);
        this.newClubId = '';
        this.newSportId = '';
    },

    get filteredClubs() {
        if (!this.selectedSports.length) return {};
        const res = {};
        for (const [id, c] of Object.entries(this.allClubsData)) {
            if (c.sports.some(s => this.selectedSports.includes(String(s)))) {
                res[id] = c.name;
            }
        }
        return res;
    },

    get sportsForNewClub() {
        if (!this.newClubId) return {};
        const res = {};
        for (const sid of (this.allClubsData[this.newClubId]?.sports ?? [])) {
            if (this.selectedSports.includes(String(sid))) {
                res[sid] = this.allSportsData[sid];
            }
        }
        return res;
    },
}));