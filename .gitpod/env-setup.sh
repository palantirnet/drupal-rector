#!/usr/bin/env bash
if [ -n "$DEBUG_DRUPALPOD" ]; then
    set -x
fi

# Create a phpstorm command
sudo cp "${GITPOD_REPO_ROOT}"/.gitpod/phpstorm.template.sh /usr/local/bin/phpstorm

# Start ddev in the drupal website directory
DRUPAL_DIR="${GITPOD_REPO_ROOT}"/drupal-website
cd "$DRUPAL_DIR" && ddev start

#Open preview browser
gp preview "$(gp url 8080)"
