#!/usr/bin/env bash
# Corrected: quoted contiguous phrases (the only reliable mechanism for this Zoekt index).
# Inside quotes: char classes work as regex AND leading backslash is significant.
set -u
OUT=/Users/bjorn/projects/drupal-rector/scratchpad/v2_covers
D11=/Users/bjorn/projects/drupal-rector/scratchpad/d11_repos.txt
NUM=2000
enc() { python3 -c "import urllib.parse,sys;print(urllib.parse.quote(sys.argv[1]))" "$1"; }

run() {
  local id="$1"; local raw="$2"
  local q="-r:drupal ${raw} f:test"
  local json="$OUT/r2_${id}.json"
  curl -s "https://api.tresbien.tech/v1/search?q=$(enc "$q")&num=${NUM}" -o "$json"
  local fc; fc=$(jq -r '.Result.FileCount // "ERR"' "$json" 2>/dev/null)
  local repos; repos=$(jq -r '.Result.RepoURLs // {} | keys | length' "$json" 2>/dev/null)
  jq -r '.Result.RepoURLs // {} | keys[]' "$json" 2>/dev/null | sed 's#.*/##' | sort -u > "$OUT/r2_${id}.repos.txt"
  local d11; d11=$(comm -12 <(sort -u "$OUT/r2_${id}.repos.txt") <(sort -u "$D11") | wc -l | tr -d ' ')
  printf '%-26s FC~%-6s repos=%-5s d11=%-5s  q=[%s]\n' "$id" "$fc" "$repos" "$d11" "$q"
  echo "$q" > "$OUT/r2_${id}.query.txt"
}

# Sanity totals (single tokens — reliable)
run t_covers          '@covers'
run t_cdc             '@coversDefaultClass'
# Class-target FQCN (leading backslash, no method) :  "@covers \Class"  -> but exclude ::  -> use FQCN-with-EOL not feasible; measure FQCN-any then method
# We measure by leading-char buckets (these overlap with method forms; refined below):
run fqcn_anyslash     '"@covers \\\\"'          # @covers \... (leading backslash) — FQCN, class OR ::method
run unqual_anyletter  '"@covers [A-Z]"'         # @covers Capital... (no leading backslash) — unqualified, class OR ::method
run method_ext        '"@covers ::"'            # @covers ::method  (coversDefaultClass extension / global fn)
# coversDefaultClass split
run cdc_fqcn          '"@coversDefaultClass \\\\"'
run cdc_unqual        '"@coversDefaultClass [A-Z]"'
# Method-with-class subsets (contiguous, require :: somewhere after a class token)
run fqcn_method       '"@covers \\\\[A-Z][A-Za-z0-9_\\\\]*::"'   # @covers \Foo\Bar::method
run unqual_method     '"@covers [A-Z][A-Za-z0-9_]*::"'           # @covers Foo::method  (no leading bs)
