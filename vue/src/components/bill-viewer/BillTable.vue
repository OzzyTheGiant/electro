<template>
	<table class="table">
		<thead>
			<tr><th>Bill Date</th><th>Bill Amount</th><th>Actions</th></tr>
		</thead>
		<tbody>
			<tr v-for="bill of bills" :key="bill.id">
				<td>{{ bill.paymentDate }}</td>
				<td>${{ bill.paymentAmount }}</td>
				<td>
					<div class="field is-grouped">
						<div class="control">
							<button class="button" @click.prevent="onClickUpdateButton(bill.id)"><i class="fas fa-pen"></i></button>
						</div>
						<div class="control">
							<button class="button is-danger" @click.prevent="onClickDeleteButton(bill.id)"><i class="fas fa-trash-alt"></i></button>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</template>

<script lang="ts">
import { Component, Vue, Prop, Emit } from 'vue-property-decorator';
import { Bill } from '../../models/Bill';

@Component
export default class BillTable extends Vue {
	@Prop() public bills: Bill[] = [
		{id:1, paymentDate:"2019-01-01", paymentAmount:75.50},
		{id:1, paymentDate:"2019-02-01", paymentAmount:92.50}
	];

	@Emit()
	public onClickUpdateButton(billID:number):void {
		this.$emit('edit', billID);
	}

	@Emit()
	public onClickDeleteButton(billID:number):void {
		this.$emit('delete', billID);
	}
}
</script>

<style scoped>
	th:last-child {text-align:right}
	.field.isgrouped {justify-content:flex-end}
</style>