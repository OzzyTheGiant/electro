<template>
	<div class="columns is-4 is-marginless">
		<bill-form/>
		<bill-viewer/>
	</div>
</template>

<script>
import { Component, Vue } from "vue-property-decorator";
import BillForm from "./BillForm.vue";
import BillViewer from "./BillViewer.vue";

@Component({
	components:{
		"bill-form":BillForm,
		"bill-viewer":BillViewer
	}
})
export default class DashBoard extends Vue {

}
</script>

<style scoped></style>