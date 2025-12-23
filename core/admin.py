from django.contrib import admin
from .models import Usuario, Recurso, Prestamo, Producto, Role

@admin.register(Usuario)
class UsuarioAdmin(admin.ModelAdmin):
    list_display = ('nombre', 'apellido', 'email', 'activo', 'created_at')
    search_fields = ('nombre', 'apellido', 'email')
    list_filter = ('activo', 'created_at')
    readonly_fields = ('created_at', 'updated_at')

@admin.register(Recurso)
class RecursoAdmin(admin.ModelAdmin):
    list_display = ('nombre', 'cantidad', 'estado', 'created_at')
    search_fields = ('nombre',)
    list_filter = ('estado', 'created_at')
    readonly_fields = ('created_at', 'updated_at')

@admin.register(Prestamo)
class PrestamoAdmin(admin.ModelAdmin):
    list_display = ('codigo', 'usuario', 'recurso', 'estado', 'fecha_prestamo')
    search_fields = ('codigo', 'usuario__nombre', 'recurso__nombre')
    list_filter = ('estado', 'fecha_prestamo', 'created_at')
    readonly_fields = ('created_at', 'updated_at')

@admin.register(Producto)
class ProductoAdmin(admin.ModelAdmin):
    list_display = ('nombre', 'cantidad', 'estado', 'fecha_entrada')
    search_fields = ('nombre',)
    list_filter = ('estado', 'fecha_entrada', 'created_at')
    readonly_fields = ('created_at', 'updated_at')

@admin.register(Role)
class RoleAdmin(admin.ModelAdmin):
    list_display = ('nombre', 'created_at')
    search_fields = ('nombre',)
    readonly_fields = ('created_at', 'updated_at')
