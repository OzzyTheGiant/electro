<template>
	<table class="table">
		<thead>
			<tr><th>Date</th><th>Amount</th><th>Actions</th></tr>
		</thead>
		<tbody>
			<tr v-for="bill of bills" :key="bill.id">
				<td>{{ bill.paymentDate | shortDate }}</td>
				<td>${{ bill.paymentAmount | currency }}</td>
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
import { shortDate, currency } from '../../filters/Filters';

@Component({
	filters:{ shortDate, currency }
})
export default class BillTable extends Vue {
	@Prop() public bills!: Bill[];

	@Emit('edit')
	public onClickUpdateButton(billID:number):number {
		return billID;
	}

	@Emit('delete')
	public onClickDeleteButton(billID:number):number {
		return billID;
	}
}
</script>

<style scoped>
	th:last-child {text-align:right}
	.field.is-grouped {justify-content:flex-end}

	@media screen and (max-width:330px) {
		table button .fas {font-size:1rem;}
	}
</style>