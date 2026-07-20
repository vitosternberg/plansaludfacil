import re, sys

with open('/tmp/quvi_test_plan.html') as f:
    html = f.read()

data = {}
patterns = {
    'isapre': r'Isapre\s*</[^>]*>\s*<[^>]*>\s*(\w[\w\s]+)',
    'codigo': r'Código\s*</[^>]*>\s*<[^>]*>\s*([\d\-A-Za-z]+)',
    'linea': r'Línea\s*</[^>]*>\s*<[^>]*>\s*([\w\s\-]+)',
    'nota_global': r'Nota Global\s*</[^>]*>\s*<[^>]*>\s*([\d.]+)',
    'cobertura_hosp': r'Cobertura hospitalaria\s*</[^>]*>\s*<[^>]*>\s*([^<]+)',
    'cobertura_amb': r'Cobertura ambulatoria\s*</[^>]*>\s*<[^>]*>\s*([^<]+)',
    'tope_anual': r'Tope anual\s*</[^>]*>\s*<[^>]*>\s*([^<]+)',
    'prestadores': r'([\d]+)\s*prestadores',
    'costo_uf': r'(\d+[,.]\d{2})\s*UF',
    'costo_clp': r'\$\s*([\d.]+)',
}

for key, pat in patterns.items():
    m = re.search(pat, html, re.IGNORECASE)
    if m:
        data[key] = m.group(1).strip()
        print(f'{key}: {data[key]}')

print(f"\nAll extracted: {len(data)}/10 fields")
