// tslint:disable: no-unused-expression
import { expect } from 'chai';
import { shallowMount, Wrapper } from '@vue/test-utils';
import App from '../../src/App.vue';
import Login from "../../src/components/Login.vue";
import DashBoard from "../../src/components/DashBoard.vue";

describe("App Component", () => {
	let wrapper: Wrapper<App>;
	let loginComponent: HTMLElement;

	beforeEach(() => {
		wrapper = shallowMount(App, {
			stubs:{
				login:Login,
				dashboard:DashBoard
			}
		});
		loginComponent = wrapper.find(Login).element;
	});

	it("renders Login component only when app launches", () => {
		expect(loginComponent).to.not.be.undefined;
		expect(wrapper.find(DashBoard).element).to.be.undefined;
	});

	it("renders dashboard component after successful login", () => {
		wrapper.find(Login).vm.$data.credentials.username = "Ozzy";
		wrapper.find(Login).vm.$data.credentials.password = "notarealpassword";
		wrapper.find("form").trigger('submit');
		expect(wrapper.find(Login).element).to.be.undefined;
		expect(wrapper.find(DashBoard).element).to.not.be.undefined;
	});
});
