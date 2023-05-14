import { createApp } from 'vue';
const Vue = createApp({})

import './bootstrap';

// Vue.mixin({
//     methods: {
//         __relativeTime(date) {
//             if (date == null) return 'Не указано';
//             // 5 минут назад, 3 дня назад
//             return moment(date).startOf('minute').fromNow();
//         },
//         __ordinaryTime(date) {
//             if (date == null) return 'Не указано';
//             // 5 минут назад, 3 дня назад
//             return moment(date).format('LT');
//         },
//     }
// });

import './../../public/vendor/adminlte/dist/js/adminlte.min';
import './../../public/vendor/jquery/jquery.min';
import './../../public/vendor/overlayScrollbars/js/jquery.overlayScrollbars.min';
import './../../public/vendor/bootstrap/js/bootstrap.bundle.min';

// import './admin/bootstrap';
// import './admin/app-components/bootstrap';
// import './admin/index';
// import 'vue-multiselect/dist/vue-multiselect.min.css';
// import flatPickr from 'vue-flatpickr-component';
// import Notifications from '@kyvg/vue3-notification';
// import Multiselect from 'vue-multiselect';
// import 'flatpickr/dist/flatpickr.css';
// import VModal from 'vue3-simple-dialog'
// import Badge from './admin/ui/Badge.vue';
// import Monitor from "./admin/monitor/Monitor.vue";
// import PillBadge from './admin/ui/PillBadge.vue';
// import Reception from './admin/reception/Reception.vue';
// import TestPrint from './admin/reception/Test.vue';
// import Cabinet from './admin/cabinet/Cabinet.vue';
// const options = {
//     name: '_blank',
//     specs: [
//         'fullscreen=no',
//         'titlebar=no',
//         'scrollbars=no'
//     ],
//     styles: [
//         // 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
//         // 'https://unpkg.com/kidlat-css/css/kidlat.css'
//     ]
// }
// Vue.use(VModal, { dialog: true, dynamic: true, injectModalsContainer: true });
// Vue.use(Notifications);
// Vue.use('test-print', TestPrint);
// Vue.component('monitor', Monitor);
// Vue.component('multiselect', Multiselect);
// Vue.component('datetime', flatPickr);
// Vue.component('monitor', Monitor);
// Vue.component('reception', Reception);
// Vue.component('cabinet', Cabinet);
Vue.component('badge', Badge);
// Vue.component('pill-badge', PillBadge);
// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
// //     Vue.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

Vue.mount('#app')
