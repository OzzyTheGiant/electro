package electro.models;

import java.io.Serializable;

import javax.persistence.Column;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import com.google.gson.annotations.Expose;

public class User implements Serializable {
	private static transient final long serialVersionUID = -6586257090826961044L;

	@Column(name = "ID")
	@Expose
	private int id;

	@Column(name = "Username")
	@NotNull(message = "Username is required")
	@Size(max = 30)
	@Expose
	private String username;

	@Column(name = "Password")
	@NotNull(message = "Password is required")
	@Size(max = 255)
	@Expose(serialize = false)
	private String password;

	public int getId() {
		return this.id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public String getUsername() {
		return this.username;
	}

	public void setUsername(String username) {
		this.username = username;
	}

	public String getPassword() {
		return this.password;
	}

	public void setPassword(String password) {
		this.password = password;
	}

}