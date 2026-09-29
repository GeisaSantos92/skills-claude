#!/usr/bin/env node
/*
 * Inventaria uma pasta de materiais de cliente para o briefing de layout.
 * Uso: node scan_folder.js <pasta> [--max-text 3000]
 *
 * Sem dependências. Saída em Markdown: arquivos por categoria, dimensões de
 * PNG/JPG/GIF/WEBP/SVG, cores hex de SVGs, texto de .docx/.txt/.md.
 * Não altera nada na pasta. Cores de PNG/JPG: olhe a imagem com a ferramenta Read.
 */
const fs = require('fs');
const path = require('path');
const { execFileSync } = require('child_process');

const IMG = new Set(['.png', '.jpg', '.jpeg', '.webp', '.gif', '.svg', '.avif', '.bmp', '.tif', '.tiff', '.ico']);
const FONT = new Set(['.ttf', '.otf', '.woff', '.woff2']);
const VIDEO = new Set(['.mp4', '.mov', '.webm', '.m4v', '.avi']);
const TEXT = new Set(['.txt', '.md']);
const DOCLIKE = new Set(['.docx', '.doc', '.odt', '.rtf']);
const SHEET = new Set(['.xlsx', '.xls', '.csv']);
const DESIGN = new Set(['.fig', '.psd', '.ai', '.xd', '.sketch', '.indd', '.eps']);
const SKIP_DIRS = new Set(['node_modules', '.git', '_layout', '__MACOSX']);

const LOGO = ['logo', 'marca', 'brand', 'logotipo', 'simbolo', 'symbol'];
const FAV = ['favicon', 'icon', 'icone', 'ico-'];
const COPY = ['texto', 'copy', 'conteudo', 'conteúdo', 'briefing', 'sobre', 'servico', 'serviço'];
const MANUAL = ['manual', 'brandbook', 'identidade', 'guideline'];
const has = (s, list) => list.some((h) => s.includes(h));

