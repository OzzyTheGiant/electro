import { expect } from 'chai';
import { shallowMount, Wrapper } from '@vue/test-utils';
import Field from '../../src/components/form/Field.vue';

describe('Field Component', () => {
	const textProps = {
		name:"app-field",
		value:"Example",
		placeholder:"Enter data here",
		labelText:"Data:"
	};

	const datePickerProps = {
		...textProps,
		type:"datepicker",
		value:"January 1, 2019"
	};

	const currencyProps = {
		...textProps,
		type:"currency",
		value:23450
	};

	const textareaProps = {
		...textProps,
		type:"textarea"
	};

	let wrapper: Wrapper<Field>;
	let inputField:HTMLInputElement;
	let label:HTMLLabelElement;

	beforeEach(() => {
		wrapper = shallowMount(Field, {propsData:textProps,});
		inputField = wrapper.find(`#${textProps.name}`).element as HTMLInputElement;
		label = wrapper.find("label").element as HTMLLabelElement;
	});

	it('displays as a default text field with default attributes', () => {
		expect(inputField.value).to.equal(textProps.value);
		expect(inputField.getAttribute("placeholder")).to.equal(textProps.placeholder);
		expect(label.getAttribute("for")).to.equal(textProps.name);
	});

	it('displays as a "text" type field when type is "datepicker"', () => {
		wrapper.setProps(datePickerProps);
		expect(inputField.getAttribute('type')).to.equal('text'); // datepickers are 'text' type
		expect(inputField.value).to.equal(datePickerProps.value);
	});

	it('displays as a "text" type field and converts decimal to currency format when type is "currency"', () => {
		wrapper.setProps(currencyProps);
		expect(inputField.getAttribute('type')).to.equal("text");
		expect(inputField.value).to.equal("$234.50"); // v-money directive will format as decimal based on number of digits
	});

	it('displays textarea if type is "textarea"', () => {
		wrapper.setProps(textareaProps);
		const textarea = wrapper.find('textarea').element as HTMLTextAreaElement;
		expect(textarea.value).to.equal("Example");
	});

	it('should emit updated value when input value changes', () => {
		inputField.value = "New Value";
		inputField.dispatchEvent(new Event("input"));
		expect(wrapper.emitted().input[0][0]).to.equal("New Value");
	});
});
