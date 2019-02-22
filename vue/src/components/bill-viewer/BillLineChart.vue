<!-- do not add <template> tag, as the mixin will provide its own template -->
<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { Line, mixins } from "vue-chartjs";

const initChartData = () => {
	return {
		labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
		datasets: [
			{
				label: 'Payment Amount',
				backgroundColor: 'rgba(255,221,87,0.5)',
				borderColor: '#FFDD57',
				fill: 'start',
				lineTension:0,
				data:[]
			}
		]
	};
};

@Component({
	extends:Line,
	mixins:[mixins.reactiveProp],
})
export default class BillLineChart extends Vue<Line> {
	@Prop() public payments!:number[];
	@Prop({default:initChartData}) public chartData!:any;

	public options:object = {
		responsive: true,
		maintainAspectRatio: false
	};

	constructor() { super(); }

	public created():void {
		if (this.payments) this.chartData.datasets[0].data = this.payments;
	}

	public mounted():void {
		this.renderChart(this.chartData, this.options);
	}

	public updated():void {
		this.renderChart(this.chartData, this.options);
	}
}
</script>

<style lang="scss" scoped></style>