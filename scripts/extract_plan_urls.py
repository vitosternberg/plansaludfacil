#!/usr/bin/env python3
"""Extract all plan URLs from QuVi sitemap and fetch their details."""
import re, csv, urllib.request, time, os, sys

SITEMAP_PATH = 'adjuntos/quvi_sitemap.xml'
OUTPUT = 'adjuntos/planes_isapre.csv'

# Read sitemap
with open(SITEMAP_PATH) as f:
    text = f.read()

urls = list(set(re.findall(r'https://www\.quvi\.cl/plan/[^<\"]+', text)))
print(f'✅ {len(urls)} plan URLs únicos en el sitemap')

# Count by isapre
patterns = {
    '13-': 'Consalud', 'SOL': 'Cruz Blanca', 'CMBX': 'Cruz Blanca',
    'E': 'Esencial', 'IN': 'Esencial', 'SM': 'Esencial',
    'PPS': 'Nva MasVida', 'PM': 'Nva MasVida', 'PS': 'Nva MasVida',
    'MS': 'Colmena', 'PR': 'Colmena', 'ST': 'Colmena', 'MX': 'Colmena',
    'BP': 'Banmédica', 'BS': 'Banmédica',
    'VP': 'Vida Tres',
}
counts = {}
for u in urls:
    code = u.split('/plan/')[-1]
    found = False
    for pat, name in patterns.items():
        if code.startswith(pat):
            counts[name] = counts.get(name, 0) + 1
            found = True
            break
    if not found:
        counts['OTROS'] = counts.get('OTROS', 0) + 1
        print(f'  ? {code}')

for name, c in sorted(counts.items(), key=lambda x: -x[1]):
    print(f'  {name}: {c}')

# Save just the URLs for batching
with open('adjuntos/quvi_plan_urls.txt', 'w') as f:
    for u in sorted(urls):
        f.write(u + '\n')
print(f'\nURLs guardados en adjuntos/quvi_plan_urls.txt')
