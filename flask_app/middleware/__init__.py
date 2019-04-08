import json;
from flask import request, session;

def manage_session():
	# # None is passed in order to generate session key, rather than giving a string key
	# session_id = session_store.put(None, json.dumps({"current_user":None}).encode('utf-8'));

	# # create session cookie if does not exist
	# try:
	# 	session_cookie_value = request.cookies.get("electro");
	# except KeyError:
	# 	session_store.
	if not session["current_user"]:
		session["current_user"] = None;