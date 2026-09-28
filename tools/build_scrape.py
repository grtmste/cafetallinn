# -*- coding: utf-8 -*-
import json, os, re, shutil, sys, unicodedata
sys.path.insert(0, os.path.dirname(__file__))
from transcription import MENU, DRINKS, SRC

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
UP = sys.argv[1]  # folder with the images the client uploaded
OUT = os.path.join(ROOT, 'scrape')
IMG = os.path.join(OUT, 'images')

def slug(s):
    s = unicodedata.normalize('NFKD', s).encode('ascii', 'ignore').decode()
    return re.sub(r'[^a-z0-9]+', '-', s.lower()).strip('-')

def build(data, kind):
    cats = []
    for order, (name, heading, page, items) in enumerate(data, 1):
        cats.append({
            'name': name, 'slug': slug(name), 'source_heading': heading, 'order': order,
            'source_image': 'images/' + SRC[page],
            'items': [{'name': n, 'price': p, 'description': d, 'tags': [], 'order': i,
                       'needs_review': True} for i, (n, p, d) in enumerate(items, 1)],
        })
    return {
        '_source': 'Transcribed verbatim from the %s page images supplied by the client '
                   '(live page images /wp-content/uploads/2026/09/*_20260827_132945_000N.png). '
                   'Every entry is needs_review because it was read from an image.' % kind,
        'type': kind, 'categories': cats,
    }

menu = build(MENU, 'menuu')
drinks = build(DRINKS, 'joogikaart')
json.dump(menu, open(os.path.join(OUT, 'menu.json'), 'w'), ensure_ascii=False, indent=2)
json.dump(drinks, open(os.path.join(OUT, 'drinks.json'), 'w'), ensure_ascii=False, indent=2)

contact = {
    '_source': 'Live site footer + Kontakt page (client screenshots), WXR export.',
    'name': 'Café Tallinn',
    'address': {'value': 'Rannarootsi keskus, Haapsalu', 'needs_review': True,
                'note': 'Only "Haapsalu Rannarootsi keskuses" appears in site text; the map pin sits in the Rannarootsi centre next to JYSK. Street address not published on the site.'},
    'phone': {'value': '+372 56630243', 'href': 'tel:+37256630243',
              'source': 'Kontakt page + footer on every page'},
    'phone_other': {'value': '+372 472 9021', 'source': 'FAQ answer "Kuidas ma saan lauda reserveerida?" only', 'needs_review': True},
    'email': 'info@cafetallinn.ee',
    'opening_hours': {
        'KOHVIK AVATUD': ['E-L 10.00 - 20.00', 'P 10.00 - 19.00'],
        'TERRASS AVATUD': ['E-N 10.00-20.00', 'R-L 10.00-23.00', 'P 10.00-19.00'],
        'note': 'Verbatim from the live footer. Entrance chalkboard photo (ANF_6902) shows older hours.',
    },
    'kitchen_note': 'Köök läheb kinni 15 minutit enne kohviku sulgemist. (KKK)',
    'social': {'facebook': 'http://www.facebook.com/cafetallinn',
               'instagram': {'value': None, 'needs_review': True,
                             'note': 'Instagram icon is shown in the live footer but its URL is not in the export.'}},
    'map': {'query': 'Cafe Tallinn, Rannarootsi keskus, Haapsalu',
            'embed_url': 'https://maps.google.com/maps?q=Cafe%20Tallinn%2C%20Rannarootsi%20keskus%2C%20Haapsalu&z=16&output=embed',
            'needs_review': True, 'note': 'Live footer embeds a Google map pinned on "Cafe Tallinn" in Rannarootsi.'},
}
json.dump(contact, open(os.path.join(OUT, 'contact.json'), 'w'), ensure_ascii=False, indent=2)

