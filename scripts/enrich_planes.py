#!/usr/bin/env python3
"""Taggear planes con región basado en nombre y agregar columna al CSV."""
import csv, re

def detect_region(name, isapre):
    """Detecta la región de un plan por su nombre."""
    name_upper = name.upper()
    
    # Sur
    if re.search(r'REG[.\s]*SUR|REGIONAL.*SUR|\bSUR\b', name_upper):
        return 'sur'
    # Norte
    if re.search(r'REG[.\s]*NORTE|REGIONAL.*NORTE|\bNORTE\b', name_upper):
        return 'norte'
    # Centro (selectivo: no confundir con "CONCENTRA" o similares)
    if re.search(r'\bCENTRO\b', name_upper) and not re.search(r'CONCENTR', name_upper):
        return 'centro'
    # Disponible en todas las regiones
    return 'todas'

rows = []
with open('adjuntos/planes_isapre.csv') as f:
    reader = csv.DictReader(f)
    for row in reader:
        region = detect_region(row['nombre'], row['isapre'])
        row['region'] = region
        rows.append(row)

# Guardar con nueva columna
fieldnames = list(rows[0].keys())
with open('adjuntos/planes_isapre.csv', 'w', newline='') as f:
    writer = csv.DictWriter(f, fieldnames=fieldnames)
    writer.writeheader()
    writer.writerows(rows)

# Estadísticas
from collections import Counter
counts = Counter(r['region'] for r in rows)
for k, v in counts.most_common():
    print(f'{k}: {v} planes ({round(v*100/len(rows),1)}%)')

print(f'\nTotal: {len(rows)} planes')
print('Columna "region" agregada al CSV.')
