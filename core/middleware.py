from django.shortcuts import redirect
from django.urls import resolve

class SessionCheckMiddleware:
    """Middleware para verificar sesión personalizada"""
    
    def __init__(self, get_response):
        self.get_response = get_response
        # Rutas públicas que no necesitan autenticación
        self.public_routes = [
            'login',
            'login_view',
        ]
    
    def __call__(self, request):
        # Obtener el nombre de la vista actual
        try:
            view_name = resolve(request.path).url_name
        except:
            view_name = None
        
        # Si no está autenticado y no está en una ruta pública
        if 'usuario_id' not in request.session and view_name not in self.public_routes:
            # Si no es el login, redirige
            if view_name != 'login':
                return redirect('login')
        
        response = self.get_response(request)
        return response
