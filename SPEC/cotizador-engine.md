# SPEC: Motor de Cotización Real — PlanSaludFácil

**Última actualización:** 2026-07-16 (sesión activa)
**Estado:** FASE 1-3 completadas. Motor funcional con pricing realista.

---

## FASE 1 — Catálogo de Planes ✅

### 1.1 Scraping del catálogo QuVi ✅
- **Archivo:** `adjuntos/planes_isapre.csv`
- **2,231 planes** de 7 ISAPREs con 100% de nombres, UF, prestadores
- **Scripts:** `scripts/scrape_all_quvi_planes.py`, `scripts/fill_missing_names.py`

### 1.2 Tabla HTML interactiva ✅
- **Archivo:** `pages/planes/tabla-planes.php`
- Búsqueda, filtros, orden, paginación, badges por ISAPRE

---

## FASE 2 — Coberturas Reales ✅

### 2.1 Fuente de coberturas ✅
- **Archivo:** `adjuntos/revision_IA_Planes_isapre.csv`
- Datos por ISAPRE: % hospitalario, % consulta, tope anual, urgencia (preferente + libre elección)

### 2.2 Parser de coberturas ✅
- Normaliza nombres, cap 100% en porcentajes, soporta múltiples formatos

---

## FASE 3 — Motor de Cotización ✅

### 3.1 Engine (`core/cotizador_engine.py`) ✅

**Pricing realista** (implementado 2026-07-16):
```
precio_clp = (UF_base × factor_edad + cargas × (UF_base × factor_carga + GES)) × valor_UF
```

**✅ Tabla OFICIAL implementada (`core/isapre_pricing.py`, Circular IF/N° 343)**

**Tabla OFICIAL (Circular IF/N° 343, Superintendencia de Salud, vigente desde 01/04/2020):**
| Edad | Cotizantes | Cargas |
|---|---|---|
| 0 a <20 | **0.6** | **0.6** |
| 20 a <25 | 0.9 | 0.7 |
| 25 a <35 | 1.0 | 0.7 |
| 35 a <45 | 1.3 | 0.9 |
| 45 a <55 | 1.4 | 1.0 |
| 55 a <65 | 2.0 | 1.4 |
| 65+ | 2.4 | 2.2 |

- **Tabla única** para todo el sistema ISAPRE (no varía por ISAPRE)
- **Cargas tienen tabla distinta** (más baja) que cotizantes
- **Prohíbe discriminación por sexo** explícitamente
- **No aplica para cambios de precio por tramo etario** (solo al contratar/incorporar)
- **Fuente:** `adjuntos/articles-18918_recurso_1.pdf`

**GES prima real:** 1.0–1.4 UF por beneficiario (varía por ISAPRE). Actualmente usamos 0.2 UF (subestimado).

**HECHO: `core/isapre_pricing.py` usa tabla oficial Circular IF/N° 343. `core/cotizador_engine.py` importa el módulo.

**Carga-adjusted pricing (fórmula real):**
- Cada carga: plan_uf × factor_edad_carga + GES_prima (~0.2 UF) al precio mensual (~$27,000 CLP a $38,500/UF)
- El scoring fuerza planes más baratos cuando hay cargas para compensar

**Scoring (100 pts):**
| Componente | Peso | Descripción |
|---|---|---|
| Precio vs 7% | 35 pts | Ajustado por edad + cargas. Dentro del 7% → 35 pts |
| Cobertura intereses | 30 pts | Match entre intereses y coberturas ISAPRE (midpoint del rango) |
| Red prestadores | 15 pts | 30+ → 15 pts; 15+ → 10 pts |
| Sin cargas | 5 pts | Bonus si 0 cargas y plan <3.5 UF |
| ISAPRE top | 3 pts | Bonus menor por Banmédica/Cruz Blanca/Consalud |

**Diversificación:** Top 5 garantiza 1 plan por ISAPRE distinta.

### 3.2 Endpoint API (`api/cotizar.php`) ✅
- POST JSON → ranking de 5 planes
- `precio_clp` incluye factor etario y carga (corregido 2026-07-16)

### 3.3 Comparador (`pages/planes/comparador.php`) ✅ UPGRADEADO
- **Antes:** datos estáticos de `queplan_isapres_datos.csv`, comparación ISAPRE-level
- **Ahora:** llama a `api/cotizar.php`, muestra 5 planes reales con scores y razones
- CTA dinámico: 0 cargas → formulario individual, 1+ cargas → formulario familiar
- Prefill de edad, renta y cargas en URL del formulario
- URL del API usa `BASE_URL` dinámico (funciona en local y producción)

