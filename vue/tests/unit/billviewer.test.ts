// tslint:disable: no-unused-expression
// tslint:disable: max-classes-per-file
import { expect } from 'chai';
import { shallowMount, Wrapper } from '@vue/test-utils';
import BillViewer from '../../src/components/BillViewer.vue';
import bills from "./test-data";

describe("BillViewer Component", () => {
	let wrapper: Wrapper<BillViewer>;

	beforeEach(() => {
		wrapper = shallowMount(BillViewer, {
			propsData: { bills },
			stubs:["bill-table", "bill-line-chart"]
		});
	});

	it("renders BillTable component on mount", () => {
		expect(wrapper.vm.$data.displayChart).to.be.false;
		expect(wrapper.vm.$data.initChart).to.be.false;
		expect(wrapper.find("bill-table-stub").element).to.not.be.undefined;
		expect(wrapper.find("bill-line-chart-stub").element).to.be.undefined;
	});

	it('toggles component visiblity when clicking toggle button', () => {
		wrapper.find(".button.level-right").element.click();
		expect(wrapper.vm.$data.displayChart).to.be.true;
		expect(wrapper.vm.$data.initChart).to.be.true;
		expect(wrapper.find("bill-table-stub").element.style.display).to.equal("none");
		expect(wrapper.find("bill-line-chart-stub").element.style.display).to.equal(""); // display:none removed
	});
});
