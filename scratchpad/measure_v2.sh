#!/usr/bin/env bash
set -u
OUT=/Users/bjorn/projects/drupal-rector/scratchpad
mkdir -p "$OUT/v2"

enc() { python3 -c "import urllib.parse,sys;print(urllib.parse.quote(sys.argv[1]))" "$1"; }

# fetch: id, full-query-string, num  -> writes $OUT/v2/$id.json, prints id\tFileCount\tRepoCount
fetch() {
  local id="$1" q="$2" num="$3"
  local Q; Q=$(enc "$q")
  curl -s "https://api.tresbien.tech/v1/search?q=$Q&num=$num" -o "$OUT/v2/$id.json"
  local fc rc files
  fc=$(jq '.Result.FileCount // 0' "$OUT/v2/$id.json" 2>/dev/null)
  rc=$(jq '.Result.RepoURLs | if type=="object" then (keys|length) elif type=="array" then length else 0 end' "$OUT/v2/$id.json" 2>/dev/null)
  files=$(jq '.Result.Files | length' "$OUT/v2/$id.json" 2>/dev/null)
  # write distinct repo list from RepoURLs (authoritative, not Files-capped)
  jq -r '.Result.RepoURLs | if type=="object" then keys[] elif type=="array" then .[] else empty end' "$OUT/v2/$id.json" 2>/dev/null | sort -u > "$OUT/v2/$id.repos.txt"
  printf "%s\tFileCount=%s\tRepoURLs=%s\tFilesArr=%s\n" "$id" "${fc:-ERR}" "${rc:-ERR}" "${files:-ERR}"
}

# tag list: id  pattern(without -r:drupal)
TAGS="covers:@covers\b
coversDefaultClass:@coversDefaultClass\b
coversNothing:@coversNothing\b
uses:@uses\b
requires:@requires\b
medium:@medium\b
small:@small\b
large:@large\b
preserveGlobalState:@preserveGlobalState\b
backupGlobals:@backupGlobals\b
runInSeparateProcess:@runInSeparateProcess\b
testdox:@testdox\b"

echo "### RAW + TEST-SCOPED per tag (num=200, RepoURLs is authoritative for distinct repos) ###"
while IFS=: read -r id pat; do
  [ -z "$id" ] && continue
  fetch "${id}_raw"  "-r:drupal ${pat}"          200
  fetch "${id}_test" "-r:drupal ${pat} f:test"   200
done <<< "$TAGS"
