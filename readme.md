# Documentación del Proyecto - API de Gestión de Centros Cívicos

## Índice

1. [Introducción](#1-introducción)  
2. [¿Qué es una API?](#2-qué-es-una-api)  
3. [Objetivo de la API](#3-objetivo-de-la-api)  
4. [Tecnologías utilizadas](#4-tecnologías-utilizadas)  
   - [Librerías externas](#41-librerías-utilizadas)  
5. [Estructura y funcionamiento del proyecto](#5-estructura-y-funcionamiento-del-proyecto)  
6. [Endpoints definidos](#6-endpoints-definidos)  
   - [Usuarios (público)](#usuarios-público)  
   - [Usuario (privado)](#usuario-privado)  
   - [Centros cívicos (público)](#centros-cívicos-público)  
   - [Instalaciones (público)](#instalaciones-público)  
   - [Actividades (público)](#actividades-público)  
   - [Reservas (privado)](#reservas-privado)  
   - [Inscripciones (privado)](#inscripciones-privado)  
7. [Cliente web con React](#7-cliente-web-con-react)  

---

## 1. Introducción

Este proyecto consiste en el desarrollo de una API REST que permite gestionar un sistema de centros cívicos. La API está construida en PHP nativo y permite el acceso a diferentes funcionalidades públicas y privadas según el rol del usuario.

---

## 2. ¿Qué es una API?

Una API (Application Programming Interface) es un conjunto de reglas y protocolos que permite que diferentes aplicaciones se comuniquen entre sí. En el contexto web, una API REST permite exponer y consumir datos mediante peticiones HTTP como GET, POST, PUT y DELETE.

---

## 3. Objetivo de la API

El objetivo de esta API es facilitar la gestión de centros cívicos, permitiendo a los usuarios consultar información de centros, actividades e instalaciones, así como gestionar sus reservas e inscripciones a actividades.

---

## 4. Tecnologías utilizadas

- **PHP nativo**: Se ha utilizado PHP sin frameworks para tener un control total del flujo de la aplicación.
- **JWT (JSON Web Tokens)**: Para la autenticación de usuarios y la protección de endpoints privados.
- **Enrutamiento personalizado**: Se ha implementado un sistema de rutas mediante expresiones regulares y un router manual.

### 4.1. Librerías utilizadas

Se han empleado las siguientes librerías mediante Composer:

#### **vlucas/phpdotenv**

- **Versión usada:** ^5.4  
- **Descripción:** Permite cargar variables de entorno desde un archivo `.env` al entorno de ejecución de PHP.
- **Uso principal en el proyecto:**  
  - Configuración de credenciales y parámetros sin exponerlos en el código.
  - Separación clara entre entorno de desarrollo y producción.

#### **firebase/php-jwt**

- **Versión usada:** ^6.11  
- **Descripción:** Facilita la creación, codificación, decodificación y validación de tokens JWT.
- **Uso principal en el proyecto:**  
  - Implementación del sistema de autenticación.
  - Protección de rutas privadas mediante verificación de tokens.

---

## 5. Estructura y funcionamiento del proyecto

El archivo principal del proyecto es `index.php`, el cual se encarga de:

- Recoger las peticiones HTTP entrantes.
- Buscar la ruta correspondiente mediante el router.
- Verificar si la ruta requiere autenticación.
- Instanciar el controlador correspondiente y ejecutar su lógica.

### Control de autenticación

Si una ruta está marcada como privada (requiere el perfil `usuario`), se llama a la función `estaAutentificado()` para verificar si el token de acceso es válido. Si no lo es, se responde con un error 401 (No autorizado).

---

## 6. Endpoints definidos

### Usuarios (público)

- **Registro de usuario**
  - Ruta: `/api/register`
  - Método: POST
  - Controlador: `UsuariosController`

- **Login de usuario**
  - Ruta: `/api/login`
  - Método: POST
  - Controlador: `AuthController`

---

### Usuario (privado)

- **Operaciones del usuario autenticado (GET, PUT, DELETE)**
  - Ruta: `/api/user`
  - Métodos: GET, PUT, DELETE
  - Controlador: `UsuariosController`
  - Requiere autenticación

- **Renovar el token de sesión**
  - Ruta: `/api/token/refresh`
  - Método: POST
  - Controlador: `AuthController`
  - Requiere autenticación

---

### Centros cívicos (público)

- **Obtener todos los centros o un centro por ID**
  - Ruta: `/api/centros` o `/api/centros/{id}`
  - Método: GET
  - Controlador: `CentrosCivicosController`

---

### Instalaciones (público)

- **Obtener instalaciones de un centro específico**
  - Ruta: `/api/centros/{id}/instalaciones`
  - Método: GET
  - Controlador: `InstalacionesController`

- **Obtener todas las instalaciones**
  - Ruta: `/api/instalaciones`
  - Método: GET
  - Controlador: `InstalacionesController`

---

### Actividades (público)

- **Obtener actividades de un centro específico**
  - Ruta: `/api/centros/{id}/actividades`
  - Método: GET
  - Controlador: `ActividadesController`

- **Obtener todas las actividades**
  - Ruta: `/api/actividades`
  - Método: GET
  - Controlador: `ActividadesController`

---

### Reservas (privado)

- **Gestionar reservas del usuario**
  - Ruta: `/api/reservas` o `/api/reservas/{id}`
  - Métodos: GET, POST, DELETE
  - Controlador: `ReservasController`
  - Requiere autenticación

---

### Inscripciones (privado)

- **Inscribirse o desinscribirse de actividades**
  - Ruta: `/api/inscripciones` o `/api/inscripciones/{id}`
  - Métodos: POST, DELETE
  - Controlador: `InscripcionesController`
  - Requiere autenticación

---

## 7. Cliente web con React

### ¿Qué es React?

React es una biblioteca de JavaScript desarrollada por Meta (Facebook) para construir interfaces de usuario dinámicas y reactivas. Su principal característica es la posibilidad de construir componentes reutilizables que actualizan la vista de manera eficiente cuando cambian los datos.

---

### Desarrollo del cliente

El cliente del proyecto se ha desarrollado utilizando **React** y está compuesto por varios componentes funcionales que se comunican con la API para mostrar datos y permitir la interacción del usuario.

Se utiliza el sistema de rutas de React (`react-router-dom`) para navegar entre diferentes páginas como el listado de centros, detalles de un centro, login, registro, actividades, instalaciones y gestión de la cuenta.

#### Estructura principal del archivo `App.js`:

- Se define el estado `isAuthenticated` para controlar si el usuario está autenticado.
- Se comprueba el token JWT guardado en `localStorage` al cargar la aplicación.
- Se añaden listeners para detectar cambios en `localStorage` (por ejemplo, al cerrar sesión).
- Se incluyen todas las rutas mediante `<Routes>` y `<Route>`, asociadas a sus respectivos componentes.

#### Rutas implementadas:

- `/` → Página de inicio con el listado de centros (`Centros`)
- `/centro/:id` → Detalles de un centro específico (`CentroUnico`)
- `/login` → Página de inicio de sesión (`Login`)
- `/registro` → Página de registro (`Registro`)
- `/logout` → Componente para cerrar sesión (`Logout`)
- `/mi-cuenta` → Perfil del usuario (`MiCuenta`)
- `/instalaciones` → Lista de instalaciones (`Instalaciones`)
- `/actividades` → Lista de actividades (`Actividades`)
- `/mis-reservas` → Gestión de reservas (`Reservas`)

El componente `Navbar` se actualiza dinámicamente según el estado de autenticación, mostrando u ocultando opciones como "Mi cuenta" o "Cerrar sesión".

---
