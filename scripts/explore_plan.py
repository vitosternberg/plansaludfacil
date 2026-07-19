import re, json, urllib.request, sys

# Fetch a plan page
url = "https://www.quvi.cl/plan/BSNB2602610"
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
with urllib.request.urlopen(req) as resp:
    html = resp.read().decode('utf-8')

# Extract __NEXT_DATA__
m = re.search(r'<script id="__NEXT_DATA__" type="application/json">(.*?)</script>', html, re.DOTALL)
if not m:
    print('No NEXT_DATA'); sys.exit(1)

data = json.loads(m.group(1))
page_props = data.get('props', {}).get('pageProps', {})

# Find plan detail
plan = None
def find_plan(d, path=''):
    if isinstance(d, dict):
        if 'codigo' in d and 'nota_global' in d:
            return d
        for k, v in d.items():
            r = find_plan(v, f'{path}.{k}')
            if r: return r
    return None

plan = find_plan(page_props)
if not plan:
    print("Plan not found. Dumping pageProps keys:")
    def dump_keys(d, depth=0):
        if depth > 2: return
        if isinstance(d, dict):
            for k, v in d.items():
                prefix = '  '*depth
                if isinstance(v, dict): print(f'{prefix}{k}: {{}}')
                elif isinstance(v, list): print(f'{prefix}{k}: [{len(v)}]')
                else: print(f'{prefix}{k}: {str(v)[:60]}')
                dump_keys(v, depth+1)
    dump_keys(page_props)
    sys.exit(1)

# Extract coverage data from plan
print("=== Plan Data ===")
for k in ['codigo', 'nombre', 'isapre_nombre', 'linea_nombre', 'nota_global', 'region', 'red_prestadores']:
    if k in plan:
        print(f"  {k}: {plan[k]}")

# Look for coberturas in plan or nested structures
for k, v in plan.items():
    if 'cobertura' in str(k).lower() or 'hospitalaria' in str(k).lower() or 'ambulatoria' in str(k).lower():
        print(f"  {k}: {str(v)[:200]}")

# Search for cobertura-related data recursively
def find_hospitalaria(d, path=''):
    if isinstance(d, dict):
        for k, v in d.items():
            if 'hospitalaria' in str(k).lower() or 'cobertura' in str(k).lower():
                print(f"  {path}.{k}: {str(v)[:200]}")
            find_hospitalaria(v, f'{path}.{k}')
    elif isinstance(d, list):
        for i, v in enumerate(d[:5]):
            find_hospitalaria(v, f'{path}[{i}]')

find_hospitalaria(plan, 'plan')

# Dump full plan structure (shallow)
print("\n=== All plan keys ===")
for k, v in plan.items():
    prefix = f"  {k}:"
    if isinstance(v, dict): print(f"{prefix} {{...{len(v)} keys}}")
    elif isinstance(v, list): print(f"{prefix} [{len(v)} items]")
    else: print(f"{prefix} {str(v)[:80]}")
