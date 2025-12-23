import json
from django.shortcuts import render, redirect, get_object_or_404
from django.db.models import Q, Count
from django.db.models.functions import TruncMonth
from .models import Usuario, Recurso, Prestamo, Producto, Role

# ========== AUTENTICACIÓN ==========
def login_view(request):
    """Vista de login"""
    # Si ya está logueado, redirige al dashboard
    if 'usuario_id' in request.session:
        return redirect('dashboard')
    
    if request.method == 'POST':
        email = request.POST.get('email', '').strip()
        password = request.POST.get('password', '').strip()
        
        if not email or not password:
            return render(request, 'login.html', {'error': 'Por favor completa todos los campos'})
        
        try:
            usuario = Usuario.objects.get(email=email, activo=True)
            if usuario.password == password:
                request.session['usuario_id'] = usuario.id
                request.session['usuario_nombre'] = usuario.nombre
                request.session['usuario_apellido'] = usuario.apellido
                request.session['usuario_email'] = usuario.email
                return redirect('dashboard')
            else:
                return render(request, 'login.html', {'error': 'Contraseña incorrecta'})
        except Usuario.DoesNotExist:
            return render(request, 'login.html', {'error': 'El email no está registrado en el sistema'})
    
    return render(request, 'login.html')

def logout_view(request):
    """Vista de logout"""
    request.session.flush()
    return redirect('login')

# ========== HOME ==========
def home(request):
    """Vista de inicio con módulos principales"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    return render(request, 'home.html')

# ========== DASHBOARD ==========
def dashboard(request):
    """Vista del dashboard con estadísticas"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    usuarios_count = Usuario.objects.filter(activo=True).count()
    recursos_count = Recurso.objects.count()
    prestamos_count = Prestamo.objects.count()
    prestamos_pendientes = Prestamo.objects.filter(estado='pendiente').count()
    productos_count = Producto.objects.count()

    # Conteos por estado para recursos
    recursos_estado_qs = Recurso.objects.values('estado').annotate(total=Count('id'))
    recursos_estado = {'disponible': 0, 'no_disponible': 0, 'mantenimiento': 0}
    for item in recursos_estado_qs:
        recursos_estado[item['estado']] = item['total']

    # Conteos por estado para productos
    productos_estado_qs = Producto.objects.values('estado').annotate(total=Count('id'))
    productos_estado = {'entrada': 0, 'salida': 0, 'disponible': 0}
    for item in productos_estado_qs:
        productos_estado[item['estado']] = item['total']

    # Productos por mes (usando created_at)
    productos_mes_qs = (
        Producto.objects
        .annotate(mes=TruncMonth('created_at'))
        .values('mes')
        .annotate(total=Count('id'))
        .order_by('mes')
    )
    productos_mes_labels = [item['mes'].strftime('%b %Y') for item in productos_mes_qs]
    productos_mes_data = [item['total'] for item in productos_mes_qs]

    # Serializar a JSON para el template (evita errores de comillas en JS)
    productos_mes_labels_json = json.dumps(productos_mes_labels)
    productos_mes_data_json = json.dumps(productos_mes_data)
    
    context = {
        'usuarios': usuarios_count,
        'recursos': recursos_count,
        'prestamos': prestamos_count,
        'prestamos_pendientes': prestamos_pendientes,
        'productos': productos_count,
        'usuario_nombre': request.session.get('usuario_nombre', 'Usuario'),
        'recursos_estado': recursos_estado,
        'productos_estado': productos_estado,
        'productos_mes_labels_json': productos_mes_labels_json,
        'productos_mes_data_json': productos_mes_data_json,
    }
    return render(request, 'dashboard.html', context)

# ========== USUARIOS ==========
def usuarios_lista(request):
    """Lista de usuarios"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    usuarios = Usuario.objects.all().order_by('-created_at')
    
    if request.GET.get('q'):
        query = request.GET.get('q')
        usuarios = usuarios.filter(
            Q(nombre__icontains=query) | 
            Q(apellido__icontains=query) | 
            Q(email__icontains=query)
        )
    
    return render(request, 'usuarios/lista.html', {'usuarios': usuarios})

def usuario_crear(request):
    """Crear nuevo usuario"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    roles = Role.objects.all()
    
    if request.method == 'POST':
        nombre = request.POST.get('nombre')
        apellido = request.POST.get('apellido')
        email = request.POST.get('email')
        telefono = request.POST.get('telefono', '')
        password = request.POST.get('password')
        
        if Usuario.objects.filter(email=email).exists():
            return render(request, 'usuarios/crear.html', 
                        {'error': 'El email ya existe', 'roles': roles})
        
        usuario = Usuario.objects.create(
            nombre=nombre,
            apellido=apellido,
            email=email,
            telefono=telefono,
            password=password,
            activo=True
        )
        
        roles_ids = request.POST.getlist('roles')
        if roles_ids:
            usuario.roles.set(roles_ids)
        
        return redirect('usuarios_lista')
    
    return render(request, 'usuarios/crear.html', {'roles': roles})

