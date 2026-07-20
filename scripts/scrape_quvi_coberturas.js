#!/usr/bin/env node
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const OUT = path.join(__dirname, '..', 'adjuntos', 'quvi_coberturas.csv');
const FAILED = path.join(__dirname, '..', 'adjuntos', 'quvi_cobertura_failed.txt');
const ISAPRES = ['banmedica', 'colmena', 'consalud', 'cruz-blanca', 'esencial', 'nueva-masvida', 'vida-tres'];

function save(data) {
    if (!data.length) return;
    const keys = Object.keys(data[0]);
    fs.writeFileSync(OUT, [keys.join(','), ...data.map(r => keys.map(k => r[k] || '').join(','))].join('\n'));
}

(async () => {
    const browser = await chromium.launch({ headless: true });
    const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    const page = await ctx.newPage();
    const results = [], failed = [];

    for (const isapre of ISAPRES) {
        console.log(`\n=== ${isapre.toUpperCase()} ===`);
        await page.goto(`https://www.quvi.cl/isapres/${isapre}`, { waitUntil: 'networkidle', timeout: 30000 });
        await page.waitForTimeout(3000);

        // Debug: dump page title and all links/buttons
        const title = await page.title();
        console.log(`  Page title: ${title}`);

        // Find plan cards — try multiple selectors
        const cards = await page.evaluate(() => {
            // Collect all plan links/cards
            const items = [];
            // Try data-plan-code
            document.querySelectorAll('[data-plan-code]').forEach(el => {
                items.push({ code: el.getAttribute('data-plan-code'), text: el.textContent?.trim().slice(0,40) });
            });
            if (items.length) return items;
            // Try links to /plan/
            document.querySelectorAll('a[href*="/plan/"]').forEach(el => {
                const href = el.getAttribute('href');
                const code = href.split('/plan/')[1]?.split('/')[0]?.split('?')[0];
                if (code && code.length > 2) items.push({ code, text: el.textContent?.trim().slice(0,40) });
            });
            return items;
        });

        console.log(`  Found ${cards.length} plan cards`);

        for (let i = 0; i < cards.length; i++) {
            const { code } = cards[i];
            try {
                // Click the card/link to navigate to plan detail
                await page.goto(`https://www.quvi.cl/plan/${code}`, { waitUntil: 'networkidle', timeout: 15000 });
                await page.waitForTimeout(1500);

                // Try to extract coverage from the plan detail page
                const data = await page.evaluate(() => {
                    // Method 1: Check for SEO data section
                    const seo = document.querySelector('.seo-plan-data');
                    // Method 2: Check for any coverage text in the full page
                    const body = document.body?.textContent || '';
                    
                    const extract = (label) => {
                        const r = new RegExp(label + '[:\s]*([^\n]{2,50})', 'i');
                        const m = body.match(r);
                        return m ? m[1].trim() : '';
                    };

                    // Method 3: Look for structured coverage elements
                    let hosp = extract('Cobertura hospitalaria');
                    let amb = extract('Cobertura ambulatoria');
                    
                    // Fallback: try extracting from badge/card elements
                    if (!hosp) {
                        const hospEl = document.querySelector('[data-criterio="hospitalaria"]');
                        if (hospEl) hosp = hospEl.textContent?.trim();
                    }

                    return {
                        nombre: document.querySelector('h1')?.textContent?.trim() || '',
                        cobertura_hospitalaria: hosp,
                        cobertura_ambulatoria: amb,
                        nota_global: extract('Nota Global'),
                        tope_anual: extract('Tope anual'),
                        prestadores: extract('prestadores'),
                        costo_uf: extract('UF'),
                        linea: extract('Línea'),
                    };
                });

                if (data.cobertura_hospitalaria || data.cobertura_ambulatoria) {
                    data.codigo = code;
                    data.isapre = isapre;
                    data.url = `https://www.quvi.cl/plan/${code}`;
                    results.push(data);
                    console.log(`  [${i+1}/${cards.length}] ${code}: H=${data.cobertura_hospitalaria} A=${data.cobertura_ambulatoria}`);
                } else {
                    failed.push(code);
                    // Debug: dump some page text
                    const sample = await page.evaluate(() => document.body?.textContent?.slice(0, 200));
                    console.log(`  [${i+1}/${cards.length}] ${code}: sin datos. Sample: ${sample?.slice(0,80)}`);
                }

            } catch (e) {
                failed.push(code);
                console.log(`  [${i+1}/${cards.length}] ${code}: error`);
            }

            if (results.length % 20 === 0) {
                save(results);
                fs.writeFileSync(FAILED, failed.join('\n'));
            }
        }
        save(results);
        fs.writeFileSync(FAILED, failed.join('\n'));
    }

    await browser.close();
    save(results);
    console.log(`\nDone. ${results.length} with data, ${failed.length} failed.`);
})();
