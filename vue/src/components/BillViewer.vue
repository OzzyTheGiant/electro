<template>
	<div class="column is-8">
		<div class="widget">
			<div class="level is-mobile">
				<h2 class="level-left">Your Bills</h2>
				<div class="level-right level is-marginless is-mobile">
					<field class="level-left" v-model="year" name="bv-year" type="text" placeholder="Year" :horizontal="true" length="4"/>
					<button class="level-right button" @click.prevent="toggleView"><i class="fas fa-chart-line"></i></button>
				</div>
			</div>
			<bill-table v-show="bills && !displayChart" :bills="bills"/>
			<bill-line-chart v-if="initChart" v-show="bills && displayChart" :payments="payments"/>
			<div v-show="!bills || !bills.length">No payments made in this year</div>
		</div>
	</div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from "vue-property-decorator";
import { Bill } from "../models/Bill";
import BillTable from "./bill-viewer/BillTable.vue";
import BillLineChart from "./bill-viewer/BillLineChart.vue";

@Component({
	components:{
		"bill-table":BillTable,
		"bill-line-chart": BillLineChart
	}
})
export default class BillViewer extends Vue {
	@Prop() public bills!: Bill[];

	public initChart:boolean = false;
	public displayChart:boolean = false;
	public year:number = new Date().getFullYear();

	get payments():number[] {
		return this.bills ? this.bills.map(bill => bill.paymentAmount) : [];
	}

	public toggleView():void {
		/* initialize the component if it's the first time toggling, but toggle visibility onwards */
		if (!this.initChart) this.initChart = true;
		this.displayChart = !this.displayChart;
	}
}
</script>

<style scoped lang="scss">
	div.level-right {min-width:180px}

	@media screen and (max-width:400px) {
		.widget {
			.level h2 {margin-bottom:1rem;}
			> .level.is-mobile {display:block;}
		}
	}
</style>