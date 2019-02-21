<template>
	<div class="column is-8">
		<div class="widget">
			<div class="level is-mobile">
				<h2 class="level-left">Your Bills</h2>
				<div class="level-right level is-marginless is-mobile">
					<field class="level-left" v-model="year" name="bv-year" type="text" placeholder="Year" :horizontal="true" length="4"/>
					<button class="level-right button"><i class="fas fa-chart-line"></i></button>
				</div>
			</div>
			<div></div>
			<div></div>
		</div>
	</div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from "vue-property-decorator";
import { Bill } from "../models/Bill";

@Component
export default class BillViewer extends Vue {
	@Prop() public bills!: Bill[];

	public year:number = new Date().getFullYear();
}
</script>

<style scoped>
	div.level-right {min-width:180px}
</style>