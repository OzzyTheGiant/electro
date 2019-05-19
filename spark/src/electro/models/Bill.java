package electro.models;

import java.math.BigDecimal;
import java.sql.Date;
import javax.persistence.Column;
import javax.validation.constraints.DecimalMax;
import javax.validation.constraints.DecimalMin;
import javax.validation.constraints.Min;
import javax.validation.constraints.NotNull;

public class Bill {
	@Column(name = "ID") 
	private int id;

	@Column(name = "User")
	@NotNull(message = "User cannot be empty")
	@Min(value = 1, message = "User cannot be empty")
	private int user;

	@Column(name = "PaymentAmount") 
	@NotNull(message = "Payment amount cannot be empty")
	@DecimalMin(value = "0.01", message = "Payment amount must be greater than $0")
	@DecimalMax(value = "99999.99", message = "Payment amount must be less than $99999.99")
	private BigDecimal paymentAmount;

	@Column(name = "PaymentDate") 
	@NotNull(message = "Payment date cannot be empty")
	private Date paymentDate;

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