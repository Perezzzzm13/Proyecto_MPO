# Guia de instalacion y configuracion - GymTracker

## 1. Requisitos previos

Para instalar y ejecutar el proyecto GymTracker es necesario tener instalado el siguiente software:

- XAMPP, que incluye Apache, PHP y MySQL.
- Un navegador web actualizado, como Google Chrome, Firefox o Microsoft Edge.
- Un editor de codigo, como Visual Studio Code o NetBeans.
- Git, en caso de querer clonar el proyecto desde un repositorio.

## 2. Ubicacion del proyecto

El proyecto debe colocarse dentro de la carpeta `htdocs` de XAMPP.

Por ejemplo:

```text
C:\xampp\htdocs\GymTracker
```

Si el proyecto se descarga comprimido en formato `.zip`, primero se debe descomprimir y despues copiar la carpeta completa dentro de `htdocs`.

## 3. Puesta en marcha de XAMPP

Para ejecutar la aplicacion en local hay que abrir el panel de control de XAMPP e iniciar los siguientes servicios:

- Apache
- MySQL

Apache se encarga de servir la aplicacion web y MySQL permite utilizar la base de datos del proyecto.

## 4. Creacion de la base de datos

Una vez iniciado MySQL, se debe acceder a phpMyAdmin desde el navegador:

```text
http://localhost/phpmyadmin
```

Dentro de phpMyAdmin hay que crear una nueva base de datos para el proyecto. El nombre recomendado es:

```text
gymtracker
```

Despues, se deben importar los archivos SQL incluidos en la carpeta `database` del proyecto.

El orden recomendado es:

1. `database/gymtracker-tables.sql`
2. `database/inserts_gymtracker.sql`

El primer archivo crea las tablas necesarias y el segundo introduce datos iniciales de prueba.

## 5. Configuracion de la conexion a la base de datos

La conexion a la base de datos se configura en el archivo:

```text
backend/config/database.php
```

En un entorno local con XAMPP, los datos habituales de conexion son:

```php
$host = "localhost";
$dbname = "gymtracker";
$username = "root";
$password = "";
```

Estos datos pueden variar si se ha cambiado la configuracion de MySQL. El nombre de la base de datos debe coincidir con el creado en phpMyAdmin.

## 6. Acceso a la aplicacion

Cuando Apache y MySQL esten iniciados y la base de datos este importada, se puede acceder a la aplicacion desde el navegador mediante la siguiente URL:

```text
http://localhost/GymTracker/
```

Desde esta pantalla el usuario puede iniciar sesion o registrarse, segun los datos disponibles en la base de datos.

## 7. Configuracion de la API externa de ejercicios

El proyecto utiliza una API externa para buscar ejercicios. La clave de la API se encuentra configurada en:

```text
backend/exercises/search.php
```

En caso de cambiar de cuenta o de clave, se debe modificar el valor de la variable:

```php
$apiKey = "TU_CLAVE_DE_API";
```

Si la API externa no responde, la aplicacion dispone de una lista local de ejercicios para que la busqueda siga funcionando de forma basica.

## 8. Despliegue en un servidor externo

Para desplegar el proyecto en un hosting como InfinityFree, se deben realizar estos pasos:

1. Subir todos los archivos del proyecto al servidor mediante el gestor de archivos o un cliente FTP.
2. Crear una base de datos MySQL desde el panel del hosting.
3. Importar los archivos SQL de la carpeta `database`.
4. Modificar `backend/config/database.php` con los datos proporcionados por el hosting.
5. Comprobar que la aplicacion carga correctamente desde la URL publica.

En produccion, los datos de conexion suelen ser diferentes a los usados en local, por lo que es importante revisar el nombre del servidor, el usuario, la contrasena y el nombre de la base de datos.

## 9. Comprobaciones finales

Tras completar la instalacion, se recomienda comprobar lo siguiente:

- La pagina principal carga correctamente.
- El registro de usuarios funciona.
- El inicio de sesion permite acceder al panel principal.
- Se pueden crear, editar y eliminar rutinas.
- Se pueden buscar ejercicios y anadirlos a una rutina.
- Se pueden iniciar y guardar sesiones de entrenamiento.
- El usuario administrador puede gestionar usuarios si tiene permisos.

Si alguna funcionalidad no funciona, se debe revisar primero que Apache y MySQL esten iniciados, que la base de datos este importada correctamente y que los datos de conexion sean correctos.
