#!/usr/bin/env python3
"""Extrae códigos de planes desde QuVi.cl y sus detalles."""
import json, re, csv, urllib.request, os, sys, time

OUTPUT = 'adjuntos/planes_isapre.csv'
ISAPRES_SLUGS = [
    'banmedica', 'colmena', 'consalud', 'cruz_blanca',
    'esencial', 'nueva_masvida', 'vida_tres'
]

def fetch(url, max_bytes=500000):
    """Fetch URL and return text. Retry once on failure."""
    for attempt in range(2):
        try:
            req = urllib.request.Request(url, headers={
                'User-Agent': 'Mozilla/5.0 QuViBot/1.0',
                'Accept': 'text/html'
            })
            with urllib.request.urlopen(req, timeout=15) as resp:
                return resp.read(max_bytes).decode('utf-8', errors='replace')
        except Exception as e:
            if attempt == 0:
                time.sleep(2)
            else:
                print(f'  ERROR fetching {url}: {e}')
                return None

def extract_plan_codes(html):
    """Extract plan codes + names from JSON-LD in HTML."""
    # Find the JSON-LD block
    match = re.search(r'<script type="application/ld\+json">(.*?)</script>', html, re.DOTALL)
    if not match:
        return []
    try:
        data = json.loads(match.group(1))
    except:
        return []
    
    # Navigate to itemListElement
    if isinstance(data, dict):
        graph = data.get('@graph', [])
    else:
        graph = data
    
    plans = []
    for item in graph:
        if item.get('@type') == 'ItemList':
            for elem in item.get('itemListElement', []):
                url = elem.get('url', '')
                code = url.split('/plan/')[-1] if '/plan/' in url else ''
                name = elem.get('name', '')
                if code and name:
                    plans.append((code, name))
    return plans

def extract_plan_details(html):
    """Extract UF, tope anual, prestadores from plan detail page."""
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

# ─── MAIN ───
all_rows = []
for slug in ISAPRES_SLUGS:
    print(f'\n🔍 {slug}...')
    url = f'https://www.quvi.cl/isapres/{slug}'
    html = fetch(url)
    if not html:
        continue
    
    plans = extract_plan_codes(html)
    print(f'   Encontrados {len(plans)} planes destacados')
    
    for code, name in plans:
        print(f'   📋 {code}: {name[:60]}...')
        plan_url = f'https://www.quvi.cl/plan/{code}'
        plan_html = fetch(plan_url)
        if not plan_html:
            continue
        
        details = extract_plan_details(plan_html)
        isapre_name = name.split('(')[0].strip() if '(' in name else ''
        # Infer isapre from slug
        isapre_map = {
            'banmedica': 'Banmédica', 'colmena': 'Colmena', 'consalud': 'Consalud',
            'cruz_blanca': 'Cruz Blanca', 'esencial': 'Esencial',
            'nueva_masvida': 'Nueva MasVida', 'vida_tres': 'Vida Tres'
        }
        isapre = isapre_map.get(slug, slug)
        
        all_rows.append({
            'isapre': isapre,
            'codigo': code,
            'nombre': name,
            'uf': details['uf'],
            'tope_anual_uf': details['tope_anual'],
            'prestadores_plan': details['prestadores'],
            'url': plan_url,
        })
        print(f'      UF={details["uf"]} | Tope={details["tope_anual"]}UF | Prest={details["prestadores"]}')
        time.sleep(0.5)  # Be polite
    
    time.sleep(1)

# ─── SAVE CSV ───
with open(OUTPUT, 'w', newline='', encoding='utf-8') as f:
    w = csv.DictWriter(f, fieldnames=['isapre','codigo','nombre','uf','tope_anual_uf','prestadores_plan','url'])
    w.writeheader()
    for row in all_rows:
        w.writerow(row)

print(f'\n✅ {len(all_rows)} planes guardados en {OUTPUT}')
