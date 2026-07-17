# Constitución de Contenido — Plan Salud Fácil

**Basado en:** [Crear contenido útil, fiable y centrado en las personas](https://developers.google.com/search/docs/fundamentals/creating-helpful-content?hl=es) — Google Search Central  
**Propósito:** Todo el contenido nuevo y existente en `plansaludfacil.cl` debe cumplir estas reglas antes de publicarse. No son sugerencias: son condiciones.

---

## I. Principio fundamental: Las personas primero

El contenido existe para **ayudar a una persona real** que busca información sobre isapres. No existe para rankear en Google. Si cumple lo primero, lo segundo llega solo.

- ❌ "Necesito llenar esta página con keywords de isapres para que Google la posicione"
- ✅ "Un profesional de 28 años quiere saber qué plan le conviene. ¿Qué información real necesita?"

---

## II. E-E-A-T: Los 4 pilares de confianza

### Experience (Experiencia)
El contenido debe demostrar **experiencia de primera mano** en el mundo de las isapres.

- ✅ "Hemos cotizado más de 1.000 planes en 2026. Los precios reales de Cruz Blanca para un adulto de 35 años en Santiago oscilan entre $X y $Y."
- ❌ "Cruz Blanca tiene precios competitivos." (vago, sin datos, sin evidencia de experiencia)
- **Regla concreta:** Todo dato numérico (precio, cobertura, copago) debe venir de un PDF real (`/adjuntos/`) o de información proporcionada directamente por la isapre.

### Expertise (Conocimiento técnico)
El contenido debe mostrar **dominio del sistema de isapres chileno**.

- ✅ Explicar qué significa "UF", "VA", "tope por evento", "preferente vs libre elección"
- ✅ Citar la Ley Corta de Isapres y sus implicancias reales
- ❌ "Los planes de isapre son buenos para tu salud" (sin profundidad técnica)
- **Regla concreta:** Cada página debe incluir al menos un dato técnico verificable (UF, VA, % de bonificación, período de carencia, tope anual).

### Authoritativeness (Autoridad)
El contenido debe **citar fuentes reconocibles**.

- ✅ "Según la Superintendencia de Salud, las isapres ajustaron sus precios un X% en 2025"
- ✅ Incluir enlaces a `supersalud.gob.cl` cuando corresponda
- ❌ Afirmaciones sin fuente: "Colmena es la mejor isapre para familias"
- **Regla concreta:** Toda afirmación comparativa ("la mejor", "la más grande", "la más barata") debe tener una fuente o dato objetivo que la respalde.

### Trustworthiness (Confianza)
El contenido debe ser **transparente sobre quién lo crea y por qué**.

- ✅ "Este análisis fue revisado por nuestro equipo de corredores de isapre con 5 años de experiencia"
- ✅ "Última actualización: julio 2026"
- ❌ Ocultar que el contenido fue generado por IA sin revisión humana
- **Regla concreta:** Si se usa IA para generar borradores, debe ser revisado y validado por un experto humano antes de publicar.

---

## III. Las 15 preguntas de autoevaluación de Google

Antes de publicar cualquier página, debe poder responderse "SÍ" a las preguntas 1-5 sin dudar:

### ¿El contenido es original?
1. ¿Aporta información, análisis o investigación original?
2. ¿Ofrece una descripción completa del tema?
3. ¿Proporciona un análisis profundo más allá de lo obvio?
4. ¿Cita fuentes externas verificables?
5. ¿Es sustancialmente diferente de otras páginas del sitio? → **Crítico para PSF: las 6 páginas de compañías NO deben ser iguales**

### ¿Demuestra experiencia real?
6. ¿El contenido fue creado por alguien con experiencia real en isapres?
7. ¿El titular y el título de la página son descriptivos y útiles (no clickbait)?
8. ¿Confiarías en esta información si viniera de una enciclopedia impresa?
9. ¿El contenido tiene errores fácticos verificables? (debe ser NO)

### ¿Está centrado en las personas?
10. ¿El contenido se creó pensando en ayudar a los usuarios, no en atraer visitas de buscadores?
11. ¿El sitio tiene un propósito principal claro (comparar isapres)?
12. Después de leer el contenido, ¿el usuario siente que aprendió suficiente sobre el tema?

### ¿Evita prácticas de "contenido para buscadores"?
13. ¿Evita resumir lo que otros dicen sin agregar valor?
14. ¿Evita producir contenido masivo en muchas variantes solo para cubrir keywords? → **Crítico: no crear 80 páginas iguales, una por cada variante de plan**
15. ¿Está dirigido a una audiencia específica (ej: jóvenes profesionales, adultos mayores) en lugar de ser genérico?

---

## IV. Reglas concretas para Plan Salud Fácil

### 1. Páginas de compañías (las 6)
- **Prohibido:** misma estructura con solo cambio de nombre de isapre
- **Obligatorio:** cada página debe tener al menos **3 diferencias sustanciales** respecto a las otras:
  - Un dato numérico real de cobertura extraído de un PDF (`/adjuntos/`)
  - Una clínica o prestador específico de la red de esa isapre
  - Una política de aceptación o requisito específico (ej: edad máxima, preexistencias)

### 2. Precios y coberturas
- **Prohibido:** inventar precios. "Desde $65.000" sin fuente es desinformación.
- **Obligatorio:** usar marcadores con disclaimer cuando no hay dato real:
  ```
  Precio referencial: no disponible en documento oficial. 
  Solicita una cotización personalizada sin costo.
  ```
- **Excepción:** si el precio viene de un PDF real en `/adjuntos/`, citar el código del plan (ej: "Plan COLMENA BLUE 22610080").

### 3. Comparaciones entre isapres
- **Prohibido:** "X es mejor que Y" sin dato objetivo
- **Permitido:** "X tiene tope hospitalario de 800 UF/año vs 250 UF/año de Y"
- **Regla:** toda comparación debe ser cuantificable

### 4. Disclaimer obligatorio
Toda página con contenido comercial debe incluir al pie:
```
La información de coberturas se basa en los planes de salud complementarios 
publicados por cada isapre. Los precios finales dependen de tu renta imponible 
(7% legal), edad, cargas y evaluación de salud. Solicita una cotización 
personalizada sin costo.
```

---

## V. Framework "Quién, Cómo, Por qué"

Antes de crear cualquier página nueva, responder en el encabezado del archivo PHP:

```php
/**
 * Quién: [nombre o rol del creador del contenido]
 * Cómo: [investigación manual / revisión de PDF oficial / IA supervisada]
 * Por qué: [necesidad real del usuario que esta página resuelve]
 * Fuentes: [PDFs, URLs, o documentos usados]
 * Última revisión: [fecha]
 */
```

---

## VI. Checklist de publicación

- [ ] ¿El título (H1) describe exactamente lo que el usuario encontrará?
- [ ] ¿La meta description es un resumen real del contenido (no clickbait)?
- [ ] ¿Hay al menos un dato numérico verificable (UF, %, VA, tope)?
- [ ] ¿La página es sustancialmente diferente de sus hermanas en el mismo nivel?
- [ ] ¿Las afirmaciones comparativas tienen fuente?
- [ ] ¿El contenido responde una pregunta real de un usuario chileno sobre isapres?
- [ ] ¿El disclaimer de precios está presente?
- [ ] ¿Se revisó manualmente (no solo IA)?

---

*Documento vivo. Última actualización: 14 de julio de 2026. Cualquier miembro del equipo puede proponer enmiendas.*
