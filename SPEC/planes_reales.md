-- Planes de Isapre reales (extraídos de PDFs en /adjuntos/)
-- Los montos están en UF o VA (Veces Arancel). No incluyen precios mensuales (no vienen en los PDFs).
-- Coberturas expresadas como % de bonificación.

-- ── COLMENA ─────────────────────────────────────────────
-- COLMENA BLUE 22610080 (Individual)
-- COLMENA ELITE 12610080 (Individual, premium)
-- COLMENA SILVER 2266060 (Individual)

-- ── NUEVA MASVIDA ───────────────────────────────────────
-- Pleno Salud PS260612 (Individual) — prestadores preferentes
-- Pleno Salud PS260616, PS260618 (variantes)

-- ── BANMÉDICA ───────────────────────────────────────────
-- SOLUCION 2 1400 526 (Individual) — prestadores preferentes
-- SOLN214526 — 100% clínica top, 90% media, 80% estándar

-- ── CRUZ BLANCA ─────────────────────────────────────────
-- CBMR001526 al CBMR045526 (~45 variantes) — "CBMR" = Cruz Blanca Medical Range?
-- PROR106526 al PROR212526 (~16 variantes) — "PROR" = ?
-- PROT101526 al PROT314626 (~20 variantes) — "PROT" = ?

-- ── ESTRUCTURA COMÚN DE TODOS LOS PDFs ──────────────────
-- Todos siguen el mismo formato legal (Plan de Salud Complementario):
--   1. HOSPITALARIAS: Día Cama, UCI, Pabellón, Exámenes, Imagenología, Honorarios, Medicamentos
--   2. AMBULATORIAS: Consulta, Telemedicina, Exámenes, Kinesiología, PAD Dental, Pabellón Amb.
--   3. URGENCIAS: Copago fijo simple/compleja por clínica
--   4. PRESTACIONES RESTRINGIDAS: Cirugía bariátrica, fotorrefractiva, etc.
--   5. TOPES: Por evento y por beneficiario/año en UF

-- ── TABLA DE COBERTURAS REALES ─────────────────────────

-- COLMENA BLUE (plan medio)
-- | Prestación              | Preferente | Libre Elección | Tope evento |
-- |-------------------------|------------|----------------|-------------|
-- | Día Cama                | 100%       | 90%            | —           |
-- | Día Cama UCI            | 100%       | 90%            | —           |
-- | Consulta médica         | 100%       | 60%            | —           |
-- | Kinesiología            | 80%        | 60%            | 20 UF       |
-- | Dental PAD              | 60%        | 60%            | 1-2 UF      |
-- | Tope hosp./año          | 250 UF     | 250 UF         | —           |
-- | Urgencia simple         | 0.4 UF     | —              | —           |
-- | Clínicas top            | 100%       | —              | —           |

-- NUEVA MASVIDA - Pleno Salud (plan alto)
-- | Prestación              | Preferente | Libre Elección | Tope evento |
-- |-------------------------|------------|----------------|-------------|
-- | Día Cama                | 90%        | 90%            | —           |
-- | Día Cama UCI            | 90%        | 90%            | —           |
-- | Consulta médica         | 100%       | 70%            | —           |
-- | Kinesiología            | 70%        | 50%            | 60 UF       |
-- | Tope hosp./año          | 260 UF     | 180 UF         | —           |
-- | Urgencia simple         | 1.2 UF     | —              | —           |
-- | Clínicas top            | 60%        | —              | —           |
-- | Clínicas media          | 80-90%     | —              | —           |

-- BANMÉDICA - Solución 2 (plan medio-alto)
-- | Prestación              | Preferente | Libre Elección | Tope evento |
-- |-------------------------|------------|----------------|-------------|
-- | Día Cama                | 100%       | 90%            | —           |
-- | Consulta médica         | 100%       | 60%            | —           |
-- | Tope hosp./año          | 600 UF     | 300 UF         | —           |
-- | Urgencia simple/compleja| 3.1/0.9 UF | —              | —           |
-- | Clínicas top            | 100%       | —              | —           |
-- | Clínicas media          | 80-90%     | —              | —           |

-- ── NOTAS ───────────────────────────────────────────────
-- 1. Ningún PDF incluye precio mensual del plan (eso se calcula con el 7% de la renta)
-- 2. Los códigos CBMR/PROR/PROT no identifican la isapre explícitamente — son códigos F.U.N.
-- 3. UF actual (julio 2026): ~$38.500 CLP
-- 4. VA = Veces Arancel (valor de referencia de Fonasa para cada prestación)
