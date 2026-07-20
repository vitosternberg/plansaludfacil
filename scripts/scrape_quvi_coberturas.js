#!/usr/bin/env node
/**
 * Scrape coberturas reales de quvi.cl — vía modal #planDetailPanel.
 * Estrategia: visitar 7 páginas isapre, click "Ver Plan", extraer modal.
 * Mucho más rápido que 2,231 visitas individuales.
 */
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const OUT = path.join(__dirname, '..', 'adjuntos', 'quvi_coberturas.csv');
const FAIL = path.join(__dirname, '..', 'adjuntos', 'quvi_cobertura_failed.txt');

const ISAPRES = [
    { slug: 'banmedica', name: 'Banmédica' },
    { slug: 'colmena', name: 'Colmena' },
    { slug: 'consalud', name: 'Consalud' },
    { slug: 'cruz-blanca', name: 'Cruz Blanca' },
    { slug: 'esencial', name: 'Esencial' },
    { slug: 'nueva-masvida', name: 'Nueva Masvida' },
    { slug: 'vida-tres', name: 'Vida Tres' },
];

const results = [];
const failed = [];

function save() {
    if (!results.length) return;
    const keys = Object.keys(results[0]);
    fs.writeFileSync(OUT, [keys.join(','), ...results.map(r => keys.map(k => r[k]||'').join(','))].join('\n'));
    fs.writeFileSync(FAIL, failed.join('\n'));
}

(async () => {
    const browser = await chromium.launch({ headless: true });
    const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    const page = await ctx.newPage();

    for (const { slug, name } of ISAPRES) {
        console.log(`\n=== ${name.toUpperCase()} ===`);
        const url = `https://www.quvi.cl/isapres/${slug}`;
        await page.goto(url, { waitUntil: 'networkidle', timeout: 30000 });
        await page.waitForTimeout(3000);

        // Find all "Ver Plan" buttons/links
        const planLinks = await page.evaluate(() => {
            const links = [];
            document.querySelectorAll('a').forEach(a => {
                const h = a.getAttribute('href') || '';
                const t = a.textContent?.trim() || '';
                if (h.includes('/plan/') && (t === 'Ver Plan' || t === 'Explorar plan' || t === 'Ver plan')) {
                    const code = h.split('/plan/')[1]?.split('?')[0];
                    if (code) links.push({ code, href: h });
                }
            });
            // Also try buttons
            document.querySelectorAll('button').forEach(b => {
                const t = b.textContent?.trim() || '';
                if (t === 'Ver Plan' || t === 'Explorar plan') {
                    const card = b.closest('[class*="plan"], [class*="card"]');
                    const a = card?.querySelector('a[href*="/plan/"]');
                    const h = a?.getAttribute('href') || '';
                    const code = h.split('/plan/')[1]?.split('?')[0];
                    if (code) links.push({ code, href: h });
                }
            });
            return links;
        });

        console.log(`  Found ${planLinks.length} plans`);

        for (let i = 0; i < planLinks.length; i++) {
            const { code } = planLinks[i];
            try {
                // Click the link/button to open modal
                const clicked = await page.evaluate((c) => {
                    // Try link first
                    const link = document.querySelector(`a[href*="/plan/${c}"]`);
                    if (link) { link.click(); return 'link'; }
                    // Try button
                    const btn = document.querySelector(`button:has-text("Ver Plan")`);
                    if (btn) { btn.click(); return 'button'; }
                    return null;
                }, code);

                if (!clicked) {
                    failed.push(code);
                    console.log(`  [${i+1}/${planLinks.length}] ${code}: no clickable`);
                    continue;
                }

                // Wait for modal
                await page.waitForTimeout(1500);

                // Try to extract from modal
                const data = await page.evaluate(() => {
                    const panel = document.getElementById('planDetailPanel');
                    if (!panel || panel.offsetParent === null) return null;
                    
                    const t = panel.textContent || '';
                    const result = {};

                    const extract = (label) => {
                        const re = new RegExp(label + '[:\\s]*([^\\n]{2,60})', 'i');
                        const m = t.match(re);
                        return m ? m[1].trim() : '';
                    };

                    result.nombre = extract('Con Prestadores') || extract('Plan');
                    result.isapre = extract('Isapre');
                    result.linea = extract('Línea');
                    result.cobertura_hospitalaria = extract('Cobertura hospitalaria');
                    result.cobertura_ambulatoria = extract('Cobertura ambulatoria');
                    result.tope_anual = extract('Tope anual');
                    result.nota_global = extract('Nota Global');
                    
                    const prest = t.match(/(\\d+)\\s*prestadores/);
                    if (prest) result.prestadores = prest[1];
                    
                    const costo = t.match(/(\\d+[,.]\\d{2})\\s*UF\\s*[·]\\s*\\$?([\\d.]+)/);
                    if (costo) { result.costo_uf = costo[1]; result.costo_clp = costo[2]; }

                    return result;
                });

                if (data && (data.cobertura_hospitalaria || data.cobertura_ambulatoria)) {
                    data.codigo = code;
                    data.url = `https://www.quvi.cl/plan/${code}`;
                    results.push(data);
                    console.log(`  [${i+1}/${planLinks.length}] ${code}: H=${data.cobertura_hospitalaria?.slice(0,10)} A=${data.cobertura_ambulatoria?.slice(0,10)}`);
                } else {
                    failed.push(code);
                    console.log(`  [${i+1}/${planLinks.length}] ${code}: modal sin datos`);
                }

                // Close modal: press Escape or click outside
                await page.keyboard.press('Escape');
                await page.waitForTimeout(500);

            } catch (e) {
                failed.push(code);
                console.log(`  [${i+1}/${planLinks.length}] ${code}: ${e.message?.slice(0,40)}`);
            }

            if (results.length % 30 === 0) save();
        }
        save();
    }

    await browser.close();
    save();
    console.log(`\nDone. ${results.length} with data, ${failed.length} failed.`);
})();
