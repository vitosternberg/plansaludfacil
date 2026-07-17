# Tabla de Control — Auditoría de Páginas por Isapre

**Fecha:** 2026-07-15  
**Objetivo:** Verificar que las 6 páginas tengan el mismo estándar y solo datos reales.

## Estructura de secciones

| Página | Coberturas | Precios (tablas) | Diferenciador | ¿Es para ti? | Total |
|--------|-----------|-----------------|---------------|-------------|-------|
| Banmédica | ✅ | ✅ 3+3 filas | ✅ Red prestadores | ✅ | 4 |
| Colmena | ✅ | ✅ 3+3 filas | ✅ Maternidad | ✅ | 4 |
| Cruz Blanca | ✅ | ✅ 3+3 filas | ❌ **FALTA** | ✅ | 3 |
| Consalud | ✅ | ✅ 3+3 filas | ✅ Regiones | ✅ | 4 |
| Nueva MasVida | ✅ | ✅ 3+0* filas | ✅ Adulto Mayor | ✅ | 4 |
| Vida Tres | ✅ | ✅ 2+0* filas | ✅ Experiencia VIP | ✅ | 4 |

*\* Sin datos de carga en queplan.cl*

---

## Análisis de contenido: ¿real o inventado?

| Elemento | Banmédica | Colmena | Cruz Blanca | Consalud | Nva MasVida | Vida Tres |
|----------|-----------|---------|-------------|----------|-------------|-----------|
| **Precios** | ✅ real (queplan) | ✅ real (queplan) | ✅ real (queplan) | ✅ real (queplan) | ✅ real (queplan) | ✅ real (queplan) |
| **Coberturas** | ⚠️ parcial | ⚠️ parcial | ⚠️ parcial | ⚠️ parcial | ⚠️ parcial | ⚠️ parcial |
| **"Ideal para"** | ⚠️ subjetivo | ⚠️ subjetivo | ⚠️ subjetivo | ⚠️ subjetivo | ⚠️ subjetivo | ⚠️ subjetivo |
| **Diferenciador** | ⚠️ sin fuente | ⚠️ sin fuente | ❌ inexistente | ⚠️ sin fuente | ⚠️ sin fuente | ❌ inventado |
| **Clínicas citadas** | ✅ reales (CSV) | ❌ ninguna | ❌ ninguna | ❌ ninguna | ❌ ninguna | ✅ reales (CSV) |
| **Historia corporativa** | ❌ no incluida | ❌ no incluida | ❌ no incluida | ❌ no incluida | ❌ no incluida | ❌ no incluida |

---

## Problemas detectados

### 🔴 Críticos
1. **Cruz Blanca**: solo 3 secciones, falta una sección diferenciadora (ej: "Deporte y Kinesiología", "Salud Mental"). Ningún motivo real para elegirla vs otras.
2. **Vida Tres - "Experiencia VIP"**: sección completamente inventada ("ejecutivo 24/7", "segunda opinión médica internacional"). No hay fuente en PDF ni CSV.

### 🟡 Medios
3. **Coberturas genéricas**: todas dicen variantes de "buena cobertura ambulatoria, hospitalización, telemedicina, dental". Sin porcentajes reales del CSV/PDF. Ejemplo de cómo debería ser:
   ```
   ✅ "Cruz Blanca ofrece hasta 100% en kinesiología ambulatoria (tope 60 UF/año) y 90% en consulta médica"
   ❌ "Cruz Blanca tiene buena cobertura kinésica"  
   ```
4. **"Ideal para" repetitivos**: todas usan la misma estructura "ideal para X que buscan Y". Poco distintivo entre isapres.
5. **Sin clínicas**: Colmena, Cruz Blanca, Consalud, Nva MasVida no citan ninguna clínica real de su red (el CSV de `revision_IA_Planes_isapre.csv` lista las clínicas reales por isapre).

### 🟢 Correcto
6. ✅ Precios 100% reales, con fuente citada (queplan.cl, julio 2026)
7. ✅ Estructura de tablas consistente: Individuales + Carga
8. ✅ Breadcrumbs correctos en las 6
9. ✅ Formulario de cotización presente en las 6
10. ✅ FAQ única por isapre (no copiada)

---

## Acciones correctivas recomendadas

| # | Acción | Prioridad |
|---|--------|-----------|
| 1 | Agregar sección "Deporte y Kinesiología" a Cruz Blanca | ALTA |
| 2 | Reescribir "Experiencia VIP" de Vida Tres con datos reales del CSV | ALTA |
| 3 | Agregar datos de cobertura con % reales del CSV a cada página | MEDIA |
| 4 | Incluir historia corporativa del CSV (`revision_IA_Planes_isapre.csv`) en cada página | MEDIA |
| 5 | Listar clínicas reales por isapre usando el CSV | MEDIA |
| 6 | Diferenciar mejor los "Ideal para" (que no todos suenen igual) | BAJA |
