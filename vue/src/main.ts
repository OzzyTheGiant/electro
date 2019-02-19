import Vue from 'vue';
import App from './App.vue';
import './registerServiceWorker';
import VCalendar from 'v-calendar';
import Field from './components/form/Field.vue';

Vue.config.productionTip = false;

Vue.component('field', Field); // custom field component

Vue.use(VCalendar, { // v-date-picker settings
	datePickerTintColor:'#282e38',
	navVisibility:'focus', // this will apply to mobile devices; on desktop, calendar will appear on hover
	paneWidth:300, // width, in pixels, of calendar pane
	formats:{
		title: 'MMMM YYYY',
		weekdays: 'W',
		navMonths: 'MMM',
		dayPopover: 'L',
		input:[
			'MMMM D, YYYY', // default, displayed on <input>
			'YYYY-MM-DD', // mysql or other database format
			'M/D/YY',
			'M/D/YYYY',
			'MM/DD/YY',
			'MM/DD/YYYY',
			'M-D-YY',
			'M-D-YYYY',
			'MM-DD-YY',
			'MM-DD-YYYY',
		]
	}
});

new Vue({
	render: (h) => h(App),
}).$mount('#app');
