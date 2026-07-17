#!/usr/bin/env python3
"""Store per-isapre pricing tables from queplan.cl"""
import csv
import os
import sys

PATH = 'adjuntos/queplan_tablas_precios.csv'

def init_csv():
    """Create CSV if it doesn't exist, return existing rows and next id."""
    if os.path.exists(PATH):
        with open(PATH, 'r', encoding='utf-8') as f:
            reader = csv.DictReader(f)
            rows = list(reader)
        next_id = len(rows) + 1
        return rows, next_id
    else:
        with open(PATH, 'w', newline='', encoding='utf-8') as f:
            w = csv.writer(f)
            w.writerow(['id', 'isapre', 'categoria', 'perfil', 'precio_desde_clp', 'precio_hasta_clp'])
        return [], 1

def add_rows(isapre, categoria, rows_data):
    """rows_data: list of (perfil, precio_desde, precio_hasta)"""
    existing, next_id = init_csv()
    
    new_rows = []
    for perfil, desde, hasta in rows_data:
        new_rows.append({
            'id': str(next_id),
            'isapre': isapre,
            'categoria': categoria,
            'perfil': perfil,
            'precio_desde_clp': desde,
            'precio_hasta_clp': hasta,
        })
        next_id += 1
    
    file_exists = os.path.exists(PATH) and os.path.getsize(PATH) > 0
    with open(PATH, 'a', newline='', encoding='utf-8') as f:
        w = csv.DictWriter(f, fieldnames=['id','isapre','categoria','perfil','precio_desde_clp','precio_hasta_clp'])
        if not file_exists:
            w.writeheader()
        for row in new_rows:
            w.writerow(row)
    
    return new_rows

# ─── DATA ───────────────────────────────────────────────
isapre = 'Colmena'

individual = [
    ('Hombre 30 años',   '$85.724',  '$249.249'),
    ('Hombre 40 años',   '$99.253',  '$311.836'),
    ('Hombre 50 años',   '$103.762', '$332.698'),
    ('Mujer 30 años',    '$85.724',  '$249.249'),
    ('Mujer 40 años',    '$99.253',  '$311.836'),
    ('Mujer 50 años',    '$103.762', '$332.698'),
]

pareja = [
    ('Pareja 1 (hombre y mujer 30 años)',          '$171.447',  '$498.499'),
    ('Pareja 2 (hombre y mujer 40 años)',          '$198.505',  '$623.672'),
    ('Pareja 3 (hombre y mujer 50 años)',          '$207.525',  '$665.397'),
]

familia = [
    ('Familia 1 (hombre y mujer 30 años + 1 hijo 5 años)',   '$239.132',  '$664.299'),
    ('Familia 2 (hombre y mujer 40 años + 1 hijo 10 años)',  '$266.190',  '$789.472'),
    ('Familia 3 (hombre y mujer 50 años + 1 hijo 20 años)',  '$279.719',  '$852.059'),
]

# ─── WRITE ──────────────────────────────────────────────
r1 = add_rows(isapre, 'Individual', individual)
r2 = add_rows(isapre, 'Pareja', pareja)
r3 = add_rows(isapre, 'Familia', familia)

total = len(r1) + len(r2) + len(r3)
print(f'✅ Agregadas {total} filas para {isapre}')
print(f'   Individual: {len(r1)} | Pareja: {len(r2)} | Familia: {len(r3)}')
print(f'   CSV: {PATH}')

# Show summary
print()
print(f'{"Categoria":<12} {"Perfil":<42} {"Desde":>12} {"Hasta":>12}')
print('-' * 82)
for cat, rows in [('Individual', individual), ('Pareja', pareja), ('Familia', familia)]:
    for r in rows:
        print(f'{cat:<12} {r[0]:<42} {r[1]:>12} {r[2]:>12}')
