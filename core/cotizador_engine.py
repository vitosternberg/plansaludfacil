#!/usr/bin/env python3
"""
Motor de Cotización Real — PlanSaludFácil
Usa el catálogo QuVi (2,231 planes) + coberturas por ISAPRE (revision_IA).
Modo CLI: python3 core/cotizador_engine.py '<json_lead>'
Modo test: python3 core/cotizador_engine.py --test
"""

import csv, json, sys, os, re
from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(BASE_DIR / 'core'))
from isapre_pricing import calcular_precio, get_factor, GES_PRIMA_DEFAULT

# ─── 1. CARGAR CATÁLOGO DE PLANES ────────────────────────

def load_catalog():
    """Carga planes_isapre.csv → lista de dicts."""
    path = BASE_DIR / 'adjuntos' / 'planes_isapre.csv'
    planes = []
    with open(path, 'r', encoding='utf-8') as f:
        for row in csv.DictReader(f):
            if row['nombre'].strip() and row['uf'].strip():
                planes.append({
                    'isapre': row['isapre'].strip(),
                    'codigo': row['codigo'].strip(),
                    'nombre': row['nombre'].strip(),
                    'uf': _parse_num(row['uf']),
                    'tope_anual_uf': _parse_num(row['tope_anual_uf']),
                    'prestadores': int(row['prestadores_plan']) if row['prestadores_plan'].strip() else 0,
                    'url': row['url'].strip(),
                })
    return planes

def _parse_num(s):
    try: return float(s.replace(',', '.'))
    except: return 0.0

# ─── 2. CARGAR COBERTURAS POR ISAPRE ─────────────────────

def _parse_coverage_pct(s):
    """'80%-100%' → (80, 100), '90%' → (90, 90), '' → (0,0).
    Caps values at 100 (some rows have '80%-1000' typos)."""
    s = s.strip()
    if not s or s.upper() == 'N/A':
        return (0, 0)
    # Replace "a" separator: "100% a 90%" → "100% 90%"
    s = s.replace(' a ', ' ')
    nums = re.findall(r'(\d+)', s)
    nums = [min(int(n), 100) for n in nums]  # Cap at 100
    if len(nums) == 0: return (0, 0)
    if len(nums) == 1: return (nums[0], nums[0])
    return (min(nums), max(nums))  # min/max regardless of order

def _parse_tope(s):
    """'250-800 UF' → (250, 800), 'SIN TOPE' → (9999, 9999)"""
    s = s.strip()
    if not s or s.upper() in ('N/A', 'SIN TOPE'):
        return (9999, 9999)
    nums = re.findall(r'([\d.]+)', s.replace('.', ''))
    nums = [float(n) for n in nums]
    if len(nums) == 0: return (0, 0)
    if len(nums) == 1: return (nums[0], nums[0])
    return (nums[0], nums[-1])

def _parse_urgencia(s):
    """'0,4 UF-3,5 UF' → (0.4, 3.5)"""
    return _parse_tope(s.replace(',', '.'))

def load_coberturas():
    """Carga revision_IA_Planes_isapre.csv → dict por ISAPRE."""
    path = BASE_DIR / 'adjuntos' / 'revision_IA_Planes_isapre.csv'
    coberturas = {}
    with open(path, 'r', encoding='utf-8') as f:
        # Skip 2 header rows
        next(f); next(f)
        reader = csv.reader(f)
        for row in reader:
            if len(row) < 7:
                continue
            isapre_raw = row[0].strip()
            if not isapre_raw:
                continue
            
            isapre = _normalize_isapre(isapre_raw)
            
            hosp_pref = _parse_coverage_pct(row[2]) if len(row) > 2 else (0, 0)
            cons_pref = _parse_coverage_pct(row[3]) if len(row) > 3 else (0, 0)
            tope_pref = _parse_tope(row[4]) if len(row) > 4 else (0, 0)
            hosp_libre = _parse_coverage_pct(row[6]) if len(row) > 6 else (0, 0)
            cons_libre = _parse_coverage_pct(row[7]) if len(row) > 7 else (0, 0)
            tope_libre = _parse_tope(row[8]) if len(row) > 8 else (0, 0)
            urgencia = _parse_urgencia(row[10]) if len(row) > 10 else (0, 0)
            historia = row[11].strip() if len(row) > 11 else ''
            red = row[12].strip() if len(row) > 12 else ''
            
            if isapre not in coberturas:
                coberturas[isapre] = {
                    'hospitalaria_pref_min': hosp_pref[0],
                    'hospitalaria_pref_max': hosp_pref[1],
                    'consulta_pref_min': cons_pref[0],
                    'consulta_pref_max': cons_pref[1],
                    'tope_pref_min': tope_pref[0],
                    'tope_pref_max': tope_pref[1],
                    'hospitalaria_libre_min': hosp_libre[0],
                    'hospitalaria_libre_max': hosp_libre[1],
                    'consulta_libre_min': cons_libre[0],
                    'consulta_libre_max': cons_libre[1],
                    'urgencia_min': urgencia[0],
                    'urgencia_max': urgencia[1],
                    'historia': historia,
                    'red_prestadores': red,
                }
    
    # Fallback: if an ISAPRE is missing, use averages
    defaults = _compute_fallback(coberturas)
    return coberturas, defaults

