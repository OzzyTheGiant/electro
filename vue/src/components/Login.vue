<template>
	<div class="columns is-centered is-vcentered is-marginless">
		<div class="column is-half">
			<form class="widget" autocomplete="off" @submit.prevent="submitForm">
				<h1>Electro</h1>
				<field name="login-username" type="text" placeholder="Username" v-model="credentials.username"/>
				<field name="login-password" type="password" placeholder="Password" v-model="credentials.password"/>
				<div class="field">
					<div class="control"><button type="submit" class="button">Log In</button></div>
				</div>
			</form>
		</div>
	</div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Emit } from "vue-property-decorator";
import Field from "./form/Field.vue";

@Component({
	components:{field:Field}
})
export default class Login extends Vue {
	public credentials = {
		username: null,
		password: null
	};

	@Emit()
	public submitForm():void {
		this.$emit("login", this.credentials);
	}
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
	.column {
		max-width:500px;
		height:305px;
	}
</style>