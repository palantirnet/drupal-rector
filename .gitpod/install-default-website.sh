#!/usr/bin/env bash
if [ -n "$DEBUG_DRUPALPOD" ]; then
    set -x
fi

DRUPAL_DIR="${GITPOD_REPO_ROOT}"/drupal-website
mkdir -p "$DRUPAL_DIR"

cd "$DRUPAL_DIR" && ddev config --create-docroot --docroot=web  --project-type=drupal9
cd "$DRUPAL_DIR" && ddev composer create -y drupal/recommended-project
cd "$DRUPAL_DIR" && ddev composer require drush/drush:^10
# Add rector source code as symlink (to ../)
cd "$DRUPAL_DIR" && composer config repositories.drupal-rector '{"type": "path", "url": "../", "options": {"symlink": true}}'
# Get all drupal-rector dependencies
cd "$DRUPAL_DIR" && ddev composer require palantirnet/drupal-rector:\"*\"

# Create hard link of rector.php file
cd "$DRUPAL_DIR" && ln ../rector.php .

# Install standard drupal profile page
cd "$DRUPAL_DIR" && ddev drush si -y --account-pass=admin --site-name="Drupal\ Rector" standard
