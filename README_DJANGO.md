# 🦅 Águilas Saber - Django

Tu proyecto de Django ha sido creado exitosamente. Aquí está toda la información que necesitas:

## 📋 Información del Proyecto

Este es el equivalente en Django de tu proyecto Laravel. Se han migrado todos los modelos, vistas, rutas y templates.

## 🚀 Cómo Ejecutar el Proyecto

### 1. Activar el Entorno Virtual
```powershell
cd "C:\Users\DELL\Documents\MARILYN SOCOLA\5 NIVEL\VINCULACION\aguilas_saber"
.\django_venv\Scripts\Activate.ps1
```

### 2. Ejecutar el Servidor
```powershell
python manage.py runserver
```

El servidor estará disponible en: **http://localhost:8000**

## 🔐 Credenciales de Acceso

### Login de la Aplicación (http://localhost:8000/login/)
- **Email:** `admin@aguila.com`
- **Contraseña:** `admin123`

### Panel Admin de Django (http://localhost:8000/admin/)
- **Usuario:** `admin`
- **Contraseña:** `admin123`

Para crear nuevos usuarios, accede al sistema con las credenciales de arriba o usa el panel de administración.

## 📁 Estructura del Proyecto

```
aguilas_saber/
├── aguilas_saber/          # Configuración del proyecto
│   ├── settings.py         # Configuración de Django
│   ├── urls.py            # URLs principales
│   ├── wsgi.py
│   └── asgi.py
├── core/                   # Aplicación principal
│   ├── models.py          # Modelos (Usuario, Recurso, Prestamo, Producto, Role)
│   ├── views.py           # Vistas/Controladores
│   ├── urls.py            # URLs de la app
│   ├── admin.py           # Admin panel configuration
│   ├── templates/         # Templates HTML
│   │   ├── login.html
│   │   ├── dashboard.html
│   │   ├── usuarios/
│   │   ├── recursos/
│   │   ├── prestamos/
│   │   └── productos/
│   └── migrations/        # Migraciones de BD
├── db.sqlite3            # Base de datos SQLite
├── manage.py             # Herramienta de administración
└── requirements.txt      # Dependencias
```

## 🗄️ Modelos de Datos

### Usuario
- nombre
- apellido
- email (único)
- telefono
- password
- activo (boolean)
- roles (ManyToMany a Role)

### Recurso
- nombre (único)
- descripcion
- cantidad
- estado (disponible, no_disponible, mantenimiento)

### Producto
- nombre (único)
- estado (entrada, salida, disponible)
- fecha_entrada
- fecha_salida
- cantidad

### Préstamo
- codigo (único)
- usuario (ForeignKey)
- recurso (ForeignKey)
- fecha_prestamo
- fecha_devolucion
- estado (pendiente, entregado, devuelto, cancelado)

### Role
- nombre (único)
- descripcion

## 🛣️ Rutas Disponibles

### Autenticación
- `GET /login/` - Página de login
- `POST /login/` - Procesar login
- `GET /logout/` - Cerrar sesión

### Dashboard
- `GET /` - Dashboard principal
- `GET /dashboard/` - Dashboard (mismo)

### Usuarios
- `GET /usuarios/` - Listar usuarios
- `GET /usuarios/crear/` - Formulario crear usuario
- `POST /usuarios/crear/` - Guardar nuevo usuario
- `GET /usuarios/<id>/editar/` - Editar usuario
- `POST /usuarios/<id>/editar/` - Guardar cambios
- `GET /usuarios/<id>/eliminar/` - Eliminar usuario

### Recursos
- `GET /recursos/` - Listar recursos
- `GET /recursos/crear/` - Formulario crear recurso
- `POST /recursos/crear/` - Guardar nuevo recurso
- `GET /recursos/<id>/editar/` - Editar recurso
- `POST /recursos/<id>/editar/` - Guardar cambios
- `GET /recursos/<id>/eliminar/` - Eliminar recurso

### Préstamos
- `GET /prestamos/` - Listar préstamos
- `GET /prestamos/crear/` - Formulario crear préstamo
- `POST /prestamos/crear/` - Guardar nuevo préstamo
- `GET /prestamos/<id>/editar/` - Editar préstamo
- `POST /prestamos/<id>/editar/` - Guardar cambios
- `GET /prestamos/<id>/eliminar/` - Eliminar préstamo

### Productos
- `GET /productos/` - Listar productos
- `GET /productos/crear/` - Formulario crear producto
- `POST /productos/crear/` - Guardar nuevo producto
- `GET /productos/<id>/editar/` - Editar producto
- `POST /productos/<id>/editar/` - Guardar cambios
- `GET /productos/<id>/eliminar/` - Eliminar producto

## 🎨 Cambiar Contraseña de Admin

```powershell
python manage.py changepassword admin
```

## 📦 Instalar Dependencias Adicionales

Si necesitas instalar más paquetes en el futuro:

```powershell
pip install nombre_paquete
pip freeze > requirements.txt  # Para actualizar el archivo
```

## 🔄 Comandos Útiles

### Crear un nuevo superusuario
```powershell
python manage.py createsuperuser
```

### Hacer migraciones
```powershell
python manage.py makemigrations
python manage.py migrate
```

### Ver información de la BD
```powershell
python manage.py dbshell
```

### Limpiar cache
```powershell
python manage.py clear_cache
```

## 🔒 Autenticación

El sistema usa sesiones de Django. Los usuarios deben hacer login en `/login/` con su email y contraseña.

Para cambiar de usuario personalizado a Django User en el futuro, necesitarás:
1. Crear nuevas migraciones
2. Heredar de AbstractUser en lugar de un modelo personalizado

## 🚀 Próximos Pasos

1. **Cambiar contraseña de admin:**
   ```powershell
   python manage.py changepassword admin
   ```

2. **Crear usuarios de prueba a través del admin:** http://localhost:8000/admin

3. **Personalizaciones:** Puedes editar templates en `core/templates/` para cambiar los estilos

4. **Agregar más campos:** Edita `core/models.py` y crea nuevas migraciones

## 📝 Notas Importantes

- La contraseña se guarda en texto plano en esta versión. Para producción, usa `django.contrib.auth.hashers`
- SQLite es solo para desarrollo. Para producción usa PostgreSQL, MySQL, etc.
- El archivo `db.sqlite3` NO debe subirse a git. Agrega a `.gitignore`

## 💡 Tips

- El login está protegido con `@login_required`
- Todos los formularios usan CSRF protection de Django
- Los templates están listos para personalizar con CSS adicional
- Puedes acceder al admin en `/admin/` con usuario: admin

¡Tu proyecto Django está listo! 🎉