### 3.4 Cotización Adicional Voluntaria ✅
- Planes que exceden el 7% muestran: "+$X extra (descuento vía empleador)"
- Si 0 de 5 planes caben en el 7%: banner ámbar con 4 opciones (cotización adicional, plan compensado, revisar cargas, Ley Corta)
- Si solo 1-2 caben: banner azul informativo

### 3.5 Integración `pages/gracias.php` ✅
- `motor_cotizacion_real()` llama al engine Python
- Parsea `message` del formulario para extraer datos del lead
- Muestra plan recomendado real con precio, score, prestadores, UF

### 3.6 Cotizador standalone (`pages/cotizador.php`) ✅
- Formulario interactivo con edad, renta, cargas, intereses
- Resultados con score rings, badges ISAPRE, razones
- Auto-ejecuta al cargar la página

---

## REGISTRO DE CAMBIOS (sesión 2026-07-16)

| Hito | Qué cambió |
|---|---|
| CSV completo | 2,231 planes con 100% nombres (corrección `extract_nombre()`) |
| Tabla HTML | `pages/planes/tabla-planes.php` interactiva |
| Coberturas | Parseo de `revision_IA_Planes_isapre.csv` con normalización |
| Engine v1 | `core/cotizador_engine.py` reemplaza POC de 5 planes fake |
| API endpoint | `api/cotizar.php` POST → JSON |
| gracias.php | `motor_cotizacion_real()` integrado |
| Comparador upgrade | JS llama API real en vez de datos estáticos |
| CTA formulario | Botón dinámico → individual o familiar según cargas |
| API URL fix | `BASE_URL` dinámico en vez de `/plansaludfacil/...` hardcodeado |
| Factor etario | `_edad_factor()` — la edad ahora ×1.0 a ×5.5 el precio |
| Carga pricing | Cada carga +0.7 UF, el motor busca planes más baratos |
| Output price fix | `precio_clp` ahora incluye edad + cargas (antes solo UF base) |
| Cotización Adicional | Banners explicativos cuando planes exceden el 7% |
| SPEC actualizado | Este documento |

---

## PENDIENTE (próxima sesión)

### Widget JS de cotización en tiempo real
- `js/cotizador_widget.js` que llame a `api/cotizar.php` con debounce al cambiar inputs

### Precio personalizado por ISAPRE
- Cada ISAPRE tiene su propia tabla de factores (actualmente usamos tabla genérica)
- Scrapear tablas reales o usar datos de QuVi/Supersalud

---

## ARCHIVOS CLAVE

| Archivo | Rol |
|---|---|
| `adjuntos/planes_isapre.csv` | Catálogo 2,231 planes |
| `adjuntos/revision_IA_Planes_isapre.csv` | Coberturas por ISAPRE |
| `core/cotizador_engine.py` | Motor de scoring con pricing realista |
| `api/cotizar.php` | Endpoint HTTP |
| `pages/planes/comparador.php` | Comparador interactivo (conectado al motor) |
| `pages/cotizador.php` | Cotizador standalone |
| `pages/planes/tabla-planes.php` | Visualización interactiva del catálogo |
| `pages/gracias.php` | Página post-formulario (resultados reales) |
| `scripts/fill_missing_names.py` | Corrección de nombres (curl) |
| `SPEC/cotizador-engine.md` | Este documento |

## COMANDOS RÁPIDOS

```bash
# Test del motor
python3 core/cotizador_engine.py --test

# Probar API
curl -X POST http://localhost:8080/api/cotizar.php \
  -H "Content-Type: application/json" \
  -d '{"edad":30,"renta":1300000,"cargas":0}'

# Comparador
open http://localhost:8080/planes/comparador/

# Catálogo
open http://localhost:8080/pages/planes/tabla-planes.php
```

---

## AUDITORÍA GEMINI (2026-07-16)

### Hallazgos y correcciones

| # | Hallazgo | Estado |
|---|---|---|
| 1 | **Tabla Única de Factores (TFU) correcta** — Circular IF/N° 343 es la única válida para planes nuevos. No existen tablas por ISAPRE. | ✅ Ya implementado |
| 2 | **Secuencia del algoritmo correcta** — Base UF → TFU → GES → × UF → vs 7%. | ✅ Ya implementado |
| 3 | **Primas GES genéricas** — Usábamos 1.2 UF para todos. Cada ISAPRE tiene su propia prima (0.74–1.30 UF). | ✅ Corregido (2026-07-16) |
| 4 | **Coberturas por ISAPRE, no por plan** — Asumimos misma cobertura para todos los planes de una ISAPRE. En realidad cada plan varía. | ⚠️ Pendiente (sin datos) |
| 5 | **Tope por prestación y anual** — El motor no modela el tope por evento ni el decaimiento de cobertura al llegar al tope anual. | ⚠️ Pendiente (complejidad alta) |
| 6 | **Red preferente vs libre elección** — No diferenciamos tipo de red en el scoring. | ⚠️ Pendiente |

