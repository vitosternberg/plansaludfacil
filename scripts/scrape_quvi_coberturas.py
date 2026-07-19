#!/usr/bin/env python3
"""Scrape coberturas reales de quvi.cl usando Playwright. Background task ~20-30 min."""

import csv, time, os

urls = []
with open('adjuntos/quvi_plan_urls.txt') as f:
    for line in f:
        line = line.strip()
        if line and 'quvi.cl/plan/' in line:
            parts = line.split(',')
            code = parts[0].strip() if len(parts) > 1 else ''
            url = parts[-1].strip() if ',' in line else line
            if not url.startswith('http'):
                url = f'https://www.quvi.cl/plan/{code}'
            urls.append((code, url))

print(f"Total plans: {len(urls)}")

from playwright.sync_api import sync_playwright, TimeoutError as PlaywrightTimeout

def save_results(data):
    if not data:
        return
    keys = ['codigo', 'url'] + sorted(set().union(*(d.keys() for d in data)) - {'codigo', 'url'})
    with open('adjuntos/quvi_coberturas.csv', 'w', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=keys, extrasaction='ignore')
        writer.writeheader()
        writer.writerows(data)

results = []
FAILED_FILE = 'adjuntos/quvi_cobertura_failed.txt'
failed = []

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(
        user_agent='Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
        viewport={'width': 1280, 'height': 800}
    )
    page = context.new_page()

    for i, (code, url) in enumerate(urls):
        if not code:
            code = url.rstrip('/').split('/')[-1]
        try:
            page.goto(url, wait_until='networkidle', timeout=15000)
            time.sleep(1.0)
            coberturas = page.evaluate("""() => {
                const result = {};
                const section = document.getElementById('pdViewCobertura');
                if (!section) return result;
                const text = section.textContent;
                const patterns = {
                    'hospitalaria': /[Hh]ospitalaria[:\s]*(\d{1,3})\s*%/,
                    'ambulatoria': /[Aa]mbulatoria[:\s]*(\d{1,3})\s*%/,
                    'caec': /CAEC[:\s]*(\d{1,3})\s*%/,
                    'urgencia': /[Uu]rgencia[:\s]*(\d{1,3})\s*%/,
                    'medicamentos': /[Mm]edicamentos?[:\s]*(\d{1,3})\s*%/,
                };
                for (k, ptn of Object.entries(patterns)) {
                    const m = text.match(ptn);
                    if (m) result[k] = parseInt(m[1]);
                }
                return result;
            }""")
            if coberturas:
                results.append({'codigo': code, 'url': url, **coberturas})
                print(f"[{i+1}/{len(urls)}] {code}: {coberturas}")
            else:
                failed.append(code)
                print(f"[{i+1}/{len(urls)}] {code}: sin datos")
        except PlaywrightTimeout:
            failed.append(code); print(f"[{i+1}/{len(urls)}] {code}: timeout")
        except Exception as e:
            failed.append(code); print(f"[{i+1}/{len(urls)}] {code}: {e}")
        
        if (i+1) % 100 == 0:
            save_results(results)
            with open(FAILED_FILE, 'w') as f: f.write('\n'.join(failed))

    browser.close()

save_results(results)
with open(FAILED_FILE, 'w') as f: f.write('\n'.join(failed))
print(f"Done. {len(results)} with data, {len(failed)} failed.")
