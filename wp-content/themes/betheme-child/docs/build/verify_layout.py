#!/usr/bin/env python3
"""
Check a deployed copy of the site against the numbers the design was built to.

    ./verify_layout.py                       # against https://miraex.com
    ./verify_layout.py https://staging.host  # against anywhere else

Everything here was a real bug at some point, which is why it is measured rather than
eyeballed: a 14px gap that rendered as 0, a phone gutter of 53px, a footer that wrapped
below 1240px, a content edge that disagreed with itself by 12px. Screenshots hid all of
them. Needs headless Chrome; nothing else.
"""

import json, os, re, subprocess, sys, tempfile, urllib.request, ssl, html

BASE = (sys.argv[1] if len(sys.argv) > 1 else 'https://miraex.com').rstrip('/')

PAGES = ['', 'technology/', 'distributed-quantum-computing/', 'quantum-sensing/',
         'quantum-networking/', 'about/', 'news/', 'news/sealsq-acquires-miraex/',
         'news/q-modus-snsf-bridge/', 'news/venture-leaders-technology/',
         'resources/', 'careers/', 'contact/', 'privacy/', 'terms-of-service/']

CHROME_PATHS = [
    '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
    '/usr/bin/google-chrome', '/usr/bin/chromium', '/usr/bin/chromium-browser',
    '/Applications/Chromium.app/Contents/MacOS/Chromium',
]

CTX = ssl.create_default_context()
CTX.check_hostname = False
CTX.verify_mode = ssl.CERT_NONE

ok_count = fail_count = 0


def chrome():
    for p in CHROME_PATHS:
        if os.path.exists(p):
            return p
    sys.exit('No headless Chrome found. Set one of: ' + ', '.join(CHROME_PATHS))


def report(label, value, expected, passed):
    global ok_count, fail_count
    mark = 'ok  ' if passed else 'FAIL'
    if passed:
        ok_count += 1
    else:
        fail_count += 1
    print(f'  [{mark}] {label:<44} {value}' + ('' if passed else f'   expected {expected}'))


def fetch(url):
    return urllib.request.urlopen(url, context=CTX, timeout=30).read().decode('utf-8', 'replace')


def probe(path, script, width=1440, height=900):
    """Render a page with `script` injected and return whatever it puts in #PROBE."""
    page = fetch(f'{BASE}/{path}')
    page = page.replace('<head>', f'<head><base href="{BASE}/">', 1)
    body = f'<script>window.addEventListener("load",function(){{setTimeout(function(){{{script}}},2200);}});</script>'

    with tempfile.TemporaryDirectory() as tmp:
        f = os.path.join(tmp, 'p.html')
        open(f, 'w', encoding='utf-8').write(page.replace('</body>', body + '</body>'))
        out = subprocess.run([chrome(), '--headless', '--disable-gpu', '--ignore-certificate-errors',
                              f'--window-size={width},{height}', '--virtual-time-budget=12000',
                              '--dump-dom', 'file://' + f], capture_output=True, text=True).stdout

    m = re.search(r'<pre id="PROBE">(.*?)</pre>', out, re.S)
    return json.loads(html.unescape(m.group(1))) if m else None


def emit(expr):
    return ('var p=document.createElement("pre");p.id="PROBE";'
            f'p.textContent=JSON.stringify({expr});document.documentElement.appendChild(p);')


print(f'\nChecking {BASE}\n')

# ---------------------------------------------------------------- pages ----
print('Pages respond')
for path in PAGES:
    try:
        code = urllib.request.urlopen(f'{BASE}/{path}', context=CTX, timeout=30).getcode()
    except Exception as exc:                                    # noqa: BLE001
        code = getattr(exc, 'code', str(exc))
    report('/' + path, code, 200, code == 200)

# ------------------------------------------------------------- buttons ----
print('\nButton rows are 14px apart (.btn-row gap)')
BTN = emit('''(function(){var rows={},out=[];
document.querySelectorAll(".mcb-column .button").forEach(function(b){
  var r=b.getBoundingClientRect(); if(!r.width) return;
  (rows[Math.round(r.top)]=rows[Math.round(r.top)]||[]).push(r); });
Object.keys(rows).forEach(function(k){ var r=rows[k].sort(function(a,b){return a.left-b.left;});
  for(var i=1;i<r.length;i++) out.push(+(r[i].left-r[i-1].right).toFixed(1)); });
return out;})()''')
for path in ['', 'technology/', 'about/', 'careers/']:
    gaps = probe(path, BTN) or []
    bad = [g for g in gaps if abs(g - 14) > 0.6]
    report('/' + path, f'{len(gaps)} rows, off: {bad or "none"}', 'all 14px', not bad)

# -------------------------------------------------------- phone gutter ----
print('\nPhone side gutter is 15px')
GUT = emit('''(function(){var W=document.documentElement.clientWidth,h=document.querySelector("h1");
return h?Math.round(h.getBoundingClientRect().left):-1;})()''')
for path in ['', 'technology/', 'contact/']:
    left = probe(path, GUT, width=390, height=844)
    report('/' + path, f'{left}px', '15px', left == 15)

# ------------------------------------------------------ footer one row ----
print('\nFooter stays on one row')
FTR = emit('''(function(){var f=document.querySelector(".mfn-footer-tmpl, #Footer");
var w=[].slice.call(f.querySelectorAll(".mcb-wrap")).filter(function(x){return x.getBoundingClientRect().width>0;}).slice(0,4);
var tops={}; w.forEach(function(x){tops[Math.round(x.getBoundingClientRect().top)]=1;});
return Object.keys(tops).length;})()''')
for width in [1440, 1280, 1100, 960]:
    rows = probe('', FTR, width=width, height=800)
    report(f'{width}px wide', f'{rows} row(s)', '1', rows == 1)

# ------------------------------------------------------------- seo tags ----
print('\nSEO tags present')
for path in ['', 'technology/', 'contact/']:
    doc = fetch(f'{BASE}/{path}')
    for tag, needle in [('description', '<meta name="description"'),
                        ('og:image', 'property="og:image"'),
                        ('canonical', 'rel="canonical"')]:
        report(f'/{path} {tag}', 'present' if needle in doc else 'MISSING', 'present', needle in doc)

# ------------------------------------------------------------- sitemap ----
print('\nSitemap')
try:
    xml = fetch(f'{BASE}/page-sitemap.xml')
    n = xml.count('<loc>')
except Exception:                                               # noqa: BLE001
    n = 0
report('page-sitemap.xml urls', n, '15', n == 15)

print(f'\n{ok_count} passed, {fail_count} failed\n')
sys.exit(1 if fail_count else 0)
