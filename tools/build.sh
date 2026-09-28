#!/usr/bin/env bash
# Build: minify CSS/JS, copy seed data, produce dist/cafetallinn.zip
# (zip root contains the cafetallinn/ theme folder).
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
T="$ROOT/cafetallinn"
cd "$ROOT"
npx --yes esbuild@0.25.10 "$T/assets/css/src/main.css" --minify --outfile="$T/assets/css/main.min.css" --log-level=warning
npx --yes esbuild@0.25.10 "$T/assets/js/src/main.js" --minify --target=es2018 --outfile="$T/assets/js/main.min.js" --log-level=warning
mkdir -p "$T/inc/data"
cp "$ROOT/scrape/menu.json" "$T/inc/data/menu.json"
cp "$ROOT/scrape/drinks.json" "$T/inc/data/drinks.json"
mkdir -p "$ROOT/dist"
rm -f "$ROOT/dist/cafetallinn.zip"
zip -qr "$ROOT/dist/cafetallinn.zip" cafetallinn -x '*.DS_Store' 'cafetallinn/node_modules/*'
ls -la "$ROOT/dist/cafetallinn.zip" "$T/assets/css/main.min.css" "$T/assets/js/main.min.js" | awk '{print $5, $9}'