def usuario_editar(request, id):
    """Editar usuario"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    usuario = get_object_or_404(Usuario, id=id)
    roles = Role.objects.all()
    
    if request.method == 'POST':
        usuario.nombre = request.POST.get('nombre')
        usuario.apellido = request.POST.get('apellido')
        usuario.email = request.POST.get('email')
        usuario.telefono = request.POST.get('telefono', '')
        usuario.activo = request.POST.get('activo') == 'on'
        usuario.save()
        
        roles_ids = request.POST.getlist('roles')
        usuario.roles.set(roles_ids)
        
        return redirect('usuarios_lista')
    
    return render(request, 'usuarios/editar.html', {
        'usuario': usuario,
        'roles': roles,
        'usuario_roles': usuario.roles.all()
    })

def usuario_eliminar(request, id):
    """Eliminar usuario"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    usuario = get_object_or_404(Usuario, id=id)
    usuario.delete()
    return redirect('usuarios_lista')

# ========== RECURSOS ==========
def recursos_lista(request):
    """Lista de recursos"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    recursos = Recurso.objects.all().order_by('-created_at')
    
    if request.GET.get('q'):
        query = request.GET.get('q')
        recursos = recursos.filter(nombre__icontains=query)
    
    if request.GET.get('estado'):
        recursos = recursos.filter(estado=request.GET.get('estado'))
    
    return render(request, 'recursos/lista.html', {'recursos': recursos})

def recurso_crear(request):
    """Crear nuevo recurso"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    if request.method == 'POST':
        nombre = request.POST.get('nombre')
        
        if Recurso.objects.filter(nombre=nombre).exists():
            return render(request, 'recursos/crear.html', 
                        {'error': 'El nombre del recurso ya existe'})
        
        Recurso.objects.create(
            nombre=nombre,
            descripcion=request.POST.get('descripcion', ''),
            cantidad=int(request.POST.get('cantidad', 0)),
            estado=request.POST.get('estado', 'disponible')
        )
        return redirect('recursos_lista')
    
    return render(request, 'recursos/crear.html')

def recurso_editar(request, id):
    """Editar recurso"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    recurso = get_object_or_404(Recurso, id=id)
    
    if request.method == 'POST':
        recurso.nombre = request.POST.get('nombre', recurso.nombre)
        recurso.descripcion = request.POST.get('descripcion', '')
        try:
            recurso.cantidad = int(request.POST.get('cantidad', recurso.cantidad))
        except (ValueError, TypeError):
            recurso.cantidad = recurso.cantidad
        
        estado = request.POST.get('estado', recurso.estado)
        # Validate estado against choices
        valid_estados = ['disponible', 'no_disponible', 'mantenimiento']
        if estado in valid_estados:
            recurso.estado = estado
        
        recurso.save()
        return redirect('recursos_lista')
    
    return render(request, 'recursos/editar.html', {'recurso': recurso})

def recurso_eliminar(request, id):
    """Eliminar recurso"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    recurso = get_object_or_404(Recurso, id=id)
    recurso.delete()
    return redirect('recursos_lista')

# ========== PRÉSTAMOS ==========
def prestamos_lista(request):
    """Lista de préstamos"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    prestamos = Prestamo.objects.all().order_by('-created_at').select_related('usuario', 'recurso')
    
    if request.GET.get('q'):
        query = request.GET.get('q')
        prestamos = prestamos.filter(Q(codigo__icontains=query) | Q(usuario__nombre__icontains=query))
    
    if request.GET.get('estado'):
        prestamos = prestamos.filter(estado=request.GET.get('estado'))
    
    return render(request, 'prestamos/lista.html', {'prestamos': prestamos})

def prestamo_crear(request):
    """Crear nuevo préstamo"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    usuarios = Usuario.objects.filter(activo=True)
    recursos = Recurso.objects.filter(estado='disponible')
    
    if request.method == 'POST':
        codigo = request.POST.get('codigo')
        
        if Prestamo.objects.filter(codigo=codigo).exists():
            return render(request, 'prestamos/crear.html', {
                'error': 'El código del préstamo ya existe',
                'usuarios': usuarios,
                'recursos': recursos
            })
        
        # Validar estado
        estado = request.POST.get('estado', 'pendiente')
        valid_estados = ['pendiente', 'entregado', 'devuelto', 'cancelado']
        if estado not in valid_estados:
            estado = 'pendiente'
        
        Prestamo.objects.create(
            codigo=codigo,
            usuario_id=request.POST.get('usuario_id'),
            recurso_id=request.POST.get('recurso_id'),
            fecha_prestamo=request.POST.get('fecha_prestamo'),
            fecha_devolucion=request.POST.get('fecha_devolucion') or None,
            estado=estado
        )
        return redirect('prestamos_lista')
    
    return render(request, 'prestamos/crear.html', {
        'usuarios': usuarios,
        'recursos': recursos
    })

