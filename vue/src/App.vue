<template>
	<div id="app">
		<nav v-if="inSession" role="navigation" aria-label="main-navigation">
			<div class="container columns is-fullhd is-vcentered is-marginless">
				<h1>Electro</h1>
				<ul>
					<li><a class="link" @click.prevent="onClickLogoutButton">Log Out</a></li>
				</ul>
			</div>
		</nav>
		<main id="module-container">
			<login v-if="!inSession" @login="onLoginFormSubmit"/>
			<dashboard v-else/>
		</main>
  	</div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
import HttpService from "./services/HttpService";
import Login from "./components/Login.vue";
import DashBoard from "./components/DashBoard.vue";

@Component({
  	components: {
		login:Login, dashboard:DashBoard
  	}
})
export default class App extends Vue {
	private http:HttpService = new HttpService();
	private inSession:boolean = false;
	
	// TODO: implement true Http session login/logout services later
	public onLoginFormSubmit(credentials:any):void {
		if (this.http.login(credentials)) this.inSession = true;
	}

	public onClickLogoutButton():void {
		this.inSession = false;
	}
}
</script>

<style lang="scss">
	@import "./sass/main";

	#app, nav .container {display:flex}

	#app {
		height:100%;
		flex-direction:column;
		h1 {display:inline-block;margin:0;}
	}

	nav {
		flex-grow:0;
		height:3rem;
		background-color:$color-card;
		.container {
			padding:0 0.75rem;;
			justify-content:space-between;
		}
		a {
			color:$color-main;
			&:hover {color:$color-main;}
		}
	}

	#module-container {
		padding:$container-padding;
		align-items:center;
		flex-grow:1;
		> .columns {width:100%; height:100%;}
	}
</style>