# Daytona Ciberconciencia

Aplicación de capacitación sobre phishing. Permite crear una cuenta, iniciar sesión y consultar cursos y sus módulos. El acceso a la pantalla de administración depende del rol asignado a la cuenta.

## Preparar la base de datos en XAMPP

1. Copie la carpeta del proyecto dentro de `C:\xampp\htdocs\` (por ejemplo, `C:\xampp\htdocs\Proyecto-Daytona`).
2. Abra el Panel de Control de XAMPP e inicie **Apache** y **MySQL**.
3. Entre en [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
4. Cree una base de datos llamada `daytona_login`.
5. Seleccione `daytona_login`, abra la pestaña **Importar**, elija el archivo `daytona_login.sql` del proyecto y pulse **Importar** o **Continuar**.
6. Compruebe que se crearon las tablas de usuarios, roles, cursos, módulos y accesos.

## Iniciar la aplicación

Con Apache y MySQL en ejecución, abra `http://localhost/Proyecto-Daytona/login.php` en el navegador. Si ubicó la carpeta con otro nombre dentro de `htdocs`, reemplace `Proyecto-Daytona` en la dirección por ese nombre.

## Registrarse e iniciar sesión

- Desde el login, seleccione **Registrar nuevo usuario**.
- Complete nombre, correo electrónico y contraseña (mínimo 6 caracteres) y pulse **Registrarse**. El correo debe ser único. La cuenta se crea con rol de usuario común y, tras el registro correcto, se vuelve al login.
- Inicie sesión con ese correo y contraseña. Si los datos no coinciden, la aplicación muestra un mensaje de error. Use el control junto a la contraseña para mostrarla u ocultarla.

## Qué muestra cada rol

- **Usuario común:** se dirige al dashboard, que saluda al usuario y lista los cursos disponibles. Al seleccionar **Ingresar al Módulo**, se abre el curso; el menú lateral permite elegir sus módulos y leer el contenido.
- **Administrador:** el login dirige al rol administrador (`id_rol = 1`) a `admin.php`, una pantalla titulada **Registro de accesos** con columnas para fecha y hora, usuario, dirección IP y resultado.

### Probar el acceso de administrador

El archivo SQL incluido asigna el rol común (`id_rol = 2`) a las dos cuentas de ejemplo; no proporciona contraseñas en texto plano para iniciar sesión. Para probar la ruta de administrador, registre una cuenta desde la aplicación y en phpMyAdmin abra la tabla `usuarios` de `daytona_login`. Edite esa cuenta y cambie `id_rol` de `2` a `1`; después inicie sesión con las credenciales elegidas al registrarse.