def _normalize_isapre(name):
    name = name.strip()
    mapping = {
        'cruz blanca': 'Cruz Blanca',
        'nueva masvida': 'Nueva Masvida',
        'nueva más vida': 'Nueva Masvida',
        'banmédica': 'Banmédica',
        'banmedica': 'Banmédica',
        'vida tres': 'Vida Tres',
    }
    return mapping.get(name.lower(), name)

def _compute_fallback(coberturas):
    """Compute conservative averages across ISAPREs for fallback (midpoint of range)."""
    d = {'hospitalaria_pref': 70, 'consulta_pref': 60, 'hospitalaria_libre': 70, 'consulta_libre': 50}
    for field_max, field_min, field_out in [
        ('hospitalaria_pref_max', 'hospitalaria_pref_min', 'hospitalaria_pref'),
        ('consulta_pref_max', 'consulta_pref_min', 'consulta_pref'),
        ('hospitalaria_libre_max', 'hospitalaria_libre_min', 'hospitalaria_libre'),
        ('consulta_libre_max', 'consulta_libre_min', 'consulta_libre'),
    ]:
        mids = []
        for k in coberturas:
            mx = coberturas[k].get(field_max, 0)
            mn = coberturas[k].get(field_min, 0)
            if mx > 0 and mx <= 100 and mn > 0:
                mids.append((mn + mx) / 2)
            elif mx > 0 and mx <= 100:
                mids.append(mx)
        if mids:
            d[field_out] = sum(mids) / len(mids)
    return d

# ─── 3. SCORING ENGINE ───────────────────────────────────

# Mapeo de intereses del lead a campos de cobertura
INTERES_MAP = {
    'salud mental': 'consulta_pref_max',
    'kinesiología y deporte': 'consulta_pref_max',
    'telemedicina': 'consulta_libre_max',

    'atención ambulatoria': 'consulta_pref_max',
    'hospitalización': 'hospitalaria_pref_max',
    'maternidad': 'hospitalaria_pref_max',
    'dental': 'consulta_pref_max',
    'farmacia': 'consulta_pref_max',
}

