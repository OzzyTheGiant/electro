export default class HttpService {
	public login(credentials: {username:string, password:string}):boolean {
		// TODO: implement the true Http login api route for this later
		return credentials.username && credentials.password ? true : false;
	}
}