# ---- images: file the client uploads under their original / descriptive names
M = {
    '1.webp': SRC['p1'], '39.webp': None,
    '37.webp': SRC['p2'], '35.webp': SRC['p3'], '36.webp': SRC['p4'], '38.webp': SRC['p5'],
    '34.webp': SRC['p6'], '33.webp': SRC['p7'], '32.webp': SRC['p8'], '31.webp': SRC['p9'],
    '2.png': 'screenshot-kontakt.png', '3.webp': 'screenshot-galerii.webp', '4.webp': 'screenshot-broneeri.webp',
    '5.webp': 'screenshot-joogikaart.webp', '6.webp': 'screenshot-avaleht.webp',
    '7.jpg': 'fresh-coffee-steams-wooden-table-close-up-generative-ai.jpg',
    '8.png': 'cafe-tallinn-logo-transparent.png',
    '9.jpg': 'close-up-barista-making-cappuccino-bartender-preparing-coffee-drink.jpg',
    '10.jpg': 'selective-focus-shot-bucket-with-forks-napkins-trendy-cafe-table.jpg',
    '11.jpg': 'ANF_6611.jpg', '12.jpg': 'ANF_6615.jpg', '13.jpg': 'ANF_6649.jpg', '14.jpg': 'ANF_6687.jpg',
    '15.jpg': 'ANF_6726.jpg', '16.jpg': 'ANF_6790.jpg', '17.jpg': 'ANF_6817.jpg', '18.jpg': 'ANF_6854.jpg',
    '19.jpg': 'ANF_6902.jpg', '20.jpg': 'ANF_6909.jpg', '21.jpg': 'ANF_6945.jpg', '22.jpg': 'ANF_6914.jpg',
    '23.jpg': 'ANF_6916.jpg', '24.jpg': 'ANF_6925.jpg', '25.jpg': 'ANF_6927.jpg',
    '26.jpg': '305937859_502083528587132_3184160036415149844_n.jpg',
    '27.webp': 'avaleht-tile-menuu-2024-02-11.webp', '28.webp': 'avaleht-tile-joogikaart-2024-02-21.webp',
    '29.webp': 'avaleht-tile-sundmused-2024-02-31.webp', '30.webp': 'avaleht-tile-catering-2024-02-41.webp',
}
os.makedirs(IMG, exist_ok=True)
received = {}
for src, dst in M.items():
    if dst:
        shutil.copyfile(os.path.join(UP, src), os.path.join(IMG, dst))
        received[dst] = src

mp = json.load(open(os.path.join(IMG, 'mapping.json')))
byname = {i['filename']: i for i in mp['images']}
for dst, src in received.items():
    e = byname.get(dst)
    if e:
        e['downloaded'] = True
        e['local_file'] = dst
        e['client_upload'] = src
alias = {'cafe-tallinn-logo-transparent.png': '59198809-d0da-4aec-9a38-e2a0a9230b91.png',
         'fresh-coffee-steams-wooden-table-close-up-generative-ai.jpg': 'fresh-coffee-steams-wooden-table-close-up-generative-ai-scaled.jpg',
         'close-up-barista-making-cappuccino-bartender-preparing-coffee-drink.jpg': 'close-up-barista-making-cappuccino-bartender-preparing-coffee-drink-scaled.jpg',
         'selective-focus-shot-bucket-with-forks-napkins-trendy-cafe-table.jpg': 'selective-focus-shot-bucket-with-forks-napkins-trendy-cafe-table-scaled.jpg',
         'avaleht-tile-menuu-2024-02-11.webp': '11.png', 'avaleht-tile-joogikaart-2024-02-21.webp': '21.png',
         'avaleht-tile-sundmused-2024-02-31.webp': '31.png', 'avaleht-tile-catering-2024-02-41.webp': '41.png'}
for dst, orig in alias.items():
    for e in mp['images']:
        if e['filename'] == orig and ('2024/02/' in e['url'] or not orig.endswith('1.png')):
            e['downloaded'] = True; e['local_file'] = dst; e['client_upload'] = received[dst]
            e['note'] = 'Supplied by client as %s; matched by size/content (probable, not byte-identical).' % received[dst]
            break
mp['_note'] = ('Binary files supplied by the client in chat (cafetallinn.ee is blocked from the build environment). '
               '"local_file" = file in this folder. Screenshots and menu page images are listed under extra_files.')
mp['extra_files'] = [{'file': d, 'client_upload': s} for d, s in received.items() if d not in [e.get('local_file') for e in mp['images']]]
json.dump(mp, open(os.path.join(IMG, 'mapping.json'), 'w'), ensure_ascii=False, indent=2)

print('menu', sum(len(c['items']) for c in menu['categories']), 'items in', len(menu['categories']), 'categories')
print('drinks', sum(len(c['items']) for c in drinks['categories']), 'items in', len(drinks['categories']), 'categories')
print('images filed', len(received), 'mapped', sum(1 for e in mp['images'] if e.get('downloaded')))
