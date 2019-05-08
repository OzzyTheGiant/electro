package electro.models;

import javax.persistence.Column;

public class Bill {
	@Column(name = "ID") private int id;
	@Column(name = "User") private int user;
	@Column(name = "PaymentAmount") private double paymentAmount;
	@Column(name = "PaymentDate") private String paymentDate;

	public int getId() {
		return this.id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public int getUser() {
		return this.user;
	}

	public void setUser(int user) {
		this.user = user;
	}

	public double getPaymentAmount() {
		return this.paymentAmount;
	}

	public void setPaymentAmount(double paymentAmount) {
		this.paymentAmount = paymentAmount;
	}

	public String getPaymentDate() {
		return this.paymentDate;
	}

	public void setPaymentDate(String paymentDate) {
		this.paymentDate = paymentDate;
	}

	public Bill id(int id) {
		this.id = id;
		return this;
	}

	public Bill user(int user) {
		this.user = user;
		return this;
	}

	public Bill paymentAmount(double paymentAmount) {
		this.paymentAmount = paymentAmount;
		return this;
	}

	public Bill paymentDate(String paymentDate) {
		this.paymentDate = paymentDate;
		return this;
	}
	
}