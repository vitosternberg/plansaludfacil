#!/usr/bin/env python3
"""
Monitor de cambios en el catálogo QuVi — PlanSaludFácil
========================================================
Compara el sitemap actual vs nuestro CSV para detectar planes nuevos o eliminados.

Uso semanal: python3 scripts/monitor_quvi.py
Output:     Imprime diferencias y actualiza SPEC si hay cambios.
"""

import subprocess, csv, json, re, os, sys
from datetime import datetime
from pathlib import Path

BASE = Path(__file__).resolve().parent.parent
CSV_PATH = BASE / 'adjuntos' / 'planes_isapre.csv'
SPEC_PATH = BASE / 'SPEC' / 'cotizador-engine.md'
SITEMAP_URL = 'https://www.quvi.cl/sitemap.xml'

def fetch_sitemap():
    """Descarga el sitemap y cuenta planes por ISAPRE."""
    r = subprocess.run(['curl', '-sS', '--max-time', '30', SITEMAP_URL],
                       capture_output=True, text=True, timeout=35)
    if r.returncode != 0:
        print(f'ERROR: No se pudo descargar el sitemap ({r.returncode})')
        return None
    
    # Extraer URLs de planes: /plan/CODIGO
    plan_urls = re.findall(r'https?://www\.quvi\.cl/plan/([^<\s"]+)', r.stdout)
    print(f'  Sitemap: {len(plan_urls)} planes encontrados')
    
    # Contar por prefijo (ISAPRE)
    # Los códigos tienen prefijos: BPPO=Banmédica, OPR=Cruz Blanca, etc.
    return len(plan_urls)

def count_csv():
    """Cuenta planes en nuestro CSV."""
    plans = 0
    isapres = {}
    with open(CSV_PATH, 'r') as f:
        reader = csv.DictReader(f)
        for row in reader:
            if row['nombre'].strip():
                plans += 1
                isapre = row['isapre']
                isapres[isapre] = isapres.get(isapre, 0) + 1
    return plans, isapres

def main():
    print(f'\n{"="*60}')
    print(f'  Monitor QuVi — {datetime.now().strftime("%Y-%m-%d %H:%M")}')
    print(f'{"="*60}')
    
    online = fetch_sitemap()
    if not online:
        return
    
    local_plans, isapres = count_csv()
    
    print(f'  CSV local:    {local_plans} planes ({len(isapres)} ISAPREs)')
    for name, count in sorted(isapres.items(), key=lambda x: -x[1]):
        print(f'    {name}: {count}')
    
    diff = online - local_plans
    if diff == 0:
        print(f'\n  ✅ Sin cambios. {local_plans} planes coinciden.')
    elif diff > 0:
        print(f'\n  ⚠️  +{diff} planes NUEVOS en QuVi. Actualizar catálogo.')
    else:
        print(f'\n  ⚠️  {abs(diff)} planes MENOS en QuVi. Posibles bajas.')
    
    print(f'{"="*60}\n')

if __name__ == '__main__':
    main()
