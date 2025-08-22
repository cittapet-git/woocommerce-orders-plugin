# Contributing to WooCommerce Real-Time Orders Plugin

¡Gracias por tu interés en contribuir a nuestro plugin! Este documento te guiará a través del proceso de contribución.

## 🚀 Cómo Contribuir

### 1. Reportar Bugs
- Usa el [sistema de issues de GitHub](https://github.com/cittapet-git/woocommerce-real-time-orders-plugin/issues)
- Incluye información detallada sobre el problema
- Describe los pasos para reproducir el bug
- Incluye información del sistema (WordPress, WooCommerce, PHP versiones)

### 2. Solicitar Nuevas Funcionalidades
- Crea un issue con la etiqueta "enhancement"
- Describe la funcionalidad que te gustaría ver
- Explica por qué sería útil
- Incluye ejemplos de uso si es posible

### 3. Contribuir Código
- Haz fork del repositorio
- Crea una rama para tu feature/fix
- Haz commit de tus cambios
- Crea un Pull Request

## 🛠️ Configuración del Entorno de Desarrollo

### Requisitos
- PHP 7.4+
- WordPress 5.0+
- WooCommerce 3.0+
- Git
- Composer (opcional)

### Instalación Local
```bash
# Clonar el repositorio
git clone https://github.com/cittapet-git/woocommerce-real-time-orders-plugin.git

# Entrar al directorio
cd woocommerce-real-time-orders-plugin

# Instalar dependencias (si usas Composer)
composer install
```

## 📝 Estándares de Código

### PHP
- Sigue los [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Usa espacios en lugar de tabs
- Indentación de 4 espacios
- Nombres de funciones en snake_case
- Nombres de clases en PascalCase

### JavaScript
- Sigue el [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)
- Usa 2 espacios para indentación
- Nombres de variables y funciones en camelCase
- Comenta tu código cuando sea necesario

### CSS
- Usa 2 espacios para indentación
- Nombres de clases en kebab-case
- Organiza las propiedades lógicamente
- Comenta secciones importantes

## 🔧 Estructura del Proyecto

```
woocommerce-real-time-orders-plugin/
├── my-real-time-orders-plugin.php    # Archivo principal del plugin
├── real-time-orders.js               # JavaScript del frontend
├── assets/                           # Archivos estáticos
│   ├── css/                         # Estilos CSS
│   └── js/                          # JavaScript del admin
├── languages/                        # Archivos de traducción
├── .github/                          # GitHub Actions y configuraciones
├── composer.json                     # Dependencias de Composer
├── LICENSE                           # Licencia MIT
├── CHANGELOG.md                      # Historial de cambios
└── CONTRIBUTING.md                   # Esta guía
```

## 🧪 Testing

### Antes de Enviar un PR
- [ ] El código funciona en WordPress 5.0+
- [ ] El código funciona con WooCommerce 3.0+
- [ ] No hay errores PHP en el log
- [ ] El JavaScript funciona sin errores en la consola
- [ ] Los shortcodes funcionan correctamente
- [ ] El panel de administración funciona
- [ ] Las actualizaciones en tiempo real funcionan

### Testing Manual
1. Instala el plugin en un WordPress local
2. Activa WooCommerce
3. Crea algunas órdenes de prueba
4. Usa el shortcode en una página
5. Verifica que las órdenes se muestren
6. Verifica que las actualizaciones funcionen
7. Prueba los filtros y funcionalidades

## 📋 Proceso de Pull Request

### 1. Crear la Rama
```bash
git checkout -b feature/nombre-de-la-funcionalidad
# o
git checkout -b fix/nombre-del-fix
```

### 2. Hacer Cambios
- Escribe código limpio y bien documentado
- Incluye comentarios cuando sea necesario
- Sigue los estándares de codificación
- Haz commits pequeños y descriptivos

### 3. Commit y Push
```bash
git add .
git commit -m "feat: agregar nueva funcionalidad X"
git push origin feature/nombre-de-la-funcionalidad
```

### 4. Crear Pull Request
- Ve a GitHub y crea un nuevo Pull Request
- Describe los cambios realizados
- Incluye información sobre testing
- Menciona si hay breaking changes

## 🏷️ Convenciones de Commits

Usamos [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` Nueva funcionalidad
- `fix:` Corrección de bug
- `docs:` Cambios en documentación
- `style:` Cambios de formato (no afectan funcionalidad)
- `refactor:` Refactorización de código
- `test:` Agregar o modificar tests
- `chore:` Cambios en build o herramientas

### Ejemplos
```bash
git commit -m "feat: agregar filtro por fecha de orden"
git commit -m "fix: corregir error en actualización AJAX"
git commit -m "docs: actualizar README con nuevos parámetros"
```

## 🐛 Reportar Bugs

### Información Requerida
- **Versión de WordPress:** X.X.X
- **Versión de WooCommerce:** X.X.X
- **Versión de PHP:** X.X.X
- **Tema activo:** Nombre del tema
- **Plugins activos:** Lista de plugins
- **Descripción del problema:** Explicación detallada
- **Pasos para reproducir:** Lista numerada de pasos
- **Comportamiento esperado:** Qué debería pasar
- **Comportamiento actual:** Qué está pasando
- **Capturas de pantalla:** Si es relevante
- **Logs de error:** Si hay errores en el log

## 💡 Sugerencias de Mejora

### Antes de Implementar
- [ ] Verifica que la funcionalidad no exista ya
- [ ] Discute la idea en un issue primero
- [ ] Asegúrate de que sea útil para la comunidad
- [ ] Considera el impacto en el rendimiento

### Implementación
- [ ] Mantén la compatibilidad hacia atrás
- [ ] Agrega tests si es posible
- [ ] Actualiza la documentación
- [ ] Incluye ejemplos de uso

## 🤝 Código de Conducta

- Sé respetuoso con otros contribuyentes
- Mantén las discusiones constructivas
- Ayuda a otros cuando puedas
- Reporta comportamiento inapropiado a los maintainers

## 📞 Contacto

- **Issues:** [GitHub Issues](https://github.com/cittapet-git/woocommerce-real-time-orders-plugin/issues)
- **Discusiones:** [GitHub Discussions](https://github.com/cittapet-git/woocommerce-real-time-orders-plugin/discussions)
- **Email:** [Tu email si quieres compartirlo]

## 🙏 Agradecimientos

Gracias por contribuir a hacer este plugin mejor para toda la comunidad de WordPress y WooCommerce.

---

**Nota:** Esta guía está en constante evolución. Si tienes sugerencias para mejorarla, ¡crea un issue o un PR! 