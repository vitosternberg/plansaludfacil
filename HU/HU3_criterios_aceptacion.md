# HU 3: Decisión del Lead sobre el Plan Más Afín

**Historia de Usuario:**
> Como lead quiero tomar una decisión sobre el plan definido como más afín a mis características para percibir altos beneficios dados por una correcta evaluación del sistema.

---

## Criterios de Aceptación

### CA-01: Destacar el plan más afín con justificación clara

**Dado que** el sistema evaluó los datos personales, financieros y de salud del lead  
**Cuando** se presentan los resultados de la cotización  
**Entonces** el sistema destaca visualmente el plan más afín a su perfil, indicando de forma clara y resumida las razones por las que ese plan es el más recomendado para él (ej. "Mejor relación precio-cobertura para tu edad y comuna", "Mayor cobertura en kinesiología que solicitaste", "Menor costo mensual con las coberturas que te interesan").

---

### CA-02: Estimación de ahorro mensual y anual

**Dado que** el lead quiere saber cuánto dinero ahorraría con el plan recomendado  
**Cuando** revisa los resultados de la evaluación  
**Entonces** el sistema muestra el ahorro estimado mensual y anual en pesos chilenos, comparando el plan recomendado contra el plan promedio del mercado o contra su plan actual si lo indicó, presentado en un formato destacado y de fácil lectura.

---

### CA-03: Comparación detallada de coberturas entre planes

**Dado que** el lead necesita comparar coberturas específicas para decidir  
**Cuando** revisa la comparación de los planes evaluados  
**Entonces** el sistema presenta una tabla comparativa con las coberturas principales (hospitalización, ambulatorio, maternidad, dental, farmacia, kinesiología, telemedicina, excedentes, salud mental) indicando para cada plan evaluado el porcentaje de cobertura, tope anual y valor del copago, con el plan más afín resaltado visualmente.

---

### CA-04: Explicación de compatibilidad del plan con el perfil del lead

**Dado que** el lead quiere entender por qué el plan se ajusta a sus características personales  
**Cuando** revisa la evaluación del plan recomendado  
**Entonces** el sistema muestra una sección que relacione explícitamente cada característica del lead (edad, renta, comuna, cargas, preexistencias, intereses de cobertura) con las ventajas concretas que ofrece el plan recomendado para ese aspecto específico.

---

### CA-05: Indicador de nivel de afinidad del plan

**Dado que** el lead necesita una medida clara de qué tan bien se ajusta el plan a su perfil  
**Cuando** se presenta el plan más afín  
**Entonces** el sistema muestra un indicador visual de afinidad (porcentaje, puntuación o barra de progreso) que refleje el grado de compatibilidad entre las necesidades del lead y las coberturas del plan, acompañado de una breve descripción de lo que significa ese nivel.

---

### CA-06: Alternativas de planes con justificación de descarte

**Dado que** el lead quiere entender por qué otros planes no fueron los más recomendados  
**Cuando** revisa los planes alternativos presentados  
**Entonces** el sistema muestra para cada plan alternativo una breve justificación de por qué no fue el más afín (ej. "Mayor precio sin mejora significativa en tus coberturas de interés", "Menor cobertura en hospitalización, que es relevante para tu perfil", "No incluye cobertura dental que solicitaste"), permitiendo al lead entender la lógica de la evaluación.

---

### CA-07: Simulador de impacto al modificar variables

**Dado que** el lead quiere explorar cómo cambiarían las opciones si varían sus condiciones  
**Cuando** utiliza el simulador de la página de resultados  
**Entonces** el sistema permite modificar variables clave (renta, cargas, comuna) y recalcula dinámicamente las recomendaciones, mostrando cómo afecta cada cambio a los planes sugeridos y al nivel de afinidad, sin perder la cotización original.

---

### CA-08: Testimonios y casos de éxito de perfiles similares

**Dado que** el lead necesita confiar en que otros en su situación obtuvieron buenos resultados  
**Cuando** revisa el plan recomendado  
**Entonces** el sistema muestra testimonios verificados de clientes con perfiles similares (misma comuna, rango de edad, tipo de plan) que contrataron a través del sistema, indicando el ahorro obtenido y su nivel de satisfacción con el plan elegido.

