// tslint:disable: no-unused-expression
// tslint:disable: forin
import { expect } from 'chai';
import { shallowMount, Wrapper } from '@vue/test-utils';
import sinon from "sinon";
import BillLineChart from '../../src/components/bill-viewer/BillLineChart.vue';
import bills from "./test-data";

describe("BillLineChart Component", () => {
	const payments:number[] = bills.map(bill => bill.PaymentAmount);

	let wrapper: Wrapper<BillLineChart>;

	const lineStub = {
		methods: { renderChart:sinon.fake() }
	};

	beforeEach(() => {
		wrapper = shallowMount(BillLineChart, {
			propsData: {payments},
			extends:lineStub
		});
	});

	it("initializes with specified payment list", () => {
		expect(lineStub.methods.renderChart.callCount).to.equal(1);
		expect(wrapper.props().chartData.datasets[0].data).to.equal(payments);
	});

	it("updates chart if data has changed", () => {
		lineStub.methods.renderChart.resetHistory(); // ensure that renderChart is called after props update
		wrapper.setProps({payments:[{paymentAmount:127.01, paymentDate: "2019-01-01", id:1}]});
		expect(lineStub.methods.renderChart.callCount).to.equal(1);
	});
});
