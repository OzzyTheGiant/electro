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
import { Bill } from "../models/Bill";

@Component({
	components:{
		"bill-form":BillForm,
		"bill-viewer":BillViewer
	}
})
export default class DashBoard extends Vue {
	public bills: Bill[] = [
		{ID:1, User:1, PaymentDate:"2019-01-01", PaymentAmount:127.50},
		{ID:2, User:1, PaymentDate:"2019-02-01", PaymentAmount:92.50},
		{ID:3, User:1, PaymentDate:"2019-03-01", PaymentAmount:80.50},
		{ID:4, User:1, PaymentDate:"2019-04-01", PaymentAmount:75.50},
		{ID:5, User:1, PaymentDate:"2019-05-01", PaymentAmount:125.50},
		{ID:6, User:1, PaymentDate:"2019-06-01", PaymentAmount:107.50},
		{ID:7, User:1, PaymentDate:"2019-07-01", PaymentAmount:109.50},
		{ID:8, User:1, PaymentDate:"2019-08-01", PaymentAmount:95.50},
		{ID:9, User:1, PaymentDate:"2019-09-01", PaymentAmount:98.50},
		{ID:10, User:1, PaymentDate:"2019-10-01", PaymentAmount:82.50},
		{ID:11, User:1, PaymentDate:"2019-11-01", PaymentAmount:99.50},
		{ID:12, User:1, PaymentDate:"2019-12-01", PaymentAmount:133.50}
	];
}
</script>

<style scoped></style>