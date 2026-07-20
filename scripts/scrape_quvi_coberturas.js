#!/usr/bin/env node
/**
 * Scrape coberturas de quvi.cl — extracción por texto completo + regex.
 * 2,231 planes ≈ 25-35 min. Guarda progreso cada 100.
 * Uso: node scripts/scrape_quvi_coberturas.js
 */

const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const URLS_FILE = path.join(__dirname, '..', 'adjuntos', 'quvi_plan_urls.txt');
const OUT_FILE  = path.join(__dirname, '..', 'adjuntos', 'quvi_coberturas.csv');
const FAIL_FILE = path.join(__dirname, '..', 'adjuntos', 'quvi_cobertura_failed.txt');

// Load URLs
const raw = fs.readFileSync(URLS_FILE, 'utf8');
const urls = raw.split('\n')
    .map(line => line.trim())
    .filter(line => line.includes('quvi.cl/plan/'))
    .map(line => {
        const parts = line.split(',');
        const code = parts.length > 1 ? parts[0].trim() : '';
        const url = parts.length > 1 ? parts[parts.length - 1].trim() : line;
        return { code, url: url.startsWith('http') ? url : `https://www.quvi.cl/plan/${code}` };
    });

console.log(`Total plans: ${urls.length}`);

const results = [];
const failed = [];

function saveProgress() {
    if (results.length === 0) return;
    const keys = ['codigo', 'url', 'isapre', 'nombre', 'linea', 'cobertura_hospitalaria',
                  'cobertura_ambulatoria', 'tope_anual', 'nota_global', 'costo_uf', 'costo_clp', 'prestadores'];
    const header = keys.join(',');
    const rows = results.map(r => keys.map(k => r[k] || '').join(','));
    fs.writeFileSync(OUT_FILE, [header, ...rows].join('\n'));
    fs.writeFileSync(FAIL_FILE, failed.join('\n'));
}

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        viewport: { width: 1440, height: 900 }
    });
    const page = await context.newPage();

    for (let i = 0; i < urls.length; i++) {
        const { code, url } = urls[i];
        const planCode = code || url.split('/plan/')[1]?.split('?')[0] || url.split('/').pop();
        
        try {
            await page.goto(url, { waitUntil: 'networkidle', timeout: 15000 });
            await page.waitForTimeout(2000);

            // Extract using regex from full body text
            const data = await page.evaluate(() => {
                const text = document.body?.innerText || '';
                const result = {};

                // Helper: extract value by label pattern
                const extract = (label) => {
                    const re = new RegExp(label + '[:\s]*([^\n]{2,60})', 'i');
                    const m = text.match(re);
                    return m ? m[1].trim() : '';
                };

                result.isapre = extract('Isapre');
                result.nombre = extract('Con Prestadores') || extract('Preferente') || '';
                result.linea = extract('Línea');
                result.cobertura_hospitalaria = extract('Cobertura hospitalaria');
                result.cobertura_ambulatoria = extract('Cobertura ambulatoria');
                result.tope_anual = extract('Tope anual');
                result.nota_global = extract('Nota Global');

                // Prestadores: search for "N prestadores" pattern
                const presMatch = text.match(/(\d+)\s*prestadores/);
                if (presMatch) result.prestadores = presMatch[1];

                // Costo: search for "X,XX UF · $XXX.XXX" or "X,XX UF"
                const costMatch = text.match(/(\d+[,.]\d{2})\s*UF\s*[·]\s*\$?([\d.]+)/);
                if (costMatch) {
                    result.costo_uf = costMatch[1];
                    result.costo_clp = costMatch[2];
                } else {
                    const ufMatch = text.match(/(\d+[,.]\d{2})\s*UF/);
                    if (ufMatch) result.costo_uf = ufMatch[1];
                }

                return result;
            });

            // Determine success: at least one coverage value found
            const hasData = data.cobertura_hospitalaria || data.cobertura_ambulatoria;
            
            if (hasData) {
                data.codigo = planCode;
                data.url = url;
                results.push(data);
                console.log(`[${i+1}/${urls.length}] ${planCode}: H=${data.cobertura_hospitalaria?.slice(0,10)} A=${data.cobertura_ambulatoria?.slice(0,10)}`);
            } else {
                failed.push(planCode);
                console.log(`[${i+1}/${urls.length}] ${planCode}: sin datos de cobertura`);
            }

        } catch (e) {
            failed.push(planCode);
            console.log(`[${i+1}/${urls.length}] ${planCode}: error - ${e.message?.slice(0,50)}`);
        }

        if ((i + 1) % 100 === 0) {
            saveProgress();
            console.log(`  → ${results.length} con datos, ${failed.length} fallidos. Guardado.`);
        }
    }

    await browser.close();
    saveProgress();
    console.log(`\n=== FINAL ===`);
    console.log(`Planes con cobertura: ${results.length}/${urls.length}`);
    console.log(`Fallidos: ${failed.length}`);
    console.log(`CSV: ${OUT_FILE}`);
})();
