#!/usr/bin/env bash
set -u
OUT=/Users/bjorn/projects/drupal-rector/scratchpad
mkdir -p "$OUT/repos"

# id and raw regex pattern (literal backslash-b). -r:drupal prepended below.
run() {
  local id="$1" pat="$2"
  local q="-r:drupal ${pat}"
  local Q
  Q=$(python3 -c "import urllib.parse,sys;print(urllib.parse.quote(sys.argv[1]))" "$q")
  local json
  json=$(curl -s "https://api.tresbien.tech/v1/search?q=$Q&num=1000")
  local files
  files=$(echo "$json" | jq '.Result.Files | length' 2>/dev/null)
  echo "$json" | jq -r '.Result.Files[]? | .Repository' 2>/dev/null | sort -u > "$OUT/repos/$id.txt"
  local repos
  repos=$(wc -l < "$OUT/repos/$id.txt" | tr -d ' ')
  local ceiling="no"
  [ "${files:-0}" -ge 1000 ] && ceiling="YES"
  printf "%s\t%s\t%s\t%s\n" "$id" "${files:-ERR}" "$repos" "$ceiling" | tee -a "$OUT/results.tsv"
}

printf "tag\tfiles\trepos\tceiling\n" > "$OUT/results.tsv"

run covers              '@covers\b'
run coversDefaultClass  '@coversDefaultClass\b'
run coversNothing       '@coversNothing\b'
run uses                '@uses\b'
run requires            '@requires\b'
run medium              '@medium\b'
run small               '@small\b'
run large               '@large\b'
run preserveGlobalState '@preserveGlobalState\b'
run backupGlobals       '@backupGlobals\b'
run runInSeparateProcess '@runInSeparateProcess\b'
run testdox             '@testdox\b'
