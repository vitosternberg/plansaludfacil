import json
with open('/tmp/cot127.json') as f:
    d = json.load(f)
for r in d['recomendaciones'][:5]:
    s = r['score']
    reasons = [x for x in r.get('razones', []) if 'cobertura' in x.lower() or 'Cobertura' in x]
    flag = 'COV' if reasons else 'NOCOV'
    print(f"{s}/100 {flag:6s} | {r['isapre']:15s} {r['nombre'][:45]}")
