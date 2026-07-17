# HU 1: Cotización de Plan de Isapre con Datos Personales

**Historia de Usuario:**
> Como usuario cotizador de planes de isapre, quiero ingresar mis datos personales y sensibles (edad, sueldo, lugar de residencia, cargas, salud actual) para obtener una cotización estimada de un plan acorde a mis datos.

---

## Criterios de Aceptación

### CA-01: Captura de datos personales básicos

**Dado que** el usuario accede al cotizador de planes  
**Cuando** se carga la página del formulario  
**Entonces** el sistema muestra los campos obligatorios: nombre completo (texto, mínimo 2 caracteres), email (formato válido) y teléfono (exactamente 9 dígitos numéricos), cada uno con su etiqueta visible, atributo `required` y placeholder orientativo.

---

### CA-02: Captura de edad

**Dado que** el usuario está completando el formulario de cotización  
**Cuando** ingresa un valor en el campo "Edad"  
**Entonces** el sistema acepta solo números enteros positivos, valida que no esté vacío y sea mayor a 0, y muestra feedback visual en tiempo real (borde verde si es válido, borde rojo si es inválido).

---

### CA-03: Captura de renta líquida

**Dado que** el usuario está completando sus datos financieros  
**Cuando** ingresa un valor en el campo "Renta Líquida"  
**Entonces** el sistema acepta solo números enteros positivos, valida que no esté vacío y sea mayor a 0, y muestra feedback visual en tiempo real (borde verde si es válido, borde rojo si es inválido).

---

### CA-04: Captura de comuna de residencia

**Dado que** el usuario necesita indicar su lugar de residencia  
**Cuando** interactúa con el campo "Comuna de Residencia"  
**Entonces** el sistema ofrece un autocompletado con el listado completo de comunas chilenas (94 comunas en orden alfabético), valida que el texto tenga al menos 2 caracteres, y permite al usuario escribir libremente o seleccionar de la lista.

---

### CA-05: Captura de cargas

**Dado que** el usuario tiene personas a su cargo  
**Cuando** selecciona una opción en el campo "Cargas"  
**Entonces** el sistema ofrece las opciones "0", "1", "2", "3" y "4 o más", valida que se haya seleccionado un valor, y muestra feedback visual (borde verde/rojo).

---

### CA-06: Captura del sistema de salud actual

**Dado que** el usuario quiere cotizar un nuevo plan  
**Cuando** responde a la pregunta "¿En qué sistema de salud estás actualmente?"  
**Entonces** el sistema permite seleccionar entre "FONASA", "Isapre" o "Ninguno", y si selecciona "Isapre" despliega un campo adicional para que indique el nombre de su isapre actual.

---

### CA-07: Captura de preexistencias de salud

**Dado que** el usuario puede tener condiciones de salud preexistentes  
**Cuando** responde a la pregunta sobre preexistencias  
**Entonces** el sistema ofrece opciones "Sí" y "No" (marcado "No" por defecto), y si selecciona "Sí" despliega un campo de texto confidencial para detallar la condición, el cual se oculta nuevamente si vuelve a seleccionar "No".

---

### CA-08: Captura de intereses de cobertura

**Dado que** el usuario tiene preferencias específicas de cobertura de salud  
**Cuando** completa la sección de intereses  
**Entonces** el sistema permite seleccionar múltiples coberturas de interés mediante checkboxes (atención ambulatoria, hospitalización, maternidad, kinesiología, telemedicina, excedentes, dental, farmacia, salud mental, otra) y si marca "Otra" habilita un campo de texto libre para especificar.

---

### CA-09: Protección antispam

**Dado que** el formulario está expuesto en internet y vulnerable a bots  
**Cuando** se renderiza el formulario  
**Entonces** el sistema incluye un campo honeypot invisible para el usuario humano (`opacity: 0`, fuera de la vista, con `tabindex="-1"` y `aria-hidden="true"`), y el backend rechaza silenciosamente cualquier envío donde ese campo no esté vacío.

---

### CA-10: Validación del formulario antes del envío

