#!/usr/bin/env python3
"""
QA Test Harness — Comparador ISAPRE
====================================
Ejecuta múltiples escenarios contra el motor y exporta resultados comparables.

Uso:
    python3 scripts/qa_cotizador.py                    # todos los escenarios
    python3 scripts/qa_cotizador.py --json             # salida JSON
    python3 scripts/qa_cotizador.py --escenario 3      # solo escenario 3
"""

import json, subprocess, sys, os

# ─── ESCENARIOS DE PRUEBA ───────────────────────────────
# Perfiles realistas del mercado chileno
ESCENARIOS = [
    {
        "id": 1,
        "nombre": "Joven profesional, soltero",
        "edad": 28, "renta": 1500000, "cargas": 0,
        "intereses": ["Kinesiología y Deporte", "Telemedicina", "Salud Mental"],
    },
    {
        "id": 2,
        "nombre": "Joven profesional, con pareja",
        "edad": 30, "renta": 1500000, "cargas": 1,
        "intereses": ["Maternidad", "Hospitalización", "Atención Ambulatoria"],
    },
    {
        "id": 3,
        "nombre": "Familia joven (2 hijos)",
        "edad": 35, "renta": 2000000, "cargas": 2,
        "intereses": ["Maternidad", "Hospitalización", "Dental", "Atención Ambulatoria"],
    },
    {
        "id": 4,
        "nombre": "Adulto medio, renta media",
        "edad": 45, "renta": 1800000, "cargas": 0,
        "intereses": ["Hospitalización", "Atención Ambulatoria", "Farmacia"],
    },
    {
        "id": 5,
        "nombre": "Adulto medio, familia",
        "edad": 48, "renta": 2500000, "cargas": 2,
        "intereses": ["Hospitalización", "Maternidad", "Dental"],
    },
    {
        "id": 6,
        "nombre": "Adulto mayor, renta alta",
        "edad": 58, "renta": 3500000, "cargas": 1,
        "intereses": ["Hospitalización", "Farmacia", "Atención Ambulatoria"],
    },
    {
        "id": 7,
        "nombre": "Adulto mayor, renta baja",
        "edad": 62, "renta": 900000, "cargas": 0,
        "intereses": ["Farmacia", "Hospitalización", "Atención Ambulatoria"],
    },
    {
        "id": 8,
        "nombre": "Renta mínima, soltero joven",
        "edad": 22, "renta": 600000, "cargas": 0,
        "intereses": ["Atención Ambulatoria", "Telemedicina"],
    },
    {
        "id": 9,
        "nombre": "Renta muy alta, familia numerosa",
        "edad": 40, "renta": 5500000, "cargas": 3,
        "intereses": ["Hospitalización", "Maternidad", "Dental", "Kinesiología y Deporte"],
    },
    {
        "id": 10,
        "nombre": "Tercera edad, pareja",
        "edad": 68, "renta": 1200000, "cargas": 1,
        "intereses": ["Farmacia", "Hospitalización"],
    },
]

ENGINE_PATH = os.path.join(os.path.dirname(__file__), '..', 'core', 'cotizador_engine.py')


def run_scenario(esc):
    """Ejecuta un escenario contra el motor y retorna resultados."""
    lead = json.dumps({
        'nombre': esc['nombre'],
        'edad': esc['edad'],
        'renta': esc['renta'],
        'cargas': esc['cargas'],
        'uf_value': 38500,
        'intereses': esc['intereses'],
    })
    
    r = subprocess.run(
        ['python3', ENGINE_PATH, lead],
        capture_output=True, text=True, timeout=30
    )
    
    if r.returncode != 0:
        return {'error': r.stderr}
    
    data = json.loads(r.stdout)
    
    # Extraer métricas clave
    pct7 = data['cotizacion_legal_7pct']
    results = []
    for rec in data['recomendaciones']:
        dentro = rec['precio_clp'] <= pct7
        results.append({
            'isapre': rec['isapre'],
            'plan': rec['nombre'][:60],
            'uf': rec['uf'],
            'precio_clp': rec['precio_clp'],
            'score': rec['score'],
            'prestadores': rec['prestadores'],
            'dentro_7pct': dentro,
            'ratio_vs_7pct': round(rec['precio_clp'] / pct7, 2) if pct7 > 0 else 999,
            'razon_principal': rec['razones'][0] if rec['razones'] else '',
        })
    
    return {
        'escenario': esc,
        'cotizacion_legal_7pct': pct7,
        'planes_dentro_7pct': sum(1 for r in results if r['dentro_7pct']),
        'top5': results,
    }


def print_text(results):
    """Formato texto legible."""
    for r in results:
        esc = r['escenario']
        print(f"\n{'='*70}")
        print(f"  #{esc['id']} {esc['nombre']}")
        print(f"  Edad: {esc['edad']} | Renta: ${esc['renta']:,} | Cargas: {esc['cargas']}")
        print(f"  Intereses: {', '.join(esc['intereses'])}")
        print(f"  7% legal: ${r['cotizacion_legal_7pct']:,}")
        print(f"  Planes dentro del 7%: {r['planes_dentro_7pct']}/5")
        print(f"  {'─'*60}")
        
        for i, plan in enumerate(r['top5']):
            icon = '✓' if plan['dentro_7pct'] else '✗'
            print(f"  {i+1}. [{plan['score']:5.1f}] {plan['isapre']:15s} {plan['plan'][:50]}")
            print(f"     ${plan['precio_clp']:,}/mes | {plan['uf']} UF | {plan['prestadores']} prest | {icon} (×{plan['ratio_vs_7pct']})")
            if plan['razon_principal']:
                print(f"     → {plan['razon_principal']}")


def print_json(results):
    """Formato JSON."""
    print(json.dumps(results, ensure_ascii=False, indent=2))


def print_summary(results):
    """Resumen ejecutivo."""
    print(f"\n{'='*80}")
    print(f"  RESUMEN QA — {len(results)} escenarios")
    print(f"  {'='*80}")
    print(f"  {'#':<3} {'Edad':<5} {'Renta':<12} {'Cargas':<7} {'7%':<12} {'#1 Plan':<20} {'Score':<7} {'Precio':<12} {'7%?'}")
    print(f"  {'-'*80}")
    for r in results:
        esc = r['escenario']
        pct7 = r['cotizacion_legal_7pct']
        top = r['top5'][0]
        dentro = '✓' if top['dentro_7pct'] else '✗'
        print(f"  {esc['id']:<3} {esc['edad']:<5} ${esc['renta']:<11,} {esc['cargas']:<7} ${pct7:<11,} {top['isapre']:<20} {top['score']:<7.1f} ${top['precio_clp']:<11,} {dentro}")


if __name__ == '__main__':
    escenario_id = None
    output_json = False
    
    args = sys.argv[1:]
    i = 0
    while i < len(args):
        if args[i] == '--json':
            output_json = True
        elif args[i] == '--escenario' and i + 1 < len(args):
            escenario_id = int(args[i+1])
            i += 1
        i += 1
    
    # Filtrar escenarios
    if escenario_id:
        escenarios = [e for e in ESCENARIOS if e['id'] == escenario_id]
    else:
        escenarios = ESCENARIOS
    
    # Ejecutar
    all_results = []
    for esc in escenarios:
        result = run_scenario(esc)
        all_results.append(result)
    
    # Output
    if output_json:
        print_json(all_results)
    else:
        print_text(all_results)
        if len(all_results) > 1:
            print_summary(all_results)
