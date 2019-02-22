<template>
	<div class="columns is-4 is-marginless">
		<bill-form/>
		<bill-viewer :bills="bills"/>
	</div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
import BillForm from "./BillForm.vue";
import BillViewer from "./BillViewer.vue";

@Component({
	components:{
		"bill-form":BillForm,
		"bill-viewer":BillViewer
	}
})
export default class DashBoard extends Vue {
	public bills = [
		{id:1, paymentDate:"2019-01-01", paymentAmount:127.50},
		{id:2, paymentDate:"2019-02-01", paymentAmount:92.50},
		{id:3, paymentDate:"2019-03-01", paymentAmount:80.50},
		{id:4, paymentDate:"2019-04-01", paymentAmount:75.50},
		{id:5, paymentDate:"2019-05-01", paymentAmount:125.50},
		{id:6, paymentDate:"2019-06-01", paymentAmount:107.50},
		{id:7, paymentDate:"2019-07-01", paymentAmount:109.50},
		{id:8, paymentDate:"2019-08-01", paymentAmount:95.50},
		{id:9, paymentDate:"2019-09-01", paymentAmount:98.50},
		{id:10, paymentDate:"2019-10-01", paymentAmount:82.50},
		{id:11, paymentDate:"2019-11-01", paymentAmount:99.50},
		{id:12, paymentDate:"2019-12-01", paymentAmount:133.50}
	];
}
</script>

<style scoped></style>