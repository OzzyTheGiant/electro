"""App views"""
from rest_framework import viewsets;
from .serializers import UserSerializer, BillSerializer;
from .models import *;

class UserViewSet(viewsets.ModelViewSet):
	"""API endpoint that allows users to be viewed or edited."""
	queryset = User.objects.all();
	serializer_class = UserSerializer;


class BillViewSet(viewsets.ModelViewSet):
	"""API endpoint that allows bills to be viewed or edited."""
	queryset = Bill.objects.all();
	serializer_class = BillSerializer;
