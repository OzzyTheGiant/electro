// tslint:disable: no-unused-expression
import { expect } from 'chai';
import { shallowMount, Wrapper } from '@vue/test-utils';
import DashBoard from '../../src/components/DashBoard.vue';

describe("DashBoard Component", () => {
	let wrapper: Wrapper<DashBoard>;

	beforeEach(() => {
		wrapper = shallowMount(DashBoard, {
			stubs:["bill-form", "bill-viewer"]
		});
	});

	it("renders BillForm and BillViewer components", () => {
		expect(wrapper.find("bill-form-stub").element).to.not.be.undefined;
		expect(wrapper.find("bill-viewer-stub").element).to.not.be.undefined;
	});
});