---

### CA-09: Certificación o sello de confianza de la evaluación

**Dado que** el lead necesita confiar en la imparcialidad y calidad de la evaluación  
**Cuando** se presenta el resultado de la cotización  
**Entonces** el sistema muestra sellos o indicadores de confianza visibles que respalden la evaluación (ej. "Evaluación imparcial", "Análisis basado en datos reales de la Superintendencia de Salud", "Sin sesgo por comisión de isapre"), reforzando la transparencia del proceso.

---

### CA-10: Desglose del costo total del plan

**Dado que** el lead quiere entender exactamente cuánto pagará y por qué  
**Cuando** revisa el detalle del plan más afín  
**Entonces** el sistema desglosa el costo total mensual en sus componentes: cotización obligatoria de salud (7%), cotización adicional si aplica, y valor total final, explicando de forma sencilla qué significa cada componente y cómo se calcula según su renta líquida.

---

### CA-11: Red de prestadores y clínicas disponibles

**Dado que** el lead quiere saber dónde podrá atenderse con el plan recomendado  
**Cuando** revisa los detalles del plan más afín  
**Entonces** el sistema muestra las clínicas, centros médicos y prestadores principales disponibles en su comuna de residencia con el plan recomendado, indicando si son de atención cerrada, preferente o libre elección.

---

### CA-12: Comparación contra FONASA

**Dado que** el lead podría estar evaluando si quedarse en FONASA o migrar a isapre  
**Cuando** el lead indicó que actualmente está en FONASA  
**Entonces** el sistema incluye una comparación específica entre el plan de isapre recomendado y la cobertura de FONASA para su perfil, mostrando diferencias en tiempos de espera, acceso a especialistas, cobertura de hospitalización y costo estimado de bolsillo en cada sistema.

---

### CA-13: Pasos concretos para contratar el plan elegido

**Dado que** el lead decidió qué plan quiere contratar  
**Cuando** hace clic en la opción de contratación del plan  
**Entonces** el sistema muestra los pasos concretos y secuenciales para completar la contratación: documentación requerida, plazos estimados, si necesita desafiliarse de su isapre actual, y la opción de que un ejecutivo lo asista de forma personalizada en cada paso sin costo adicional.

---

### CA-14: Canal de comunicación directa con el ejecutivo asignado

**Dado que** el lead tiene dudas específicas antes de decidir  
**Cuando** interactúa con los canales de contacto desde la página de resultados  
**Entonces** el sistema ofrece comunicación directa con un ejecutivo especializado (WhatsApp, llamada telefónica y correo electrónico), mostrando disponibilidad horaria, tiempo estimado de respuesta, y la opción de agendar una llamada en un horario conveniente para el lead.

---

### CA-15: Guardado de la cotización para consulta posterior

**Dado que** el lead quiere tomar la decisión con calma y revisar la cotización más tarde  
**Cuando** está en la página de resultados  
**Entonces** el sistema permite guardar la cotización enviando un enlace único al correo electrónico del lead, con una vigencia definida (ej. 7 días), y muestra un mensaje de confirmación indicando que el enlace fue enviado y su fecha de expiración.

---

### CA-16: Indicador de urgencia sin presión excesiva

**Dado que** el lead podría postergar indefinidamente la decisión  
**Cuando** revisa la cotización  
**Entonces** el sistema muestra un indicador sutil de que los precios y condiciones pueden variar en el tiempo (ej. "Precios verificados al día de hoy. Las isapres ajustan sus tarifas periódicamente"), sin generar una sensación de presión abusiva que dañe la confianza del lead en el sistema.

---

### CA-17: Confirmación de decisión y apertura del proceso de contratación

**Dado que** el lead está listo para tomar la decisión y contratar el plan más afín  
**Cuando** confirma su intención de contratar mediante el botón de acción principal  
**Entonces** el sistema registra la decisión del lead, asigna formalmente un ejecutivo al caso, inicia el proceso de contratación notificando al equipo interno, y muestra al lead una confirmación con el resumen del plan elegido, el nombre del ejecutivo asignado y el plazo estimado para el primer contacto.
