<template>
	<div class="field">
		<label :for="name" class="label">{{ labelText || placeholder }}</label>
		<div class="control" v-if="type !== 'textarea'">
			<!-- currency field, made with v-money -->
			<input v-if="type === 'currency'" v-money="currencySettings" :id="name" type="text" class="input" placeholder="$0.00" :value="value" @input="updateValue"/>
			<!-- datepicker field, made with v-calendar -->
			<input v-else-if="type === 'datepicker'" :id="name" class="input" :value="value"  type="text" placeholder="MM/DD/YYYY"/>
			<!-- other specified html input field -->
			<input v-else :id="name" :type="type" class="input" :placeholder="placeholder" :value="value" @input="updateValue"/>
		</div>
		<div class="control" v-else-if="type === 'textarea'">
			<textarea :id="name" :placeholder="placeholder" :value="value" @input="updateValue"></textarea>
		</div>
	</div>
</template>

<script lang="ts">
import { Component, Vue, Prop, Watch } from "vue-property-decorator";
import { VMoney } from 'v-money';

@Component({directives:{money:VMoney}})
export default class Field extends Vue {
	@Prop() public name!:string;
	@Prop({default:""}) public value!:string;
	@Prop({default:"text"}) public type!:string;
	@Prop({default:""}) public placeholder!:string;
	@Prop() public labelText!:string;

	public currencySettings = {prefix:"$"} // for use with currency field by v-money

	public updateValue(event:any):void {
		this.$emit('input', event.target.value); 
	}
}
</script>

<style scoped lang="scss"></style>