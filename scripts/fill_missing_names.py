#!/usr/bin/env python3
"""Fill missing 'nombre' field using curl with hard timeouts."""
import csv, re, subprocess, time, os

CSV_PATH = 'adjuntos/planes_isapre.csv'
BACKUP_PATH = 'adjuntos/planes_isapre.csv.bak'

def fetch(url, timeout=15):
    """Fetch URL via curl with hard timeout."""
    try:
        result = subprocess.run([
            'curl', '-sS', '--max-time', str(timeout),
            '-H', 'User-Agent: Mozilla/5.0 QuViBot/1.0',
            '-H', 'Accept: text/html',
            url
        ], capture_output=True, text=True, timeout=timeout + 5)
        if result.returncode == 0 and result.stdout:
            return result.stdout
        return None
    except Exception:
        return None

def extract_nombre(html):
    """Extract plan name from QuVi plan detail page."""
    text = re.sub(r'<[^>]+>', ' ', html)
    text = re.sub(r'\s+', ' ', text)
    
    m = re.search(r'Nombre\s+del\s+plan\s+(.{3,120})', text)
    if m:
        name = m.group(1).strip()
        for delim in ['Precio base', 'Isapre ', 'Código del', 'Este es']:
            idx = name.find(delim)
            if idx > 0:
                name = name[:idx].strip()
        if name and len(name) >= 2:
            return name
    
    title_match = re.search(r'<title>([^<]+)</title>', html)
    if title_match:
        title = title_match.group(1)
        for sep in [' — ', ' · ', ' | ']:
            if sep in title:
                title = title.split(sep)[0].strip()
                break
        title = re.sub(r'\s+(Banmédica|Colmena|Consalud|Cruz\s+Blanca|Esencial|Nueva\s+Masvida|Vida\s+Tres)\s+\d{4}$', '', title).strip()
        if title and len(title) >= 2:
            return title
    return ''

# ─── Backup ───
if not os.path.exists(BACKUP_PATH):
    subprocess.run(['cp', CSV_PATH, BACKUP_PATH])
    print(f'Backup: {BACKUP_PATH}')

# ─── Load ───
rows = []
missing = 0
with open(CSV_PATH, 'r', encoding='utf-8') as f:
    reader = csv.DictReader(f)
    fieldnames = reader.fieldnames
    for row in reader:
        rows.append(row)
        if not row.get('nombre', '').strip():
            missing += 1

print(f'Rows: {len(rows)} | Missing: {missing}')

# ─── Fill ───
filled = 0
errors = 0
start = time.time()

for i, row in enumerate(rows):
    if row['nombre'].strip():
        continue
    
    html = fetch(row['url'])
    if not html:
        errors += 1
        if errors <= 5:
            print(f'  ERR [{i}] {row["url"]}')
        continue
    
    name = extract_nombre(html)
    if name:
        row['nombre'] = name
        filled += 1
    else:
        errors += 1
        if errors <= 5:
            print(f'  NO_NAME [{i}] {row["url"]}')
    
    done = filled + errors
    if done % 25 == 0:
        elapsed = time.time() - start
        rate = done / elapsed if elapsed > 0 else 0
        eta = (missing - done) / rate if rate > 0 else 0
        print(f'  [{done}/{missing}] ok={filled} err={errors} | {rate:.1f}/s | ETA {eta/60:.0f}m')
        # Save
        with open(CSV_PATH, 'w', newline='', encoding='utf-8') as f:
            w = csv.DictWriter(f, fieldnames=fieldnames)
            w.writeheader()
            w.writerows(rows)
    
    time.sleep(0.4)  # Polite

# ─── Final ───
with open(CSV_PATH, 'w', newline='', encoding='utf-8') as f:
    w = csv.DictWriter(f, fieldnames=fieldnames)
    w.writeheader()
    w.writerows(rows)

elapsed = time.time() - start
still = sum(1 for r in rows if not r['nombre'].strip())
print(f'\nDone: {filled} filled, {errors} errors, {still} remaining | {elapsed/60:.1f}m')
