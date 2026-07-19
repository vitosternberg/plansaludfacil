import csv, re

with open('adjuntos/planes_isapre.csv') as f:
    reader = csv.DictReader(f)
    total = 0
    regional = 0
    by_region = {}
    examples = []
    for row in reader:
        total += 1
        name = row['nombre'].upper()
        tags = []
        if re.search(r'REG[.\s]*SUR|REGIONAL.*SUR', name): tags.append('SUR')
        if re.search(r'REG[.\s]*NORTE|REGIONAL.*NORTE', name): tags.append('NORTE')
        if re.search(r'\bCENTRO\b', name): tags.append('CENTRO')
        if tags:
            regional += 1
            for t in tags:
                by_region[t] = by_region.get(t, 0) + 1
            if len(examples) < 3:
                examples.append((row['isapre'], row['codigo'], row['nombre']))

print(f'Total: {total} planes')
print(f'Con restricción regional: {regional} ({round(regional*100/total,1)}%)')
print(f'Por tipo: {by_region}')
print('Ejemplos:')
for e in examples:
    print(f'  {e[0]:15s} {e[1]:15s} {e[2][:60]}')
