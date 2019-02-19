<template>
	<form autocomplete="off" class="column is-one-third" @submit.prevent="onSubmitBillForm">
		<h2>Add Bill</h2>
		<v-date-picker v-model="formData.paymentDate" class="field" :attributes="pickerSettings">
			<field slot-scope="slotProps" name="b-pay-date" :value="slotProps.inputValue" type="datepicker" label-text="Payment Amount"/>
		</v-date-picker>
		<field name="b-pay-amount" v-model="formData.paymentAmount" type="currency" label-text="Payment Amount"/>
		<div class="field">
			<div class="control"><button type="submit" class="button">Add Bill</button></div>
		</div>
	</form>
</template>

<script lang="ts">
import { Component, Vue, Prop, Emit } from "vue-property-decorator";
import { DatePicker } from 'v-calendar';

@Component
export default class BillForm extends Vue {
	public formData:any = {
		paymentAmount: null,
		paymentDate: null
	};

	public pickerSettings:any = [{
		key: 'today',
		dates: new Date(),
		contentStyle:{ color:"#FFDD57" }
	}];

	@Emit()
	public onSubmitBillForm():void {
		this.$emit("submitBill", this.formData);
	}
}
</script>

<style scoped>
	.field:nth-of-type(2) {margin-top:1rem;}
</style>