def score_plan(plan, lead, cobertura, defaults):
    """
    Score a single plan against a lead profile.
    Returns (score_0_100, reasons_list).
    """
    score = 0.0
    reasons = []
    
    cov = cobertura if cobertura else defaults
    
    # ── 1. Price match vs 7% legal (35 pts) ──
    cotizacion_legal = lead['renta'] * 0.07
    uf_value = lead.get('uf_value', 38500)  # UF en CLP
    cargas = lead.get('cargas', 0)
    edad = lead.get('edad', 30)
    
    # ── Real ISAPRE pricing (Circular IF/N° 343) ──
    pricing = calcular_precio(
        plan_uf=plan['uf'], 
        edad_titular=edad, 
        cargas=cargas,
        uf_value=uf_value,
        isapre=plan['isapre']
    )
    precio_plan = pricing['total_clp']
    factor = pricing['factor_titular']
    
    ratio = precio_plan / cotizacion_legal if cotizacion_legal > 0 else 999
    extra = int(precio_plan - cotizacion_legal)
    
    if ratio <= 1.0:
        pts = 35
        reasons.append(f"Tu 7% legal (${int(cotizacion_legal):,}) cubre el plan completo")
    elif ratio <= 1.10:
        pts = 28
        reasons.append(f"Leve cotización adicional: +${extra:,}/mes")
    elif ratio <= 1.25:
        pts = 20
        reasons.append(f"Requiere ${extra:,} adicional mensual")
    elif ratio <= 1.5:
        pts = 12
        reasons.append(f"Requiere ${extra:,} adicional mensual")
    elif ratio <= 2.0:
        pts = 6
        reasons.append(f"Alto costo adicional: +${extra:,}/mes sobre tu 7%")
    else:
        pts = 2
        reasons.append(f"Muy por encima de tu 7%: +${extra:,}/mes adicional")
    
    if edad > 35:
        reasons.append(f"Factor etario ×{factor:.1f} por edad ({edad} años)")
    if cargas > 0:
        reasons.append(f"Incluye {cargas} carga(s): +{pricing['costo_cargas_uf']:.1f} UF total (plan × factor edad + GES {pricing['ges_prima']} UF c/u)")
    score += pts
    
    # ── 2. Cobertura según intereses (30 pts) ──
    if lead.get('intereses'):
        total_cov = 0
        interes_count = 0
        for interes in lead['intereses']:
            key = INTERES_MAP.get(interes.lower())
            if key is None:
                continue  # Skip interests not in mapping (e.g. Excedentes)
            
            # Get midpoint coverage value
            mid_cov = None
            if key in cov and cov[key] > 0:
                base_key = key.replace('_max', '')
                min_key = base_key + '_min'
                if min_key in cov and cov.get(min_key, 0) > 0:
                    mid_cov = (cov[min_key] + cov[key]) / 2
                else:
                    mid_cov = cov[key]
            
            if mid_cov is None or mid_cov == 0:
                # Try fallback: preferente if libre is 0
                if '_libre' in key:
                    alt_key = key.replace('_libre', '_pref')
                    if alt_key in cov and cov.get(alt_key, 0) > 0:
                        base_key = alt_key.replace('_max', '')
                        min_key = base_key + '_min'
                        if min_key in cov and cov.get(min_key, 0) > 0:
                            mid_cov = (cov[min_key] + cov[alt_key]) / 2
                        else:
                            mid_cov = cov[alt_key]
            
            if mid_cov is None or mid_cov == 0:
                mid_cov = defaults.get(key.replace('_max', ''), 50)
            
            total_cov += mid_cov
            interes_count += 1
        
        if interes_count > 0:
            avg_cov = total_cov / interes_count
            coverage_pts = (avg_cov / 100) * 30
            # Affordability discount: if plan is expensive, coverage matters less
            if ratio > 1.5:
                coverage_pts *= (1.5 / ratio)  # discount coverage for unaffordable plans
            score += coverage_pts
        
            if avg_cov >= 85:
                reasons.append(f"Excelente cobertura para tus intereses (~{avg_cov:.0f}%)")
            elif avg_cov >= 70:
                reasons.append(f"Buena cobertura para tus intereses (~{avg_cov:.0f}%)")
            else:
                reasons.append(f"Cobertura estándar para tus intereses (~{avg_cov:.0f}%)")
    
    # ── 3. Red de prestadores (15 pts) ──
    prest = plan['prestadores']
    if prest >= 30:
        pts = 15
        reasons.append(f"Red amplia: {prest} prestadores en convenio")
    elif prest >= 15:
        pts = 10
        reasons.append(f"Buena red: {prest} prestadores")
    elif prest >= 5:
        pts = 5
    else:
        pts = 2
    score += pts
    
    # ── 4. Bonus: sin cargas + plan económico (5 pts) ──
    if lead.get('cargas', 0) == 0 and plan['uf'] < 3.5:
        score += 5
        reasons.append("Plan individual optimizado (sin costo por cargas)")
    
    # ── 5. Bonus: ISAPRE conocida (5 pts) ──
    top_isapres = ['Banmédica', 'Cruz Blanca', 'Consalud']
    if plan['isapre'] in top_isapres:
        score += 3
    
    return round(min(score, 100), 1), reasons


