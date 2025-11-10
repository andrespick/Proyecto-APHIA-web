¡Listo! Reescribí tu README mejorando redacción y cambiando **Laravel → PHP puro** y **Trello → Jira**, tal como pediste. Aquí tienes la versión sugerida:

---

# 🏢 Software de Gestión de Clientes – APHIA S.A.S.

Proyecto académico del **Proyecto Integrador (5.º semestre)** del programa de **Tecnología en Sistemas de Información** de la **Institución Universitaria Antonio José Camacho**.
El sistema centraliza la información de **propietarios, inquilinos, inmuebles, contratos y transacciones**, con el fin de **optimizar procesos**, **reducir errores** y **mejorar la atención al cliente** en la empresa inmobiliaria **APHIA S.A.S.**

---

## 👥 Equipo

| Rol                 | Integrante                                                                            |
| ------------------- | ------------------------------------------------------------------------------------- |
| **Scrum Master**    | Andrés Felipe Vela Flórez                                                             |
| **Developers**      | Diógenes Bermeo Sánchez, Erika Andrea Erazo Rodríguez, Jeison Elián Benítez Hernández |
| **Product Owner**   | APHIA S.A.S.                                                                          |
| **Profesor Asesor** | Flóver Sánchez Ortega                                                                 |

---

## 🎯 Objetivo General

Desarrollar un sistema de gestión para **organizar y controlar** la información de clientes e inmuebles en APHIA S.A.S., incrementando la **eficiencia operativa** y la **calidad del servicio**.

---

## ⚙️ Objetivos Específicos

* Implementar un módulo de **propietarios** con operaciones CRUD completas.
* Desarrollar los módulos de **inquilinos**, **codeudores**, **contratos** e **inmuebles**.
* Incorporar **reportes financieros básicos** y **notificaciones automáticas**.
* Implementar **autenticación** y **recuperación de contraseña** seguras.
* Planificar y dar seguimiento al desarrollo usando **metodologías ágiles (Scrum) con Jira**.

---

## 🧩 Tecnologías

| Tipo                             | Herramientas            |
| -------------------------------- | ----------------------- |
| **Backend**                      | **PHP 8 (puro)**        |
| **Frontend**                     | HTML5, CSS3, JavaScript |
| **Base de Datos**                | MySQL                   |
| **Control de Versiones**         | Git & GitHub            |
| **Gestión Ágil**                 | **Jira** (Scrum)        |
| **Entorno de Ejecución/Pruebas** | XAMPP                   |

> 📌 Nota: Se prescindió de frameworks (p. ej., Laravel) debido al alcance del curso; la aplicación está construida con **PHP nativo** siguiendo una estructura modular (controladores, modelos y vistas).

---

## 🖥️ Funcionalidades Principales

### 🔐 Autenticación y Seguridad

* Inicio/cierre de sesión.
* Recuperación de contraseña por correo (SMTP).
* Hash seguro con `password_hash()`/**bcrypt**.
* Autorización por roles (Administrador, Administrativo, Asesor).

### 🧍‍♂️ Gestión de Clientes

* CRUD de clientes.
* Búsquedas y filtros (nombre, documento, correo).

### 🏠 Propietarios e Inmuebles

* CRUD de **propietarios**.
* Registro/edición de **inmuebles** (ubicación, canon, propietario, estado, etc.).

### 📑 Contratos

* Alta, edición y baja de contratos (fechas, condiciones, relaciones con inmuebles y partes).
* **Generación de PDF** de contratos (biblioteca PHP compatible).

### 💰 Finanzas

* Registro de pagos e ingresos.
* Reportes básicos (por período, por inmueble/cliente).

### 📩 Notificaciones y Alertas

* Alertas de **vencimiento** de contratos y **pagos pendientes**.

---

## 🚀 Puesta en Marcha (local)

1. Clonar el repositorio y colocar el proyecto en `xampp/htdocs`.
2. Crear una base de datos MySQL y ejecutar los scripts de tablas.
3. Configurar variables de conexión (host, usuario, contraseña, nombre BD) y SMTP en un archivo de configuración.
4. Iniciar **Apache** y **MySQL** en XAMPP.
5. Acceder desde el navegador: `http://localhost/aphia`.

---

## 🗂️ Estructura Sugerida del Proyecto

```
/app
  /controllers
  /models
  /views
/config
/public
/vendor        (bibliotecas opcionales, p. ej., para PDF)
index.php
```

* **/app/controllers**: lógica de orquestación (recibe request, llama modelos, selecciona vistas).
* **/app/models**: consultas SQL y acceso a datos (PDO/MySQLi).
* **/app/views**: plantillas HTML/CSS/JS.
* **/config**: configuración de BD, SMTP, variables de entorno.
* **/public**: assets (CSS, JS, imágenes).
* **index.php**: punto de entrada y ruteo básico.

---

## ✅ Buenas Prácticas Aplicadas

* **PDO** con consultas preparadas para evitar SQL Injection.
* Separación **MVC ligera** sin framework.
* Validación del lado **cliente** (JS) y **servidor** (PHP).
* Control de sesiones y regeneración de ID tras login.
* Manejo centralizado de errores y logs.

---

## 📌 Alcances y Limitaciones

* El proyecto está orientado al entorno académico y a un **equipo pequeño**.
* La integración con servicios externos (correo, generación de PDF) se realiza con **librerías PHP** independientes.
* No se utiliza Laravel ni otros frameworks por decisión académica y de tiempo.

---

¿Quieres que también te deje un ejemplo de **archivo de configuración** y un **esqueleto de ruteo** en PHP nativo para que lo pegues tal cual?