### GES por ISAPRE (implementado)

| ISAPRE | GES (UF/mes) | Impacto en familia 2 cargas |
|---|---|---|
| Cruz Blanca | 0.74 | +$0/mes (referencia) |
| Esencial | 0.91 | +$13,090/mes |
| Nueva Masvida | 1.02 | +$21,560/mes |
| Banmédica | 1.10 | +$27,720/mes |
| Vida Tres | 1.12 | +$29,260/mes |
| Consalud | 1.25 | +$39,270/mes |
| Colmena | 1.30 | +$43,120/mes |

Diferencia máxima: $43,120/mes entre la ISAPRE más barata y la más cara en GES para la misma familia.

### Coberturas por plan (PDFs)

**Situación:** Los 2,231 planes tienen datos de cobertura solo a nivel ISAPRE en `revision_IA_Planes_isapre.csv`. Para obtener coberturas por plan se necesitaría:

1. **Fuente oficial:** Superintendencia de Salud (SUSESO) publica el "Plan de Salud" de cada plan como documento oficial. Estos PDFs contienen la tabla de bonificaciones completa.
   - URL probable: `https://www.suseso.cl/planes-isapres` o similar
   - Cada plan tiene un código único que podría mapearse a un PDF

2. **Fuente alternativa:** QuVi.cl ya procesa estos datos para su comparador. El endpoint del recomendador podría devolver coberturas estructuradas.

3. **Estimación:** 2,231 PDFs × ~5 páginas c/u = ~11,000 páginas. Con OCR y parser de tablas, ~2-3 semanas de trabajo.

**Recomendación:** No abordar ahora. Primero validar el motor con los datos actuales. Si la precisión es aceptable para el negocio, postergar. Si la competencia ofrece coberturas por plan y es un diferenciador crítico, priorizar.

---

## AUDITORÍA UX — Fricciones en el Cotizador (2026-07-17)

Basado en análisis de fricciones para cotizadores de ISAPRE en Chile.

### 1. Transparencia de precio ✅

| Fricción | Estado | Qué hacemos |
|---|---|---|
| Planes en UF confunden | ✅ Resuelto | Siempre mostramos CLP + UF |
| Falta valor UF real | ✅ Resuelto | $38.500 CLP/UF, visible en resultados |
| No se contrasta con 7% | ✅ Resuelto | Cada plan muestra "✓ Cubre 7%" o "+$X adicional" |

### 2. Complejidad técnico-legal ⚠️

| Fricción | Estado | Pendiente |
|---|---|---|
| Conceptos sin explicar (CAEC, copago, deducible) | ⚠️ Parcial | No hay glosario ni tooltips en el comparador |
| Tabla FUB ilegible | ⚠️ No aplica | No mostramos la FUB, mostramos cards simplificadas |

### 3. Selección de coberturas ⚠️

| Fricción | Estado | Pendiente |
|---|---|---|
| Balance ambulatorio vs hospitalario | ⚠️ Parcial | Mostramos coberturas pero no guiamos al usuario |
| Filtros por clínica ("Clínica Alemana") | ❌ No implementado | Solo filtramos por ISAPRE y prestadores |
| Factor edad no explicado | ✅ Resuelto | Se muestra "Factor etario ×1.5 por edad (35 años)" |

### 4. Captura de datos (Lead Gen) ⚠️

| Fricción | Estado | Detalle |
|---|---|---|
| DPS antes de mostrar precios | ✅ Resuelto | El comparador no pide datos médicos |
| Resultados bloqueados tras formulario | ⚠️ Dual | Comparador: resultados libres. Formulario: pide datos antes |
| Temor a spam telefónico | ⚠️ Parcial | Hay política de privacidad pero no sellos visibles |

### 5. Sobrecarga cognitiva ⚠️

| Fricción | Estado | Detalle |
|---|---|---|
| Exceso de campos | ⚠️ Parcial | Comparador: 3 campos ✓. Formulario: 10+ campos ✗ |
| Validación en tiempo real | ⚠️ Parcial | Renta muestra 7% en vivo. Edad y cargas no validan hasta submit |
| Progreso visible | ❌ No implementado | No hay barra de progreso en el formulario |

### Prioridades (próximos pasos)

1. **Tooltips en el comparador** — explicar "CAEC", "Tope anual", "Prestadores" con hover
2. **Filtro por clínica** — usar el campo `red` del CSV de coberturas
3. **Reducir campos del formulario** — de 10+ a 5 esenciales
4. **Barra de progreso** en formulario multi-step
5. **Sellos de confianza** visibles (SSL, política de privacidad, "sin spam")