def rank_plans(planes, lead, coberturas, defaults, top_n=5):
    """Score all plans, then diversify: top plan per ISAPRE + best remaining."""
    resultados = []
    for plan in planes:
        cov = coberturas.get(plan['isapre'])
        s, reasons = score_plan(plan, lead, cov, defaults)
        resultados.append((plan, s, reasons))
    
    resultados.sort(key=lambda x: x[1], reverse=True)
    
    # Diversify: pick top plan per ISAPRE first
    seen_isapres = set()
    diversified = []
    for item in resultados:
        isapre = item[0]['isapre']
        if isapre not in seen_isapres:
            diversified.append(item)
            seen_isapres.add(isapre)
        if len(diversified) >= top_n:
            break
    
    # Fill remaining slots with best remaining
    if len(diversified) < top_n:
        for item in resultados:
            if item not in diversified:
                diversified.append(item)
            if len(diversified) >= top_n:
                break
    
    return diversified[:top_n]


# ─── 4. MAIN ────────────────────────────────────────────

def main():
    if len(sys.argv) > 1 and sys.argv[1] == '--test':
        run_test()
        return
    
    # Modo JSON: recibe lead por stdin o argv
    if len(sys.argv) > 1:
        lead_json = sys.argv[1]
    else:
        lead_json = sys.stdin.read()
    
    lead = json.loads(lead_json)
    
    planes = load_catalog()
    coberturas, defaults = load_coberturas()
    
    top = rank_plans(planes, lead, coberturas, defaults, top_n=5)
    
    result = {
        'lead': lead,
        'cotizacion_legal_7pct': int(lead['renta'] * 0.07),
        'uf_value': lead.get('uf_value', 38500),
        'recomendaciones': []
    }
    
    for plan, score, reasons in top:
        cargas_lead = lead.get('cargas', 0)
        edad_lead = lead.get('edad', 30)
        pricing = calcular_precio(
            plan_uf=plan['uf'],
            edad_titular=edad_lead,
            cargas=cargas_lead,
            uf_value=lead.get('uf_value', 38500),
            isapre=plan['isapre']
        )
        precio_clp = pricing['total_clp']
        result['recomendaciones'].append({
            'isapre': plan['isapre'],
            'nombre': plan['nombre'],
            'codigo': plan['codigo'],
            'uf': plan['uf'],
            'precio_clp': precio_clp,
            'prestadores': plan['prestadores'],
            'tope_anual_uf': plan['tope_anual_uf'],
            'url': plan['url'],
            'score': score,
            'razones': reasons,
        })
    
    print(json.dumps(result, ensure_ascii=False, indent=2))


def run_test():
    """Quick test with sample lead."""
    lead = {
        "nombre": "Kathya Andrade",
        "edad": 27,
        "renta": 1300000,
        "cargas": 0,
        "uf_value": 38500,
        "intereses": ["Salud Mental", "Kinesiología y Deporte", "Telemedicina", "Excedentes"],
    }
    
    planes = load_catalog()
    coberturas, defaults = load_coberturas()
    
    print(f"Catálogo: {len(planes)} planes")
    print(f"Coberturas: {len(coberturas)} ISAPREs")
    print(f"Defaults: {defaults}")
    print(f"\nLead: {lead['nombre']}, {lead['edad']}a, ${lead['renta']:,}, 7%=${int(lead['renta']*0.07):,}")
    print(f"Intereses: {', '.join(lead['intereses'])}")
    print()
    
    top = rank_plans(planes, lead, coberturas, defaults, top_n=5)
    
    for i, (plan, score, reasons) in enumerate(top):
        precio = int(plan['uf'] * lead['uf_value'])
        print(f"{'🏆' if i == 0 else '  '} #{i+1} {plan['nombre']} ({plan['isapre']})")
        print(f"     Score: {score}/100 | UF: {plan['uf']} | ${precio:,}/mes | Prest: {plan['prestadores']}")
        for r in reasons:
            print(f"     ✓ {r}")
        print()

if __name__ == '__main__':
    main()
