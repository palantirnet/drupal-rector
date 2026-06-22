#!/usr/bin/env bash
# Measure @covers/@coversDefaultClass FORM distribution across contrib.
set -u
OUT=/Users/bjorn/projects/drupal-rector/scratchpad/v2_covers
D11=/Users/bjorn/projects/drupal-rector/scratchpad/d11_repos.txt
mkdir -p "$OUT"
NUM=200

enc() { python3 -c "import urllib.parse,sys;print(urllib.parse.quote(sys.argv[1]))" "$1"; }

run() {
  local id="$1"; local raw="$2"
  # all prepend -r:drupal and append f:test
  local q="-r:drupal ${raw} f:test"
  local eq; eq=$(enc "$q")
  local url="https://api.tresbien.tech/v1/search?q=${eq}&num=${NUM}"
  local json="$OUT/${id}.json"
  local http; http=$(curl -s -w "%{http_code}" -o "$json" "$url")
  local fc; fc=$(jq -r '.Result.FileCount // "ERR"' "$json" 2>/dev/null)
  local repos; repos=$(jq -r '.Result.RepoURLs // {} | keys | length' "$json" 2>/dev/null)
  # extract distinct repo names (basename of RepoURLs keys are urls; also use Files[].Repository)
  jq -r '.Result.RepoURLs // {} | keys[]' "$json" 2>/dev/null | sed 's#.*/##' | sort -u > "$OUT/${id}.repos.txt"
  local d11; d11=$(comm -12 <(sort -u "$OUT/${id}.repos.txt") <(sort -u "$D11") | wc -l | tr -d ' ')
  printf '%-28s http=%s FileCount=%s repos=%s d11=%s\n' "$id" "$http" "$fc" "$repos" "$d11"
  echo "$q" > "$OUT/${id}.query.txt"
}

echo "### CLASS-TARGET FORMS"
run p01_fqcn_class        '@covers \\[A-Za-z0-9_\\]+\s*$'
run p02_unqual_class      '@covers [A-Za-z][A-Za-z0-9_]*\s*$'
run p03_cdc_any           '@coversDefaultClass'
run p04_cdc_fqcn          '@coversDefaultClass \\'
run p05_cdc_unqual        '@coversDefaultClass [A-Za-z]'
echo "### METHOD/FUNCTION-TARGET FORMS"
run p06_method_ext        '@covers ::[A-Za-z]'
run p07_fqcn_method       '@covers \\[A-Za-z0-9_\\]+::'
run p08_unqual_method     '@covers [A-Za-z][A-Za-z0-9_]*::'
echo "### SANITY TOTALS"
run p09_covers_total      '@covers'
run p10_covers_backslash  '@covers \\'
