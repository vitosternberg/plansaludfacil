#!/usr/bin/env node
/**
 * Scrape coberturas reales de quvi.cl usando Playwright (Node.js).
 * Uso: node scripts/scrape_quvi_coberturas.js
 */

const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const urlsFile = path.join(__dirname, '..', 'adjuntos', 'quvi_plan_urls.txt');
const outFile  = path.join(__dirname, '..', 'adjuntos', 'quvi_coberturas.csv');
const failFile = path.join(__dirname, '..', 'adjuntos', 'quvi_cobertura_failed.txt');

const raw = fs.readFileSync(urlsFile, 'utf8');
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
    const keys = ['codigo', 'url', ...new Set(results.flatMap(r => Object.keys(r).filter(k => k !== 'codigo' && k !== 'url')))];
    const header = keys.join(',');
    const rows = results.map(r => keys.map(k => r[k] || '').join(','));
    fs.writeFileSync(outFile, [header, ...rows].join('\n'));
    fs.writeFileSync(failFile, failed.join('\n'));
    console.log(`  → Saved ${results.length} results, ${failed.length} failed`);
}

(async () => {
    const browser = await chromium.launch({ headless: true, channel: 'chromium' });
    const context = await browser.newContext({
        userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
        viewport: { width: 1280, height: 800 }
    });
    const page = await context.newPage();

    for (let i = 0; i < urls.length; i++) {
        const { code, url } = urls[i];
        const planCode = code || url.split('/').pop();
        
        try {
            await page.goto(url, { waitUntil: 'networkidle', timeout: 15000 });
            await page.waitForTimeout(1000);

            const coberturas = await page.evaluate(() => {
                const result = {};
                const section = document.getElementById('pdViewCobertura');
                if (!section) return result;
                const text = section.textContent;
                
                const patterns = {
                    hospitalaria: /[Hh]ospitalaria[:\s]*(\d{1,3})\s*%/,
                    ambulatoria: /[Aa]mbulatoria[:\s]*(\d{1,3})\s*%/,
                    caec: /CAEC[:\s]*(\d{1,3})\s*%/,
                    urgencia: /[Uu]rgencia[:\s]*(\d{1,3})\s*%/,
                    medicamentos: /[Mm]edicamentos?[:\s]*(\d{1,3})\s*%/,
                };
                
                for (const [key, ptn] of Object.entries(patterns)) {
                    const m = text.match(ptn);
                    if (m) result[key] = parseInt(m[1]);
                }
                return result;
            });

            if (Object.keys(coberturas).length > 0) {
                results.push({ codigo: planCode, url, ...coberturas });
                console.log(`[${i+1}/${urls.length}] ${planCode}: ${JSON.stringify(coberturas)}`);
            } else {
                failed.push(planCode);
                console.log(`[${i+1}/${urls.length}] ${planCode}: sin datos`);
            }
        } catch (e) {
            failed.push(planCode);
            console.log(`[${i+1}/${urls.length}] ${planCode}: ${e.message?.slice(0,60)}`);
        }

        if ((i + 1) % 100 === 0) saveProgress();
    }

    await browser.close();
    saveProgress();
    console.log(`Done. ${results.length} with data, ${failed.length} failed.`);
})();
