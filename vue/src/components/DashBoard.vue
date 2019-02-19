<template>
	<div class="columns">
		<bill-form/>
	</div>
</template>

<script>
import { Component, Vue } from "vue-property-decorator";
import BillForm from "./BillForm.vue";

@Component({
	components:{
		"bill-form":BillForm
	}
})
export default class DashBoard extends Vue {
	
}
</script>

<style scoped></style>