package electro.models;

import java.math.BigDecimal;
import java.sql.Date;
import javax.persistence.Column;

public class Bill {
	@Column(name = "ID") private int id;
	@Column(name = "User") private int user;
	@Column(name = "PaymentAmount") private BigDecimal paymentAmount;
	@Column(name = "PaymentDate") private Date paymentDate;

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

	public BigDecimal getPaymentAmount() {
		return this.paymentAmount;
	}

	public void setPaymentAmount(BigDecimal paymentAmount) {
		this.paymentAmount = paymentAmount;
	}

	public Date getPaymentDate() {
		return this.paymentDate;
	}

	public void setPaymentDate(Date paymentDate) {
		this.paymentDate = paymentDate;
	}
}