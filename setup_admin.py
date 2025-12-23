from django.contrib.auth.models import User

# Crear o actualizar el usuario admin
try:
    user = User.objects.get(username='admin')
    user.set_password('admin123')
    user.save()
    print('✓ Contraseña actualizada para usuario: admin')
except User.DoesNotExist:
    user = User.objects.create_superuser('admin', 'admin@aguila.com', 'admin123')
    print('✓ Usuario admin creado exitosamente')

print('')
print('=' * 50)
print('CREDENCIALES DE ACCESO AL PANEL ADMIN')
print('=' * 50)
print('URL: http://localhost:8000/admin')
print('Usuario: admin')
print('Contraseña: admin123')
print('=' * 50)
