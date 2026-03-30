import './bootstrap';
import Alpine from 'alpinejs';
import selectInput from './components/select-input';
import multiselectInput from './components/multiselect-input';
import modalComponent from './components/modal';
import membershipForm from './memberships/membership-form';
import eventForm from './events/event-form';
import autoHide from './shared/auto-hide';
import reservationForm from './reservations/reservation-form.js';
import fileUpload from './components/file-upload';

window.Alpine = Alpine;

Alpine.data('fileUpload', fileUpload);
Alpine.data('selectInput', selectInput);
Alpine.data('multiselectInput', multiselectInput);
Alpine.data('modalComponent', modalComponent);
Alpine.data('membershipForm', membershipForm);
Alpine.data('eventForm', eventForm);
Alpine.data('autoHide', autoHide);
Alpine.data('reservationForm', reservationForm);

Alpine.start();
