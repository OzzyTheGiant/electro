"""App serializers"""
from rest_framework import serializers
from electro.models import Bill, User


class UserSerializer(serializers.ModelSerializer):
    """JSON serializer for User model"""

    class Meta:
        model = User
        fields = ("id", "username")


class BillSerializer(serializers.ModelSerializer):
    """JSON serializer for Bill model"""

    class Meta:
        model = Bill
        fields = ("id", "user_id", "payment_amount", "payment_date")


    def to_representation(self, instance):
        data = super().to_representation(instance)
        data["payment_amount"] = float(data["payment_amount"])
        return data
