import './bootstrap';
import Alpine from 'alpinejs';
import './memberships/membership-add.js';
import selectInput from './components/select-input';
import multiselectInput from './components/multiselect-input';
import modalComponent from './components/modal';
import membershipForm from './memberships/membership-form';
import eventForm from './events/event-form';
import autoHide from './shared/auto-hide';

window.Alpine = Alpine;

Alpine.data('selectInput', selectInput);
Alpine.data('multiselectInput', multiselectInput);
Alpine.data('modalComponent', modalComponent);
Alpine.data('membershipForm', membershipForm);
Alpine.data('eventForm', eventForm);
Alpine.data('autoHide', autoHide);

Alpine.start();
