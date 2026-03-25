export default () => ({
    openMembership: false,
    openSportField: false,
    selectedMembership: '',
    selectedSportField: '',
    memberships: {},
    membershipMeta: {},
    sportFieldsBySport: {},

    get currentClubId() {
        if (!this.selectedMembership) return '';
        return this.membershipMeta[this.selectedMembership]?.club_id?.toString() ?? '';
    },

    get currentSportId() {
        if (!this.selectedMembership) return '';
        return this.membershipMeta[this.selectedMembership]?.sport_id?.toString() ?? '';
    },

    get availableSportFields() {
        if (!this.currentSportId) return {};
        return this.sportFieldsBySport[this.currentSportId] ?? {};
    },

    syncMembershipChange() {
        if (
            this.selectedSportField &&
            !Object.prototype.hasOwnProperty.call(this.availableSportFields, this.selectedSportField)
        ) {
            this.selectedSportField = '';
        }
    }
});