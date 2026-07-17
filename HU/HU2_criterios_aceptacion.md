# HU 2: Visualización de Cotización para Decisión de Contratación

**Historia de Usuario:**
> Como usuario quiero ver una cotización para poder decidir si contrato con un ejecutivo de isapre.

---

## Criterios de Aceptación

### CA-01: Confirmación de recepción de la solicitud

**Dado que** el usuario envió correctamente sus datos desde el formulario de cotización  
**Cuando** es redirigido a la página de agradecimiento  
**Entonces** el sistema muestra un mensaje de confirmación con el tipo de plan cotizado (individual, familiar o monoparental), un ícono de verificación, y el número único de cotización asignado.

---

### CA-02: Visualización del número de cotización

**Dado que** la solicitud fue registrada en la base de datos  
**Cuando** el usuario llega a la página de resultado  
**Entonces** el sistema muestra el número de cotización en un formato destacado y legible (ej. "#158"), dentro de un bloque visualmente diferenciado con la etiqueta "N° de Cotización".

---

### CA-03: Resumen de los datos enviados

**Dado que** el usuario quiere verificar la información que envió  
**Cuando** se carga la página de resultado de la cotización  
**Entonces** el sistema muestra un resumen con los datos principales ingresados: nombre, email, teléfono, tipo de plan cotizado y fecha de la solicitud, presentados de forma clara en un recuadro con fondo diferenciado.

---

### CA-04: Estimación de precio y plan recomendado

**Dado que** el usuario envió sus datos para obtener una cotización  
**Cuando** se presenta el resultado de la cotización  
**Entonces** el sistema muestra al menos un plan recomendado con su nombre, precio estimado mensual en pesos chilenos, coberturas principales incluidas, y la isapre que lo ofrece, permitiendo al usuario evaluar si le conviene contratar.

---

### CA-05: Comparación de alternativas de planes

**Dado que** el usuario quiere tomar una decisión informada  
**Cuando** revisa el resultado de su cotización  
**Entonces** el sistema presenta al menos tres alternativas de planes ordenadas por precio o afinidad, indicando para cada una: nombre del plan, isapre, precio mensual estimado, y coberturas destacadas, permitiendo al usuario comparar antes de decidir.

---

### CA-06: Información de contacto del ejecutivo

**Dado que** el usuario está interesado en contratar tras ver la cotización  
**Cuando** revisa la página de resultado  
**Entonces** el sistema muestra los datos de contacto del ejecutivo asignado: nombre, teléfono y correo electrónico, junto con un mensaje que indique cuándo se contactará con él (ej. "en las próximas horas" o "en un plazo de 24 horas hábiles").

---

### CA-07: Acceso directo a WhatsApp del ejecutivo

**Dado que** el usuario quiere contactar al ejecutivo de inmediato  
**Cuando** hace clic en el enlace de WhatsApp  
**Entonces** el sistema abre una conversación de WhatsApp con el número del ejecutivo de isapre, con un mensaje predefinido que incluya el número de cotización del usuario para agilizar la atención.

---

### CA-08: Acceso a correo electrónico de contacto

**Dado que** el usuario prefiere comunicarse por correo electrónico  
**Cuando** hace clic en el enlace de correo  
**Entonces** el sistema abre el cliente de correo predeterminado con la dirección del ejecutivo y un asunto predefinido que incluya el número de cotización.

---

### CA-09: Indicación de próximos pasos

**Dado que** el usuario quiere saber qué sucede después de ver la cotización  
**Cuando** se encuentra en la página de resultado  
**Entonces** el sistema muestra una sección con los pasos siguientes claramente enumerados: el ejecutivo se contactará con él, revisará sus necesidades específicas, y lo guiará en el proceso de contratación si decide continuar.

---

### CA-10: Redirección automática al inicio

**Dado que** el usuario ha revisado su cotización y no realiza ninguna acción  
**Cuando** transcurren 15 segundos en la página de resultado  
**Entonces** el sistema muestra una cuenta regresiva visible y, al llegar a cero, redirige automáticamente al usuario a la página de inicio del sitio, con un botón visible que permite volver al inicio antes de que termine la cuenta regresiva.

---

### CA-11: Visualización sin registro previo en base de datos

**Dado que** el usuario llega a la página de resultado sin un identificador válido o el registro no se encuentra en la base de datos  
**Cuando** se carga la página sin datos de cotización  
**Entonces** el sistema muestra un mensaje genérico de confirmación indicando que la solicitud fue recibida correctamente y que un asesor se contactará pronto, sin mostrar errores ni información confusa.

---

### CA-12: Efecto visual de confirmación

**Dado que** el envío fue exitoso y el usuario merece una retroalimentación positiva  
**Cuando** se carga la página de resultado  
**Entonces** el sistema muestra un efecto visual celebratorio (animación de confeti o similar), un ícono de verificación animado, y un encabezado destacado con el tipo de plan y un mensaje de éxito que refuerce la confianza del usuario.

---

### CA-13: Seguimiento de conversión para marketing

**Dado que** el equipo de marketing necesita medir la efectividad de las campañas  
**Cuando** el usuario llega a la página de resultado con una cotización válida  
**Entonces** el sistema registra el evento de conversión con el identificador único de la cotización, el tipo de plan y el valor de la conversión, sin comprometer datos personales sensibles del usuario.

---

### CA-14: Acceso a la cotización desde múltiples dispositivos

**Dado que** el usuario podría querer revisar su cotización más tarde desde otro dispositivo  
**Cuando** accede a la URL de resultado con el identificador de su cotización  
**Entonces** el sistema recupera los datos desde la base de datos y muestra la misma información de cotización (número, resumen, plan recomendado y datos de contacto) en cualquier dispositivo, con un diseño responsive adaptado a móviles, tablets y escritorio.

---

### CA-15: Privacidad en la visualización de la cotización

**Dado que** la cotización contiene datos personales y financieros del usuario  
**Cuando** se muestra la página de resultado  
**Entonces** el sistema no expone datos sensibles en la URL (salvo el identificador numérico de cotización), no almacena la información de la cotización en cookies ni localStorage, y aplica escapado de salida (`htmlspecialchars`) en todos los datos renderizados para prevenir inyección de código.

---

### CA-16: Indicador de urgencia o escasez

**Dado que** el usuario puede dudar en tomar acción inmediata  
**Cuando** se muestra la cotización  
**Entonces** el sistema incluye un mensaje que indique que la cotización tiene una validez limitada (ej. "Esta cotización es válida por 7 días" o "Los precios pueden variar") para incentivar la decisión de contacto con el ejecutivo.
