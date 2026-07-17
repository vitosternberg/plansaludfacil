#!/usr/bin/env python3
"""Batch fetch ALL QuVi plan detail pages from sitemap URLs."""
import re, csv, urllib.request, time, os

URLS_FILE = 'adjuntos/quvi_plan_urls.txt'
OUTPUT_CSV = 'adjuntos/planes_isapre.csv'
MAX_FETCH = None  # None = all; set to N to limit

def fetch(url, max_bytes=100000):
    for attempt in range(2):
        try:
            req = urllib.request.Request(url, headers={
                'User-Agent': 'Mozilla/5.0 QuViBot/1.0',
                'Accept': 'text/html'
            })
            with urllib.request.urlopen(req, timeout=20) as resp:
                return resp.read(max_bytes).decode('utf-8', errors='replace')
        except Exception as e:
            if attempt == 0:
                time.sleep(2)
            else:
                return None

def extract_details(html):
    text = re.sub(r'<[^>]+>', ' ', html)
    text = re.sub(r'\s+', ' ', text)
    
    uf_match = re.search(r'Precio\s+base\s+mensual\s+([\d,.]+)\s*UF', text)
    tope_match = re.search(r'Tope\s+anual\s+([\d,.]+)\s*UF', text)
    prest_match = re.search(r'Prestadores\s+en\s+convenio\s+(\d+)', text)
    
    return {
        'uf': uf_match.group(1) if uf_match else '',
        'tope_anual': tope_match.group(1) if tope_match else '',
        'prestadores': prest_match.group(1) if prest_match else '',
    }

def extract_isapre(html):
    match = re.search(r'Isapre\s+(Banmédica|Colmena|Consalud|Cruz\s+Blanca|Esencial|Nueva\s+Masvida|Vida\s+Tres)', html)
    return match.group(1) if match else ''

def extract_nombre(html):
    # Extract plan name from title or structured text
    # Usually: "SALUD PLATINUM ONE 1012/2607 (H) — Plan de Isapre Banmédica"
    title_match = re.search(r'<title>([^<]+)</title>', html)
    if title_match:
        title = title_match.group(1)
        parts = title.split('—')
        if len(parts) >= 2:
            return parts[0].strip().replace('  ', ' ')
    # Fallback: look for "Nombre del plan"
    plan_match = re.search(r'Nombre\s+del\s+plan\s+([A-Z0-9][^<]{5,100})', html)
    return plan_match.group(1).strip() if plan_match else ''

# ─── Load existing codes ───
existing_codes = set()
if os.path.exists(OUTPUT_CSV):
    with open(OUTPUT_CSV, 'r') as f:
        reader = csv.DictReader(f)
        for row in reader:
            existing_codes.add(row.get('codigo', ''))

print(f'Códigos ya descargados: {len(existing_codes)}')

# ─── Load URLs ───
with open(URLS_FILE) as f:
    urls = [line.strip() for line in f if line.strip()]

new_urls = []
for u in urls:
    code = u.split('/plan/')[-1]
    if code not in existing_codes:
        new_urls.append(u)

print(f'URLs totales: {len(urls)} | Ya descargados: {len(existing_codes)} | Pendientes: {len(new_urls)}')

if MAX_FETCH:
    new_urls = new_urls[:MAX_FETCH]
    print(f'Limitado a {MAX_FETCH}')

# ─── Fetch ───
fieldnames = ['isapre','codigo','nombre','uf','tope_anual_uf','prestadores_plan','url']
new_rows = []
errors = 0
start = time.time()

for i, url in enumerate(new_urls):
    code = url.split('/plan/')[-1]
    
    if (i + 1) % 50 == 0:
        elapsed = time.time() - start
        rate = (i + 1) / elapsed
        remaining = (len(new_urls) - i - 1) / rate if rate > 0 else 0
        print(f'  [{i+1}/{len(new_urls)}] {rate:.1f}/s | ETA {remaining/60:.0f}min | Errores: {errors}')
    
    html = fetch(url)
    if not html:
        errors += 1
        continue
    
    details = extract_details(html)
    isapre = extract_isapre(html)
    plan_name = extract_nombre(html)
    
    new_rows.append({
        'isapre': isapre,
        'codigo': code,
        'nombre': plan_name,
        'uf': details['uf'],
        'tope_anual_uf': details['tope_anual'],
        'prestadores_plan': details['prestadores'],
        'url': url,
    })
    
    # Append incrementally every 20 rows
    if len(new_rows) >= 20:
        with open(OUTPUT_CSV, 'a', newline='', encoding='utf-8') as f:
            w = csv.DictWriter(f, fieldnames=fieldnames)
            if os.path.getsize(OUTPUT_CSV) == 0:
                w.writeheader()
            for row in new_rows:
                w.writerow(row)
        new_rows = []
    
    time.sleep(0.3)

# Final flush
if new_rows:
    with open(OUTPUT_CSV, 'a', newline='', encoding='utf-8') as f:
        w = csv.DictWriter(f, fieldnames=fieldnames)
        if os.path.getsize(OUTPUT_CSV) == 0:
            w.writeheader()
        for row in new_rows:
            w.writerow(row)

elapsed = time.time() - start
total_done = len(existing_codes) + len(new_urls) - errors
print(f'\n✅ Terminado: {total_done} planes en {OUTPUT_CSV}')
print(f'   Errores: {errors} | Tiempo: {elapsed/60:.1f} min')