def prestamo_editar(request, id):
    """Editar préstamo"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    prestamo = get_object_or_404(Prestamo, id=id)
    usuarios = Usuario.objects.filter(activo=True)
    recursos = Recurso.objects.all()
    
    if request.method == 'POST':
        prestamo.codigo = request.POST.get('codigo', prestamo.codigo)
        prestamo.usuario_id = request.POST.get('usuario_id', prestamo.usuario_id)
        prestamo.recurso_id = request.POST.get('recurso_id', prestamo.recurso_id)
        prestamo.fecha_prestamo = request.POST.get('fecha_prestamo', prestamo.fecha_prestamo)
        prestamo.fecha_devolucion = request.POST.get('fecha_devolucion') or None
        
        # Validar estado
        estado = request.POST.get('estado', prestamo.estado)
        valid_estados = ['pendiente', 'entregado', 'devuelto', 'cancelado']
        if estado in valid_estados:
            prestamo.estado = estado
        
        prestamo.save()
        return redirect('prestamos_lista')
    
    return render(request, 'prestamos/editar.html', {
        'prestamo': prestamo,
        'usuarios': usuarios,
        'recursos': recursos
    })

def prestamo_eliminar(request, id):
    """Eliminar préstamo"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    prestamo = get_object_or_404(Prestamo, id=id)
    prestamo.delete()
    return redirect('prestamos_lista')

# ========== PRODUCTOS ==========
def productos_lista(request):
    """Lista de productos"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    productos = Producto.objects.all().order_by('-created_at')
    
    if request.GET.get('q'):
        query = request.GET.get('q')
        productos = productos.filter(nombre__icontains=query)
    
    if request.GET.get('estado'):
        productos = productos.filter(estado=request.GET.get('estado'))
    
    return render(request, 'productos/lista.html', {'productos': productos})

def producto_crear(request):
    """Crear nuevo producto"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    if request.method == 'POST':
        nombre = request.POST.get('nombre')
        
        if Producto.objects.filter(nombre=nombre).exists():
            return render(request, 'productos/crear.html', 
                        {'error': 'El nombre del producto ya existe'})
        
        # Validar estado
        estado = request.POST.get('estado', 'disponible')
        valid_estados = ['entrada', 'salida', 'disponible']
        if estado not in valid_estados:
            estado = 'disponible'
        
        try:
            cantidad = int(request.POST.get('cantidad', 0))
        except (ValueError, TypeError):
            cantidad = 0
        
        Producto.objects.create(
            nombre=nombre,
            estado=estado,
            fecha_entrada=request.POST.get('fecha_entrada'),
            fecha_salida=request.POST.get('fecha_salida') or None,
            cantidad=cantidad
        )
        return redirect('productos_lista')
    
    return render(request, 'productos/crear.html')

def producto_editar(request, id):
    """Editar producto"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    producto = get_object_or_404(Producto, id=id)
    
    if request.method == 'POST':
        producto.nombre = request.POST.get('nombre', producto.nombre)
        
        # Validar estado
        estado = request.POST.get('estado', producto.estado)
        valid_estados = ['entrada', 'salida', 'disponible']
        if estado in valid_estados:
            producto.estado = estado
        
        producto.fecha_entrada = request.POST.get('fecha_entrada', producto.fecha_entrada)
        producto.fecha_salida = request.POST.get('fecha_salida') or None
        
        try:
            producto.cantidad = int(request.POST.get('cantidad', producto.cantidad))
        except (ValueError, TypeError):
            producto.cantidad = producto.cantidad
        
        producto.save()
        return redirect('productos_lista')
    
    return render(request, 'productos/editar.html', {'producto': producto})

def producto_eliminar(request, id):
    """Eliminar producto"""
    if 'usuario_id' not in request.session:
        return redirect('login')
    
    producto = get_object_or_404(Producto, id=id)
    producto.delete()
    return redirect('productos_lista')
