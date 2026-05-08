#!/usr/bin/env bash
# Clones or updates the external repos that drupal-rector skills depend on.
#
# Usage: bash .claude/scripts/setup-repos.sh [--update]
#
# Without --update: skips repos that are already cloned.
# With --update:    runs `git fetch --depth=1` on existing clones.
#
# Repos are cloned into repos/ (gitignored) so they are accessible from
# inside the ddev container at /var/www/html/repos/.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPOS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)/repos"
UPDATE=false

for arg in "$@"; do
  [ "$arg" = "--update" ] && UPDATE=true
done

mkdir -p "$REPOS_DIR"

clone_or_update() {
  local name="$1"
  local url="$2"
  local branch="${3:-}"
  local dest="$REPOS_DIR/$name"

  if [ -d "$dest/.git" ]; then
    if $UPDATE; then
      echo "==> Updating $name..."
      git -C "$dest" fetch --depth=1 ${branch:+origin "$branch"} 2>&1 | tail -3
      git -C "$dest" reset --hard FETCH_HEAD
    else
      echo "==> $name already cloned -skipping (use --update to refresh)"
    fi
  else
    echo "==> Cloning $name..."
    local clone_args=(--depth=1)
    [ -n "$branch" ] && clone_args+=(--branch "$branch" --single-branch)
    git clone "${clone_args[@]}" "$url" "$dest"
  fi
}

clone_or_update drupal-digests "https://github.com/dbuytaert/drupal-digests.git"
clone_or_update drupal-core    "https://github.com/drupal/drupal.git" "11.x"

echo ""
echo "Done. Repos available at:"
echo "  repos/drupal-digests  →  /var/www/html/repos/drupal-digests (inside ddev)"
echo "  repos/drupal-core     →  /var/www/html/repos/drupal-core     (inside ddev)"
