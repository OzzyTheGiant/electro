<template>
	<div class="column is-4">
		<form class="widget" autocomplete="off" @submit.prevent="onSubmitBillForm">
			<h2>Add Bill</h2>
			<v-date-picker v-model="formData.PaymentDate" class="field" :attributes="pickerSettings" :min-date="minDate" :max-date="maxDate">
				<field slot-scope="slotProps" name="b-pay-date" :value="slotProps.inputValue" type="datepicker" label-text="Payment Amount"/>
			</v-date-picker>
			<field name="b-pay-amount" v-model="formData.PaymentAmount" type="currency" label-text="Payment Amount"/>
			<div class="field">
				<div class="control"><button type="submit" class="button">Add Bill</button></div>
			</div>
		</form>
	</div>
</template>

<script lang="ts">
import { Component, Vue, Prop, Emit } from "vue-property-decorator";
import { DatePicker } from 'v-calendar';
import { Bill } from "../models/Bill";

@Component
export default class BillForm extends Vue {
	public formData:any = {
		PaymentAmount: undefined,
		PaymentDate: undefined
	};

	public pickerSettings:any = [{
		key: 'today',
		dates: new Date(),
		contentStyle:{ color:"#FFDD57" }
	}];

	get minDate():Date { return new Date(2015, 0, 1); }
	get maxDate():Date { return new Date(2030, 11, 31); }

	@Emit("submitBill")
	public onSubmitBillForm():Bill {
		return this.formData;
	}
}
</script>

<style scoped>
	.field:nth-of-type(2) {margin-top:1rem;}
</style>