from django.db import models

class Role(models.Model):
    nombre = models.CharField(max_length=100, unique=True)
    descripcion = models.TextField(blank=True)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    def __str__(self):
        return self.nombre
    
    class Meta:
        db_table = 'roles'
        verbose_name = 'Rol'
        verbose_name_plural = 'Roles'

class Usuario(models.Model):
    nombre = models.CharField(max_length=100)
    apellido = models.CharField(max_length=100)
    email = models.EmailField(unique=True)
    telefono = models.CharField(max_length=20, blank=True)
    password = models.CharField(max_length=255)
    activo = models.BooleanField(default=True)
    roles = models.ManyToManyField(Role, blank=True, related_name='usuarios')
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    def __str__(self):
        return f"{self.nombre} {self.apellido}"
    
    class Meta:
        db_table = 'usuarios'
        verbose_name = 'Usuario'
        verbose_name_plural = 'Usuarios'

class Recurso(models.Model):
    ESTADO_CHOICES = [
        ('disponible', 'Disponible'),
        ('no_disponible', 'No disponible'),
        ('mantenimiento', 'Mantenimiento'),
    ]
    
    nombre = models.CharField(max_length=255, unique=True)
    descripcion = models.TextField(blank=True)
    cantidad = models.IntegerField(default=0)
    estado = models.CharField(max_length=50, choices=ESTADO_CHOICES, default='disponible')
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    def __str__(self):
        return self.nombre
    
    class Meta:
        db_table = 'recursos'
        verbose_name = 'Recurso'
        verbose_name_plural = 'Recursos'

class Producto(models.Model):
    ESTADO_CHOICES = [
        ('entrada', 'Entrada'),
        ('salida', 'Salida'),
        ('disponible', 'Disponible'),
    ]
    
    nombre = models.CharField(max_length=255, unique=True)
    estado = models.CharField(max_length=50, choices=ESTADO_CHOICES)
    fecha_entrada = models.DateField()
    fecha_salida = models.DateField(null=True, blank=True)
    cantidad = models.IntegerField(default=0)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    def __str__(self):
        return self.nombre
    
    class Meta:
        db_table = 'productos'
        verbose_name = 'Producto'
        verbose_name_plural = 'Productos'

class Prestamo(models.Model):
    ESTADO_CHOICES = [
        ('pendiente', 'Pendiente'),
        ('entregado', 'Entregado'),
        ('devuelto', 'Devuelto'),
        ('cancelado', 'Cancelado'),
    ]
    
    codigo = models.CharField(max_length=100, unique=True)
    usuario = models.ForeignKey(Usuario, on_delete=models.CASCADE, related_name='prestamos')
    recurso = models.ForeignKey(Recurso, on_delete=models.CASCADE, related_name='prestamos')
    fecha_prestamo = models.DateField()
    fecha_devolucion = models.DateField(null=True, blank=True)
    estado = models.CharField(max_length=50, choices=ESTADO_CHOICES, default='pendiente')
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    
    def __str__(self):
        return f"Préstamo {self.codigo}"
    
    class Meta:
        db_table = 'prestamos'
        verbose_name = 'Préstamo'
        verbose_name_plural = 'Préstamos'
