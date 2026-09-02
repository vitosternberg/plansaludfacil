/**
 * Cloudflare Worker — Proxy de datos de planes hacia el proveedor.
 * =================================================================
 * Resuelve dos cosas:
 *  1. Evita el bloqueo server-to-server: el cliente llama a este Worker
 *     (borde de Cloudflare, siempre alcanzable) en vez de apuntar directo
 *     al origen plansaludfacil.cl.
 *  2. Oculta la API key del proveedor: el Worker inyecta la key desde un
 *     secret (env.PLANES_API_KEY), así el cliente no la necesita.
 *
 * Uso del cliente:  https://<tu-worker>/...?action=ping&...
 * El Worker siempre reenvía a  {ORIGIN}/api/planes.php  pasando los
 * query params recibidos (action, filtros, etc.) y agregando la key real.
 *
 * Desplegar:
 *   wrangler secret put PLANES_API_KEY
 *   wrangler deploy
 * Configurar ORIGIN en wrangler.toml (o como variable de entorno).
 */

const ORIGIN = 'https://plansaludfacil.cl';
const ORIGIN_PATH = '/api/planes.php';

function cors() {
  return {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
    'Access-Control-Allow-Headers': 'Content-Type, X-Api-Key',
  };
}

export default {
  async fetch(request, env, ctx) {
    // 1. CORS preflight
    if (request.method === 'OPTIONS') {
      return new Response(null, { status: 204, headers: cors() });
    }

    // 2. Construir la URL al origen, inyectando la key del proveedor
    const url = new URL(request.url);
    const origin = (env.ORIGIN || ORIGIN).replace(/\/+$/, '');
    const originUrl = new URL(ORIGIN_PATH, origin);

    for (const [k, v] of url.searchParams.entries()) {
      if (k !== 'key') originUrl.searchParams.set(k, v); // ignorar key del cliente
    }
    const providerKey = env.PLANES_API_KEY || '';
    if (providerKey) originUrl.searchParams.set('key', providerKey);

    // 3. Forward (método + body, para soportar POST de cotizar)
    const init = {
      method: request.method,
      headers: { Accept: 'application/json' },
    };
    if (request.method === 'POST') {
      init.headers['Content-Type'] =
        request.headers.get('Content-Type') || 'application/x-www-form-urlencoded';
      init.body = await request.arrayBuffer();
    }

    const cacheKey = new Request(originUrl.toString(), { method: 'GET' });

    // 4. Cache en el borde para GET (alivia el origen)
    if (request.method === 'GET') {
      const hit = await caches.default.match(cacheKey);
      if (hit) return hit;
    }

    const resp = await fetch(originUrl.toString(), init);

    const out = new Response(resp.body, {
      status: resp.status,
      headers: {
        ...cors(),
        'Content-Type': resp.headers.get('Content-Type') || 'application/json',
        'Cache-Control': resp.status === 200 ? 'public, max-age=60' : 'no-store',
      },
    });

    if (request.method === 'GET' && resp.status === 200) {
      ctx.waitUntil(caches.default.put(cacheKey, out.clone()));
    }

    return out;
  },
};
