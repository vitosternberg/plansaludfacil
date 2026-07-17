/**
 * Scraper queplan.cl — Extrae datos de isapres abiertas
 * 
 * Datos extraídos por cada isapre:
 *  - Cantidad de planes disponibles
 *  - Tablas de precio por edad (individual)
 *  - Planes pareja / carga
 *  - Red de prestadores
 * 
 * Output: adjuntos/queplan_isapres.csv
 */

const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'https://queplan.cl';
const OUTPUT_CSV = path.join(__dirname, 'adjuntos', 'queplan_isapres_datos.csv');

// Isapres abiertas — slug según URL del sitio
const ISAPRES = [
  { name: 'Banmédica', slug: 'Banmedica' },
  { name: 'Colmena', slug: 'Colmena' },
  { name: 'Cruz Blanca', slug: 'Cruz-Blanca' },
  { name: 'Consalud', slug: 'Consalud' },
  { name: 'Nueva MasVida', slug: 'Nueva-MasVida' },
  { name: 'Vida Tres', slug: 'Vida-Tres' },
];

// ============================================================
// HELPERS
// ============================================================

/** Extrae todo el texto visible de un elemento, limpiando espacios */
function cleanText(el) {
  if (!el) return '';
  return el.replace(/\s+/g, ' ').trim();
}

/** Espera a que la página cargue (networkidle) */
async function waitStable(page, timeout = 10000) {
  try {
    await page.waitForLoadState('networkidle', { timeout });
  } catch {
    // ok
  }
}

// ============================================================
// SCRAPER PER ISAPRE
// ============================================================

async function scrapeIsapre(browser, { name, slug }) {
  console.log(`\n🔍 Scrapeando: ${name} (${slug})`);
  const context = await browser.newContext({
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
    viewport: { width: 1280, height: 900 },
  });
  const page = await context.newPage();

  const url = `${BASE_URL}/Isapre/${slug}`;
  console.log(`   Navegando a: ${url}`);
  await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
  await waitStable(page);

  const data = {
    isapre: name,
    url,
    num_planes: 'N/D',
    planes_nombres: '',
    tabla_individual: '',
    tabla_pareja: '',
    tabla_carga: '',
    prestadores: '',
    notas: '',
  };

  try {
    // ---- 1. Cantidad de planes ----
    // Buscar encabezados o secciones que mencionen planes
    const planesText = await page.evaluate(() => {
      // Buscar elementos que contengan "plan" o "planes" y tengan tarjetas/listas cercanas
      const allText = document.body.innerText;
      
      // Intentar encontrar una sección de planes con tarjetas (cards)
      const cards = document.querySelectorAll('[class*="card"], [class*="Card"], [class*="plan"], [class*="Plan"]');
      const planNames = [];
      
      // Buscar texto que indique cantidad de planes
      const match = allText.match(/(\d+)\s*(planes|plan)/i);
      let numPlanes = match ? match[1] : '';
      
      // Buscar nombres de planes en la página
      const headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6, strong, b');
      const seen = new Set();
      headings.forEach(h => {
        const t = h.textContent.trim();
        if ((t.includes('Plan') || t.includes('plan')) && t.length > 5 && t.length < 100) {
          if (!seen.has(t)) {
            planNames.push(t);
            seen.add(t);
          }
        }
      });

      // Buscar en listas (ul/ol) que contengan nombres de planes
      const lists = document.querySelectorAll('ul, ol');
      lists.forEach(list => {
        const items = list.querySelectorAll('li');
        items.forEach(item => {
          const t = item.textContent.trim();
          if ((t.includes('Plan') || t.includes('plan')) && t.length > 5 && t.length < 200) {
            if (!seen.has(t)) {
              planNames.push(t);
              seen.add(t);
            }
          }
        });
      });

      return { numPlanes: numPlanes || planNames.length.toString(), planNames: planNames.join(' | ') };
    });

    data.num_planes = planesText.numPlanes || String(planesText.planNames.split('|').length);
    data.planes_nombres = planesText.planNames;

    // ---- 2. Tablas de precios ----
    // Buscar todas las tablas en la página
    const tables = await page.evaluate(() => {
      const allTables = document.querySelectorAll('table');
      const results = [];
      
      allTables.forEach((table, idx) => {
        const headers = [];
        const rows = [];
        
        // Obtener encabezados
        const ths = table.querySelectorAll('thead th, thead td, tr:first-child th, tr:first-child td');
        ths.forEach(th => headers.push(th.textContent.trim()));
        
        // Obtener filas de datos
        const trs = table.querySelectorAll('tbody tr, tr');
        trs.forEach(tr => {
          const cells = tr.querySelectorAll('td, th');
          const row = [];
          cells.forEach(cell => row.push(cell.textContent.trim()));
          if (row.length > 0) rows.push(row);
        });

        // Filtrar filas vacías o de encabezado repetido
        const dataRows = rows.filter(r => {
          const joined = r.join(' ');
          return joined.length > 0 && !headers.every((h, i) => r[i] === h);
        });

        if (dataRows.length > 0) {
          results.push({
            tableIndex: idx,
            headers: headers.join(' | '),
            rows: dataRows.map(r => r.join(' | ')).join(' ;; '),
            rowCount: dataRows.length,
          });
        }
      });
      
      return results;
    });

    // Clasificar tablas
    const precioTablas = tables.filter(t => 
      t.headers.toLowerCase().includes('precio') || 
      t.headers.toLowerCase().includes('plan') ||
      t.headers.toLowerCase().includes('edad') ||
      t.headers.toLowerCase().includes('cotización') ||
      t.headers.toLowerCase().includes('valor') ||
      t.rows.toLowerCase().includes('$') ||
      t.rows.toLowerCase().includes('uf')
    );

    if (precioTablas.length > 0) {
      data.tabla_individual = precioTablas.map(t => 
        `[${t.headers}] ROWS: ${t.rows}`
      ).join(' ||| ');
    }

    // Buscar "pareja" o "carga" en el texto
    const pageText = await page.evaluate(() => document.body.innerText);
    
    // Extraer líneas relevantes que contengan "pareja" o "carga"
    const parejaLines = pageText.split('\n').filter(l => 
      /pareja|carga|familiar|familia/i.test(l) && 
      (/\$|uf|valor|precio|cotización/i.test(l) || l.length < 150)
    );
    data.tabla_pareja = parejaLines.slice(0, 20).join(' | ');

    // Buscar sección de carga
    const cargaSection = pageText.match(/carga[\s\S]{0,800}?(?:$|prestador|isapre|footer)/i);
    if (cargaSection) {
      data.tabla_carga = cargaSection[0].replace(/\s+/g, ' ').trim().substring(0, 1000);
    }

    // ---- 3. Prestadores ----
    const prestadoresText = await page.evaluate(() => {
      const body = document.body.innerText;
      const idx = body.search(/prestador|clínica|red\s*(de|médica)|convenio/i);
      if (idx === -1) return '';
      return body.substring(Math.max(0, idx - 50), Math.min(body.length, idx + 1500));
    });
    data.prestadores = prestadoresText.replace(/\s+/g, ' ').trim();

    // ---- 4. Texto completo visible (respaldo) ----
    // Guardar primeras 3000 chars del texto visible para referencia
    const fullText = await page.evaluate(() => document.body.innerText);
    data.notas = fullText.substring(0, 3000).replace(/\s+/g, ' ').trim();

    console.log(`   ✅ Planes: ${data.num_planes} | Tablas: ${tables.length} (precios: ${precioTablas.length}) | Prestadores: ${data.prestadores ? 'SÍ' : 'NO'}`);

  } catch (err) {
    console.error(`   ❌ Error en ${name}: ${err.message}`);
    data.notas = `ERROR: ${err.message}`;
  }

  await context.close();
  return data;
}

