// Runner pengganti Newman (hanya untuk verifikasi collection selama Newman tidak bisa dipasang).
// Mendukung subset API pm yang dipakai collection DTY-QCT.
// Pemakaian: node mini-runner.cjs <collection.json> <environment.json> <hasil.json>
const fs = require('fs');
const http = require('http');
const { URL } = require('url');

const col = JSON.parse(fs.readFileSync(process.argv[2], 'utf8'));
const env = JSON.parse(fs.readFileSync(process.argv[3], 'utf8'));
const fileHasil = process.argv[4];

const envVars = Object.fromEntries(env.values.map(v => [v.key, v.value]));
const colVars = Object.fromEntries((col.variable || []).map(v => [v.key, v.value]));
const jar = {};

const isi = s => String(s).replace(/{{(\w+)}}/g, (_, k) => (k in colVars ? colVars[k] : k in envVars ? envVars[k] : `{{${k}}}`));

function kirim(method, url, body, headers) {
  return new Promise((resolve, reject) => {
    const u = new URL(url);
    const cookie = Object.entries(jar).map(([k, v]) => `${k}=${v}`).join('; ');
    const h = Object.assign({}, headers, cookie ? { Cookie: cookie } : {});
    if (body !== null) {
      h['Content-Type'] = 'application/x-www-form-urlencoded';
      h['Content-Length'] = Buffer.byteLength(body);
    }
    const req = http.request({ method, hostname: u.hostname, port: u.port, path: u.pathname + u.search, headers: h }, res => {
      for (const c of res.headers['set-cookie'] || []) {
        const [kv] = c.split(';');
        const i = kv.indexOf('=');
        jar[kv.slice(0, i)] = kv.slice(i + 1);
      }
      let data = '';
      res.setEncoding('utf8');
      res.on('data', d => (data += d));
      res.on('end', () => resolve({ code: res.statusCode, headers: res.headers, body: data }));
    });
    req.on('error', reject);
    if (body !== null) req.write(body);
    req.end();
  });
}

function expect(nilai) {
  const gagal = m => { throw new Error(m); };
  const api = {
    to: {
      include: x => { if (!String(nilai).includes(x)) gagal(`tidak berisi "${x}"`); },
      match: re => { if (!re.test(String(nilai))) gagal(`"${nilai}" tidak cocok ${re}`); },
      equal: x => { if (nilai !== x) gagal(`${nilai} != ${x}`); },
      be: { null: undefined },
      not: { be: {}, match: re => { if (re.test(String(nilai))) gagal(`"${nilai}" seharusnya tidak cocok ${re}`); } },
    },
  };
  Object.defineProperty(api.to.be, 'null', { get() { if (nilai !== null) gagal('bukan null'); } });
  Object.defineProperty(api.to.not.be, 'null', { get() { if (nilai === null) gagal('bernilai null'); } });
  return api;
}

async function jalankan(item, jalur, hasil) {
  if (item.item) {
    for (const anak of item.item) await jalankan(anak, [...jalur, item.name], hasil);
    return;
  }
  const r = item.request;
  const follow = !(item.protocolProfileBehavior && item.protocolProfileBehavior.followRedirects === false);
  let method = r.method;
  let url = isi(r.url.raw);
  let body = null;
  if (r.body && r.body.urlencoded) {
    body = r.body.urlencoded.map(p => `${encodeURIComponent(p.key)}=${encodeURIComponent(isi(p.value))}`).join('&');
  }
  const headers = Object.fromEntries((r.header || []).map(x => [x.key, x.value]));
  let res = await kirim(method, url, body, headers);
  for (let i = 0; follow && [301, 302, 303, 307, 308].includes(res.code) && i < 10; i++) {
    url = new URL(res.headers.location, url).toString();
    if (![307, 308].includes(res.code)) { method = 'GET'; body = null; }
    res = await kirim(method, url, body, headers);
  }

  const tes = [];
  const pm = {
    test: (nama, fn) => { try { fn(); tes.push([nama, true]); } catch (e) { tes.push([nama, false, e.message]); } },
    expect,
    response: {
      text: () => res.body,
      code: res.code,
      headers: { get: k => res.headers[k.toLowerCase()] || null },
      to: { have: { status: k => { if (res.code !== k) throw new Error(`status ${res.code}, harap ${k}`); } } },
    },
    collectionVariables: { set: (k, v) => (colVars[k] = v), get: k => colVars[k] },
    environment: { get: k => envVars[k] },
  };
  const skrip = [...(item.event || []), ...(col.event || [])].filter(e => e.listen === 'test').map(e => e.script.exec.join('\n'));
  for (const s of skrip) {
    try { new Function('pm', s)(pm); } catch (e) { tes.push(['(script error)', false, e.message]); }
  }
  const spoof = r.body && r.body.urlencoded ? r.body.urlencoded.find(p => p.key === '_method') : null;
  hasil.push({ jalur: [...jalur, item.name].join(' / '), method: spoof ? spoof.value : r.method, url: r.url.raw, code: res.code, tes });
}

(async () => {
  const hasil = [];
  for (const folder of col.item) {
    await jalankan(folder, [], hasil);
  }
  let lulus = 0, gagal = 0;
  for (const h of hasil) {
    for (const [nama, ok, pesan] of h.tes) {
      ok ? lulus++ : gagal++;
      if (!ok) console.log(`GAGAL  ${h.jalur}\n       ${nama}: ${pesan} (HTTP ${h.code})`);
    }
  }
  console.log(`\nRequest: ${hasil.length} | Assertion lulus: ${lulus} | gagal: ${gagal}`);
  fs.writeFileSync(fileHasil, JSON.stringify({ waktu: new Date().toISOString(), runner: 'mini-runner.cjs (pengganti Newman)', request: hasil.length, lulus, gagal, hasil }, null, 2));
  console.log(`Hasil disimpan di ${fileHasil}`);
})();
