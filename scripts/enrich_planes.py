#!/usr/bin/env python3
"""Cruzamos planes_isapre.csv con queplan_isapres_datos.csv para agregar coberturas."""
import csv

# Cargar coberturas por isapre
coberturas = {}
with open('adjuntos/queplan_isapres_datos.csv') as f:
    reader = csv.DictReader(f)
    for row in reader:
        isapre = row['isapre'].strip()
        coberturas[isapre] = {
            'cobertura_hosp_pct': int(row['cobertura_hosp_pct']),
            'cobertura_amb_pct': int(row['cobertura_amb_pct']),
        }

# Mapeo de nombres de isapre (planes_isapre.csv → queplan_isapres_datos.csv)
map_nombres = {
    'Banmédica': 'Banmédica',
    'Colmena': 'Colmena',
    'Consalud': 'Consalud',
    'Cruz Blanca': 'Cruz Blanca',
    'Esencial': 'Esencial',
    'Nueva Masvida': 'Nueva MasVida',
    'Vida Tres': 'Vida Tres',
}

# Enriquecer planes_isapre.csv
enriched = []
sin_cobertura = []
with open('adjuntos/planes_isapre.csv') as f:
    reader = csv.DictReader(f)
    for row in reader:
        isapre_orig = row['isapre'].strip()
        isapre_map = map_nombres.get(isapre_orig, isapre_orig)
        cov = coberturas.get(isapre_map, {})
        
        row['cobertura_hosp_pct'] = cov.get('cobertura_hosp_pct', '')
        row['cobertura_amb_pct'] = cov.get('cobertura_amb_pct', '')
        
        if not cov:
            sin_cobertura.append(isapre_orig)
        
        enriched.append(row)

# Guardar
fieldnames = ['isapre', 'codigo', 'nombre', 'uf', 'tope_anual_uf', 'prestadores_plan',
              'cobertura_hosp_pct', 'cobertura_amb_pct', 'url']

with open('adjuntos/planes_isapre.csv', 'w', newline='') as f:
    writer = csv.DictWriter(f, fieldnames=fieldnames, extrasaction='ignore')
    writer.writeheader()
    writer.writerows(enriched)

print(f'Enriquecidos: {len(enriched)} planes')
print(f'Sin cobertura: {len(sin_cobertura)} ({set(sin_cobertura)})')
print(f'Muestra Banmédica:')
for r in enriched[:3]:
    print(f"  {r['codigo']}: {r['nombre'][:50]} | H={r['cobertura_hosp_pct']}% A={r['cobertura_amb_pct']}% UF={r['uf']}")

# Backup
import shutil
shutil.copy('adjuntos/planes_isapre.csv', 'adjuntos/planes_isapre.csv.bak')
