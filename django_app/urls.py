"""electro django_app URL Configuration

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/2.2/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  path('', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  path('', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.urls import include, path
    2. Add a URL to urlpatterns:  path('blog/', include('blog.urls'))
"""

from django.urls import include, path;
from django.conf.urls import handler404;
from rest_framework.routers import DefaultRouter;
from .application import views;
from .application.exceptions import url_not_found_error_handler;

router = DefaultRouter(trailing_slash = False);
router.register(r'bills', views.BillViewSet);

urlpatterns = [
	path("api", views.home),
	path('api/login', views.LoginView.as_view()),
	path('api/logout', views.LogoutView.as_view()),
	path("api/", include(router.urls))
];

handler404 = url_not_found_error_handler;