// ============================================================
// MAIN
// ============================================================

async function main() {
  console.log('🚀 Iniciando scraper queplan.cl...\n');
  console.log(`   Isapres a scrapear: ${ISAPRES.map(i => i.name).join(', ')}`);

  const browser = await chromium.launch({
    headless: true,
    executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
    args: [
      '--disable-gpu',
      '--no-sandbox',
      '--disable-dev-shm-usage',
      '--disable-software-rasterizer',
    ],
  });
  
  try {
    const results = [];
    
    for (const isapre of ISAPRES) {
      const data = await scrapeIsapre(browser, isapre);
      results.push(data);
      // Pequeña pausa entre isapres para no saturar
      await new Promise(r => setTimeout(r, 1500));
    }

    // ---- Guardar CSV ----
    const headers = [
      'isapre', 'url', 'num_planes', 'planes_nombres',
      'tabla_individual', 'tabla_pareja', 'tabla_carga',
      'prestadores', 'notas'
    ];
    
    const csvRows = [headers.join(',')];
    
    for (const row of results) {
      const values = headers.map(h => {
        const val = (row[h] || '').replace(/"/g, '""');
        return `"${val}"`;
      });
      csvRows.push(values.join(','));
    }

    const csvContent = csvRows.join('\n');
    fs.writeFileSync(OUTPUT_CSV, csvContent, 'utf-8');
    
    console.log(`\n\n📄 CSV guardado en: ${OUTPUT_CSV}`);
    console.log(`   Filas: ${results.length} isapres`);
    console.log('\n--- RESUMEN ---');
    
    for (const r of results) {
      console.log(`\n🏥 ${r.isapre}`);
      console.log(`   Planes: ${r.num_planes}`);
      console.log(`   Nombres: ${r.planes_nombres.substring(0, 150)}`);
      console.log(`   Prestadores: ${r.prestadores ? r.prestadores.substring(0, 200) + '...' : 'NO ENCONTRADO'}`);
    }

  } finally {
    await browser.close();
    console.log('\n✅ Browser cerrado. Scraping completado.');
  }
}

main().catch(err => {
  console.error('❌ Error fatal:', err);
  process.exit(1);
});
