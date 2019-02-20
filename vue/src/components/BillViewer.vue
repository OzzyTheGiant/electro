<template>
	<div class="column is-8">
		<div class="widget">
			<div class="level">
				<h2 class="level-left">Your Bills</h2>
				<field v-model="year" class="level-right" name="bv-year" type="text" placeholder="Year" :horizontal="true"/>
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

<style scoped></style>