<template>
	<div id="app">
		<nav></nav>
		<div id="module-container" class="columns is-centered">
			<login/>
		</div>
  	</div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
import Login from "./components/Login.vue";

@Component({
  	components: {
    	login:Login
  	}
})
export default class App extends Vue {
  	
}
</script>

<style lang="scss">
	@import "./sass/main";

	#app {
		height:100%;
		display:flex;
		flex-direction:column;
	}

	nav {
		flex-grow:0;
		height:3rem;
	}

	#module-container {
		padding:1rem 0.5rem;
		align-items:center;
		flex-grow:1;
	}
</style>