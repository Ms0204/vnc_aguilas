from django.urls import path
from . import views

urlpatterns = [
    # Autenticación
    path('login/', views.login_view, name='login'),
    path('logout/', views.logout_view, name='logout'),
    
    # Home e Inicio
    path('', views.home, name='home'),
    path('dashboard/', views.dashboard, name='dashboard'),
    
    # Usuarios
    path('usuarios/', views.usuarios_lista, name='usuarios_lista'),
    path('usuarios/crear/', views.usuario_crear, name='usuario_crear'),
    path('usuarios/<int:id>/editar/', views.usuario_editar, name='usuario_editar'),
    path('usuarios/<int:id>/eliminar/', views.usuario_eliminar, name='usuario_eliminar'),
    
    # Recursos
    path('recursos/', views.recursos_lista, name='recursos_lista'),
    path('recursos/crear/', views.recurso_crear, name='recurso_crear'),
    path('recursos/<int:id>/editar/', views.recurso_editar, name='recurso_editar'),
    path('recursos/<int:id>/eliminar/', views.recurso_eliminar, name='recurso_eliminar'),
    
    # Préstamos
    path('prestamos/', views.prestamos_lista, name='prestamos_lista'),
    path('prestamos/crear/', views.prestamo_crear, name='prestamo_crear'),
    path('prestamos/<int:id>/editar/', views.prestamo_editar, name='prestamo_editar'),
    path('prestamos/<int:id>/eliminar/', views.prestamo_eliminar, name='prestamo_eliminar'),
    
    # Productos
    path('productos/', views.productos_lista, name='productos_lista'),
    path('productos/crear/', views.producto_crear, name='producto_crear'),
    path('productos/<int:id>/editar/', views.producto_editar, name='producto_editar'),
    path('productos/<int:id>/eliminar/', views.producto_eliminar, name='producto_eliminar'),
    
    # Roles
    path('roles/', views.roles_lista, name='roles_lista'),
    path('roles/crear/', views.role_crear, name='role_crear'),
    path('roles/<int:id>/editar/', views.role_editar, name='role_editar'),
    path('roles/<int:id>/eliminar/', views.role_eliminar, name='role_eliminar'),
]
