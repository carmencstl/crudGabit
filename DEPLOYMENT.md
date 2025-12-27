# Deployment Guide - CRUD Gabit

## Estructura de archivos

```
crudGabit/
├── public/          # Raíz web (aquí están CSS, JS, imágenes)
│   ├── index.php    # Entry point
│   ├── router.php   # Router para PHP built-in server
│   ├── .htaccess    # Para Apache local
│   └── css/
│       └── styles.css
├── Controladores/
├── Modelos/
├── Vistas/
└── .htaccess        # Redirección a public/ (solo Apache local)
```

## Configuración por entorno

### Railway (Producción)
- **Servidor**: PHP built-in server
- **Raíz web**: `/public`
- **basePath**: `""` (vacío, detectado automáticamente)
- **Archivos estáticos**: Servidos por `router.php`

### Local (XAMPP/WAMP)
- **Servidor**: Apache
- **Raíz web**: `/htdocs/crudGabit`
- **basePath**: `/crudGabit` (detectado automáticamente)
- **Archivos estáticos**: Servidos por `.htaccess`

## Cómo funciona

1. **Railway** detecta la variable `RAILWAY_ENVIRONMENT` y usa basePath vacío
2. **Local** no tiene esa variable, así que usa basePath `/crudGabit`
3. Las plantillas Twig usan `{{ basePath }}/css/styles.css`
4. Resultado:
   - Railway: `/css/styles.css`
   - Local: `/crudGabit/css/styles.css`

## Variables de entorno

Opcionalmente puedes configurar en Railway:

```
BASE_PATH=
```

(déjalo vacío)
