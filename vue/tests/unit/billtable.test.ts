// tslint:disable: no-unused-expression
import { expect } from 'chai';
import { shallowMount, Wrapper } from '@vue/test-utils';
import BillTable from '../../src/components/bill-viewer/BillTable.vue';
import bills from "./test-data";

describe("BillTable Component", () => {
	let wrapper: Wrapper<BillTable>;

	beforeEach(() => {
		wrapper = shallowMount(BillTable, {propsData: {bills}});
	});

	it("displays list of bills in a table", () => {
		expect(wrapper.findAll("tbody tr").length).to.equal(12);
	});

	it("displays each bill with bill date, bill amount, and action buttons", () => {
		const tableCells = wrapper.find("tbody tr").findAll("td");
		expect(tableCells.at(0).text()).to.equal("Jan 1");
		expect(tableCells.at(1).text()).to.equal("$127.50");
		expect(tableCells.at(2).find(".field.is-grouped").element).to.not.be.undefined;
	});
});
