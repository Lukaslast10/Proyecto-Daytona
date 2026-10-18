# Flujo que va a seguir el sistema
    Usuario normal
    se registra
    inicia sesión
    entra a su panel o cursos
# Admin
    inicia sesión y si en la base figura que tiene rol de admin. le muestra los logs
    puede ver registros de accesos
    puede revisar logs
    después podés agregar más funciones
# ¿Qué hace cada tabla exactamente?
    roles: dice qué tipo de usuario es
    usuarios: guarda la información del usuario
    accesos: guarda el historial de login
  
# Tablas:
1. Roles Columnas: id_rol, nombre_rol

2. Usuarios columnas: columnas id_usuario, nombre_usuario, correo_electronico, clave, id_rol, fecha_registro

3. accesos: id_acceso,id_usuario,fecha_acceso,ip_usuario,resultado

# Cuales son los primeros pasos.
1. Relevar los requerimientos (En este caso es el login y que muestre en la pagina el contenido dependiendo el usuario logueado. Como va hacer la BD, que datos va a guardar, el flujo de la aplicacion (Que pasa si hago esto, que pasa si hago lo otro)
2. Prototipo (te da una vision general de como se ve y que tipos de datos entran y salen de la aplicacion)
3. Una vez que tengamos todo esto claro, recien empezar a codear y a unir las cosas.
4. Importante segun el profe: todo lo que agreguemos tiene que ser funcional sino resta
