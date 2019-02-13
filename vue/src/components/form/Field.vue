<template>
	<div class="field">
		<label :for="name" class="label">{{ placeholder }}</label>
		<div class="control" v-if="type !== 'textarea'">
			<input :id="name" :type="type" class="input" :placeholder="placeholder" :value="value" @input="updateValue"/>
		</div>
		<div class="control" v-else-if="type === 'textarea'">
			<textarea :id="name" :placeholder="placeholder" :value="value" @input="updateValue"></textarea>
		</div>
	</div>
</template>

<script lang="ts">
import { Component, Vue, Prop, Watch } from "vue-property-decorator";

@Component
export default class Field extends Vue {
	@Prop() public name!:string;
	@Prop({default:""}) public value!:string;
	@Prop({default:"text"}) public type!:string;
	@Prop({default:""}) public placeholder!:string;

	public updateValue(event:any):void {
		this.$emit('input', event.target.value); 
	}
}
</script>

<style scoped lang="scss"></style>