function imageSize(file, ext) {
  try {
    const fd = fs.openSync(file, 'r');
    const buf = Buffer.alloc(65536);
    const n = fs.readSync(fd, buf, 0, buf.length, 0);
    fs.closeSync(fd);
    const b = buf.subarray(0, n);
    if (ext === '.png' && b.length >= 24 && b.toString('latin1', 1, 4) === 'PNG') {
      return [b.readUInt32BE(16), b.readUInt32BE(20)];
    }
    if (ext === '.gif' && b.length >= 10) return [b.readUInt16LE(6), b.readUInt16LE(8)];
    if (ext === '.jpg' || ext === '.jpeg') {
      let i = 2;
      while (i + 9 < b.length) {
        if (b[i] !== 0xff) { i++; continue; }
        const m = b[i + 1];
        if (m >= 0xc0 && m <= 0xcf && ![0xc4, 0xc8, 0xcc].includes(m)) {
          return [b.readUInt16BE(i + 7), b.readUInt16BE(i + 5)];
        }
        i += 2 + b.readUInt16BE(i + 2);
      }
    }
    if (ext === '.webp' && b.toString('latin1', 8, 12) === 'WEBP') {
      const t = b.toString('latin1', 12, 16);
      if (t === 'VP8X') return [1 + b.readUIntLE(24, 3), 1 + b.readUIntLE(27, 3)];
      if (t === 'VP8 ') return [b.readUInt16LE(26) & 0x3fff, b.readUInt16LE(28) & 0x3fff];
      if (t === 'VP8L') {
        const v = b.readUInt32LE(21);
        return [(v & 0x3fff) + 1, ((v >> 14) & 0x3fff) + 1];
      }
    }
    if (ext === '.svg') {
      const m = b.toString('utf8').match(/viewBox="\s*[\d.\-]+[ ,]+[\d.\-]+[ ,]+([\d.]+)[ ,]+([\d.]+)/);
      if (m) return [Math.round(+m[1]), Math.round(+m[2])];
    }
  } catch (e) { /* ignora */ }
  return null;
}

function svgColors(file) {
  try {
    const s = fs.readFileSync(file, 'utf8');
    const found = s.match(/#[0-9a-fA-F]{6}\b|#[0-9a-fA-F]{3}\b/g) || [];
    return [...new Set(found.map((c) => c.toLowerCase()))].slice(0, 8);
  } catch (e) { return []; }
}

function docxText(file) {
  // .docx é um zip. Tenta bsdtar (Windows: System32\tar.exe), tar e unzip; usa cwd
  // + nome relativo porque o tar do Git Bash lê "C:" como host remoto.
  const opts = { encoding: 'utf8', maxBuffer: 50 * 1024 * 1024, cwd: path.dirname(file), stdio: ['ignore', 'pipe', 'ignore'] };
  const name = path.basename(file);
  const attempts = [];
  if (process.platform === 'win32') attempts.push(['C:\\Windows\\System32\\tar.exe', ['-xOf', name, 'word/document.xml']]);
  attempts.push(['tar', ['-xOf', name, 'word/document.xml']], ['unzip', ['-p', name, 'word/document.xml']]);
  let xml = null;
  for (const [cmd, a] of attempts) {
    try { xml = execFileSync(cmd, a, opts); if (xml && xml.includes('<w:')) break; xml = null; } catch (e) { xml = null; }
  }
  if (!xml) return '(não consegui ler o .docx; abra manualmente ou converta para .txt)';
  return xml.replace(/<\/w:p>/g, '\n').replace(/<[^>]+>/g, '')
    .replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"')
    .replace(/\n{3,}/g, '\n\n').trim();
}

function categorize(rel, ext, dims) {
  const low = rel.toLowerCase();
  if (FONT.has(ext)) return 'Fonte';
  if (VIDEO.has(ext)) return 'Vídeo';
  if (TEXT.has(ext) || DOCLIKE.has(ext)) return has(low, MANUAL) ? 'Manual de marca' : 'Texto/copy';
  if (ext === '.pdf') {
    if (has(low, MANUAL)) return 'Manual de marca';
    return has(low, COPY) ? 'Texto/copy (PDF)' : 'PDF (verificar)';
  }
  if (SHEET.has(ext)) return 'Planilha/dados';
  if (DESIGN.has(ext)) return 'Arquivo de design';
  if (ext === '.ico' || has(low, FAV)) return 'Favicon/ícone';
  if (IMG.has(ext)) {
    if (has(low, LOGO)) return 'Logo';
    if (dims && dims[0] === dims[1] && dims[0] <= 512) return 'Possível ícone (quadrado pequeno)';
    return 'Imagem/foto';
  }
  return 'Outro';
}

function walk(dir, out) {
  for (const ent of fs.readdirSync(dir, { withFileTypes: true }).sort((a, b) => a.name.localeCompare(b.name))) {
    if (ent.name.startsWith('.') || ent.name.startsWith('~$') || /^(thumbs\.db|desktop\.ini)$/i.test(ent.name)) continue;
    const full = path.join(dir, ent.name);
    if (ent.isDirectory()) { if (!SKIP_DIRS.has(ent.name)) walk(full, out); }
    else out.push(full);
  }
  return out;
}

function main() {
  const args = process.argv.slice(2);
  const root = args.find((a) => !a.startsWith('--'));
  if (!root) { console.log('Uso: node scan_folder.js <pasta> [--max-text 3000]'); process.exit(1); }
  const mi = args.indexOf('--max-text');
  const maxText = mi >= 0 ? parseInt(args[mi + 1], 10) : 3000;
  if (!fs.existsSync(root) || !fs.statSync(root).isDirectory()) { console.log('Pasta não encontrada: ' + root); process.exit(1); }

  const cats = {};
  const files = walk(root, []);
  for (const full of files) {
    const rel = path.relative(root, full);
    const ext = path.extname(full).toLowerCase();
    const size = fs.statSync(full).size;
    const dims = IMG.has(ext) ? imageSize(full, ext) : null;
    const cat = categorize(rel, ext, dims);
    (cats[cat] = cats[cat] || []).push({ rel, full, ext, size, dims });
  }

  console.log('# Inventário: ' + root + '\n');
  console.log('Total de arquivos: ' + files.length + '\n');
  for (const cat of Object.keys(cats).sort()) {
    console.log('## ' + cat + ' (' + cats[cat].length + ')');
    for (const f of cats[cat]) {
      const d = f.dims ? ' ' + f.dims[0] + 'x' + f.dims[1] : '';
      console.log('- `' + f.rel + '` (' + f.ext.slice(1) + ', ' + Math.max(1, Math.round(f.size / 1024)) + ' KB' + d + ')');
      if (f.ext === '.svg' && ['Logo', 'Favicon/ícone', 'Possível ícone (quadrado pequeno)'].includes(cat)) {
        const c = svgColors(f.full);
        if (c.length) console.log('  - cores no SVG: ' + c.join(', '));
      }
      if (cat === 'Imagem/foto' && f.dims && ['.jpg', '.jpeg', '.png', '.webp'].includes(f.ext) && f.dims[0] < 1000) {
        console.log('  - atenção: largura < 1000 px, pode não servir para hero/tela cheia');
      }
      if (FONT.has(f.ext)) console.log('  - fonte: ' + path.basename(f.rel, f.ext).replace(/[-_]/g, ' '));
    }
    console.log();
  }

  for (const cat of ['Texto/copy', 'Manual de marca']) {
    for (const f of cats[cat] || []) {
      let txt;
      if (f.ext === '.docx') txt = docxText(f.full);
      else if (TEXT.has(f.ext)) txt = fs.readFileSync(f.full, 'utf8').replace(/^﻿/, '');
      else continue;
      console.log('### Conteúdo de `' + f.rel + '`\n');
      console.log(txt.slice(0, maxText) + (txt.length > maxText ? '\n... [truncado]' : ''));
      console.log();
    }
  }

  const missing = ['Logo', 'Favicon/ícone', 'Fonte', 'Texto/copy', 'Manual de marca', 'Imagem/foto'].filter((c) => !cats[c]);
  if (missing.length) {
    console.log('## Não encontrado na pasta');
    missing.forEach((c) => console.log('- ' + c));
    console.log();
  }
  console.log('_Cores de PNG/JPG e conteúdo de PDFs/imagens: use a ferramenta Read (PDF com o parâmetro pages)._');
}

main();