**Dado que** el usuario ha completado los campos y presiona el botón de envío  
**Cuando** se ejecuta la validación completa del formulario  
**Entonces** el sistema verifica cada campo obligatorio según su tipo (email, teléfono, número, texto, select), y si algún campo es inválido resalta los campos problemáticos en rojo, muestra el mensaje "Por favor, completa correctamente los campos destacados en rojo" y no envía la solicitud al servidor.

---

### CA-11: Envío exitoso del formulario

**Dado que** todos los campos son válidos  
**Cuando** el usuario presiona "Solicitar Cotización Individual"  
**Entonces** el sistema envía los datos por POST al backend, construye un mensaje estructurado con todos los datos ingresados (comuna, sistema actual, edad, renta, cargas, intereses y preexistencias), y al recibir respuesta exitosa redirige al usuario a la página de agradecimiento con el identificador de su solicitud.

---

### CA-12: Error de conexión durante el envío

**Dado que** ocurre un fallo de red o el servidor no responde  
**Cuando** la solicitud de envío falla  
**Entonces** el sistema muestra el mensaje "Error de conexión. Inténtalo más tarde.", mantiene el formulario visible y editable, y permite al usuario reintentar el envío sin perder los datos ya ingresados.

---

### CA-13: Validación de datos en el servidor

**Dado que** el formulario fue enviado al backend  
**Cuando** el servidor recibe la solicitud POST  
**Entonces** el sistema valida que nombre, email, tipo de consulta y mensaje no estén vacíos, que el email tenga formato válido, que el teléfono (si se envió) tenga exactamente 9 dígitos, y que el campo honeypot esté vacío; si alguna validación falla, devuelve un error HTTP 400 con un mensaje descriptivo en formato JSON.

---

### CA-14: Almacenamiento de la cotización en base de datos

**Dado que** los datos pasaron la validación del servidor  
**Cuando** se inserta el registro en la base de datos  
**Entonces** el sistema guarda el nombre, correo, celular y datos adicionales (tipo de plan, mensaje estructurado, y campos extra) en la tabla correspondiente, obtiene el identificador generado, y responde al frontend con `{"success": true, "message_id": <id>}`.

---

### CA-15: Notificación por correo electrónico

**Dado que** la cotización se guardó exitosamente  
**Cuando** se dispara el envío de correo  
**Entonces** el sistema envía una notificación por SMTP seguro (puerto 465, SMTPS) al administrador y a los destinatarios configurados, con el asunto y cuerpo detallando todos los datos de la cotización (nombre, email, teléfono, tipo de consulta, edad, renta, comuna, cargas, intereses y preexistencias), sin que un fallo en el correo afecte la respuesta de éxito ya enviada al usuario.

---

### CA-16: Privacidad y confidencialidad de datos sensibles

**Dado que** el formulario recopila datos personales y sensibles (edad, sueldo, salud, preexistencias, cargas)  
**Cuando** el usuario interactúa con el formulario  
**Entonces** el sistema muestra un aviso visible de privacidad ("Tus datos están seguros. No los compartiremos con terceros sin tu autorización."), envía los datos exclusivamente por POST sin exponerlos en la URL, utiliza conexión segura para el envío de correos, y los campos de preexistencias incluyen texto que indica confidencialidad.

---

### CA-17: Accesibilidad del formulario

**Dado que** el formulario debe ser usable por personas con diversas capacidades  
**Cuando** se renderiza y se interactúa con el formulario  
**Entonces** cada campo tiene una etiqueta asociada, los campos obligatorios usan el atributo `required`, los placeholders son orientativos sin sustituir a las etiquetas, el contraste de texto es adecuado (texto oscuro sobre fondo claro), y el formulario es navegable por teclado.

---

### CA-18: Prevención de envíos duplicados

**Dado que** el usuario hace clic en el botón de envío  
**Cuando** la solicitud está siendo procesada por el servidor  
**Entonces** el sistema deshabilita el botón de envío para evitar solicitudes duplicadas, muestra un indicador visual de carga, y rehabilita el botón solo si ocurre un error que requiera reintento.
