#!/usr/bin/env node
/*
 * Screenshot de página inteira (desktop + mobile) para revisar o layout.
 * Uso: node screenshot.js <index.html | URL> [pasta-saida] [--widths 1440,375]
 *
 * Sem dependências: Node 22+ (WebSocket global) + Chrome ou Edge instalado.
 * Emula prefers-reduced-motion: reduce, então elementos com "reveal" precisam
 * aparecer sem animação (isso também testa a regra de movimento reduzido).
 * Saída: <pasta-saida>/shot-<largura>-NN.png, página fatiada em partes de ~2 telas
 * (padrão: _shots/ ao lado do HTML). A parte 01 começa na primeira dobra.
 */
const fs = require('fs');
const os = require('os');
const path = require('path');
const { spawn } = require('child_process');
const { pathToFileURL } = require('url');

const args = process.argv.slice(2);
const target = args.find((a) => !a.startsWith('--'));
if (!target) { console.error('Uso: node screenshot.js <index.html | URL> [pasta-saida] [--widths 1440,375]'); process.exit(1); }
const wi = args.indexOf('--widths');
const widths = (wi > -1 ? args[wi + 1] : '1440,375').split(',').map(Number);
const isUrl = /^https?:\/\//.test(target);
const url = isUrl ? target : pathToFileURL(path.resolve(target)).href;
const positional = args.filter((a, i) => !a.startsWith('--') && args[i - 1] !== '--widths');
const outDir = positional[1] || (isUrl ? path.resolve('_shots') : path.join(path.dirname(path.resolve(target)), '_shots'));

function findBrowser() {
  const c = process.platform === 'win32'
    ? ['C:/Program Files/Google/Chrome/Application/chrome.exe', 'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
       'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe', 'C:/Program Files/Microsoft/Edge/Application/msedge.exe']
    : process.platform === 'darwin'
      ? ['/Applications/Google Chrome.app/Contents/MacOS/Google Chrome', '/Applications/Microsoft Edge.app/Contents/MacOS/Microsoft Edge']
      : ['/usr/bin/google-chrome', '/usr/bin/chromium', '/usr/bin/chromium-browser', '/usr/bin/microsoft-edge'];
  return process.env.CHROME_PATH || c.find((p) => fs.existsSync(p));
}

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function main() {
  const bin = findBrowser();
  if (!bin) throw new Error('Chrome/Edge não encontrado (defina CHROME_PATH).');
  if (typeof WebSocket === 'undefined') throw new Error('Precisa de Node 22+ (WebSocket global).');
  fs.mkdirSync(outDir, { recursive: true });
  const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'shot-'));
  const proc = spawn(bin, ['--headless=new', '--remote-debugging-port=0', `--user-data-dir=${profile}`,
    '--hide-scrollbars', '--no-first-run', '--no-default-browser-check', 'about:blank'], { stdio: 'ignore' });

  try {
    const portFile = path.join(profile, 'DevToolsActivePort');
    for (let i = 0; i < 100 && !fs.existsSync(portFile); i++) await sleep(100);
    const port = fs.readFileSync(portFile, 'utf8').split('\n')[0].trim();
    const targets = await (await fetch(`http://127.0.0.1:${port}/json`)).json();
    const page = targets.find((t) => t.type === 'page');
    const ws = new WebSocket(page.webSocketDebuggerUrl);
    await new Promise((r, j) => { ws.onopen = r; ws.onerror = j; });
    let id = 0; const pending = new Map(); const waiters = [];
    ws.onmessage = (e) => {
      const m = JSON.parse(e.data);
      if (m.id && pending.has(m.id)) { const p = pending.get(m.id); pending.delete(m.id); m.error ? p.j(new Error(m.error.message)) : p.r(m.result); }
      else if (m.method) waiters.filter((w) => w.method === m.method).forEach((w) => w.r(m.params));
    };
    const send = (method, params = {}) => new Promise((r, j) => { const i = ++id; pending.set(i, { r, j }); ws.send(JSON.stringify({ id: i, method, params })); });
    const once = (method, ms = 15000) => new Promise((r) => { waiters.push({ method, r }); setTimeout(r, ms); });

    await send('Page.enable');
    await send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
    for (const w of widths) {
      const mobile = w < 768;
      await send('Emulation.setDeviceMetricsOverride', { width: w, height: mobile ? 812 : 900, deviceScaleFactor: 1, mobile });
      const loaded = once('Page.loadEventFired');
      await send('Page.navigate', { url });
      await loaded;
      await send('Runtime.evaluate', { expression: 'document.fonts.ready', awaitPromise: true });
      await sleep(600);
      const { result } = await send('Runtime.evaluate', { expression: 'Math.max(document.documentElement.scrollHeight, document.body.scrollHeight)', returnByValue: true });
      const h = Math.min(result.value, 16000);
      const overflow = (await send('Runtime.evaluate', { expression: 'document.documentElement.scrollWidth > innerWidth', returnByValue: true })).result.value;
      // Fatias de ~2 telas: imagens muito altas perdem detalhe quando lidas para revisão.
      const slice = mobile ? 1600 : 1800;
      const files = [];
      for (let y = 0, n = 1; y < h; y += slice, n++) {
        const shot = await send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true, clip: { x: 0, y, width: w, height: Math.min(slice, h - y), scale: 1 } });
        const file = path.join(outDir, `shot-${w}-${String(n).padStart(2, '0')}.png`);
        fs.writeFileSync(file, Buffer.from(shot.data, 'base64'));
        files.push(path.basename(file));
      }
      console.log(`${w}px: altura ${h}px, ${files.length} parte(s) em ${outDir}: ${files.join(', ')}${overflow ? '\n  ATENÇÃO: rolagem horizontal nesta largura' : ''}`);
    }
    ws.close();
  } finally {
    proc.kill();
    await sleep(300);
    try { fs.rmSync(profile, { recursive: true, force: true }); } catch (e) { /* perfil ainda em uso */ }
  }
}

main().catch((e) => { console.error('Erro:', e.message); process.exit(1); });
