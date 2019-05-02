"""App serializers"""
from rest_framework import serializers;
from .models import *;

class UserSerializer(serializers.ModelSerializer):
	"""JSON serializer for User model"""
	class Meta:
		model = User;
		fields = ('ID', 'Username');


class BillSerializer(serializers.ModelSerializer):
	"""JSON serializer for Bill model"""
	class Meta:
		model = Bill;
		fields = ('ID', 'User', 'PaymentAmount', 'PaymentDate');
