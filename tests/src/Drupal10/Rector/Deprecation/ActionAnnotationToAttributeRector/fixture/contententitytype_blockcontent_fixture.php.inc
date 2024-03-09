<?php

namespace Drupal\block_content\Entity;

use Drupal\block_content\Access\RefinableDependentAccessTrait;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\block_content\BlockContentInterface;
use Drupal\user\UserInterface;

/**
 * Defines the content block entity class.
 *
 * @ContentEntityType(
 *   id = "block_content",
 *   label = @Translation("Content block"),
 *   label_collection = @Translation("Content blocks"),
 *   label_singular = @Translation("content block"),
 *   label_plural = @Translation("content blocks"),
 *   label_count = @PluralTranslation(
 *     singular = "@count content block",
 *     plural = "@count content blocks",
 *   ),
 *   bundle_label = @Translation("Block type"),
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "access" = "Drupal\block_content\BlockContentAccessControlHandler",
 *     "list_builder" = "Drupal\block_content\BlockContentListBuilder",
 *     "view_builder" = "Drupal\block_content\BlockContentViewBuilder",
 *     "views_data" = "Drupal\block_content\BlockContentViewsData",
 *     "form" = {
 *       "add" = "Drupal\block_content\BlockContentForm",
 *       "edit" = "Drupal\block_content\BlockContentForm",
 *       "delete" = "Drupal\block_content\Form\BlockContentDeleteForm",
 *       "default" = "Drupal\block_content\BlockContentForm",
 *       "revision-delete" = \Drupal\Core\Entity\Form\RevisionDeleteForm::class,
 *       "revision-revert" = \Drupal\Core\Entity\Form\RevisionRevertForm::class,
 *     },
 *     "route_provider" = {
 *       "revision" = \Drupal\Core\Entity\Routing\RevisionHtmlRouteProvider::class,
 *     },
 *     "translation" = "Drupal\block_content\BlockContentTranslationHandler"
 *   },
 *   admin_permission = "administer block content",
 *   base_table = "block_content",
 *   revision_table = "block_content_revision",
 *   data_table = "block_content_field_data",
 *   revision_data_table = "block_content_field_revision",
 *   show_revision_ui = TRUE,
 *   links = {
 *     "canonical" = "/admin/content/block/{block_content}",
 *     "delete-form" = "/admin/content/block/{block_content}/delete",
 *     "edit-form" = "/admin/content/block/{block_content}",
 *     "collection" = "/admin/content/block",
 *     "create" = "/block",
 *     "revision-delete-form" = "/admin/content/block/{block_content}/revision/{block_content_revision}/delete",
 *     "revision-revert-form" = "/admin/content/block/{block_content}/revision/{block_content_revision}/revert",
 *     "version-history" = "/admin/content/block/{block_content}/revisions",
 *   },
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "bundle" = "type",
 *     "label" = "info",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "published" = "status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log"
 *   },
 *   bundle_entity_type = "block_content_type",
 *   field_ui_base_route = "entity.block_content_type.edit_form",
 *   render_cache = FALSE,
 *  )
 *
 *  Note that render caching of block_content entities is disabled because they
 *  are always rendered as blocks, and blocks already have their own render
 *  caching.
 *  See https://www.drupal.org/node/2284917#comment-9132521 for more information.
 */
class BlockContent extends EditorialContentEntityBase implements BlockContentInterface {

}
-----
<?php

namespace Drupal\block_content\Entity;

use Drupal\block_content\Access\RefinableDependentAccessTrait;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\block_content\BlockContentInterface;
use Drupal\user\UserInterface;

/**
 * Defines the content block entity class.
 *
 * @ContentEntityType(
 *   id = "block_content",
 *   label = @Translation("Content block"),
 *   label_collection = @Translation("Content blocks"),
 *   label_singular = @Translation("content block"),
 *   label_plural = @Translation("content blocks"),
 *   label_count = @PluralTranslation(
 *     singular = "@count content block",
 *     plural = "@count content blocks",
 *   ),
 *   bundle_label = @Translation("Block type"),
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "access" = "Drupal\block_content\BlockContentAccessControlHandler",
 *     "list_builder" = "Drupal\block_content\BlockContentListBuilder",
 *     "view_builder" = "Drupal\block_content\BlockContentViewBuilder",
 *     "views_data" = "Drupal\block_content\BlockContentViewsData",
 *     "form" = {
 *       "add" = "Drupal\block_content\BlockContentForm",
 *       "edit" = "Drupal\block_content\BlockContentForm",
 *       "delete" = "Drupal\block_content\Form\BlockContentDeleteForm",
 *       "default" = "Drupal\block_content\BlockContentForm",
 *       "revision-delete" = \Drupal\Core\Entity\Form\RevisionDeleteForm::class,
 *       "revision-revert" = \Drupal\Core\Entity\Form\RevisionRevertForm::class,
 *     },
 *     "route_provider" = {
 *       "revision" = \Drupal\Core\Entity\Routing\RevisionHtmlRouteProvider::class,
 *     },
 *     "translation" = "Drupal\block_content\BlockContentTranslationHandler"
 *   },
 *   admin_permission = "administer block content",
 *   base_table = "block_content",
 *   revision_table = "block_content_revision",
 *   data_table = "block_content_field_data",
 *   revision_data_table = "block_content_field_revision",
 *   show_revision_ui = TRUE,
 *   links = {
 *     "canonical" = "/admin/content/block/{block_content}",
 *     "delete-form" = "/admin/content/block/{block_content}/delete",
 *     "edit-form" = "/admin/content/block/{block_content}",
 *     "collection" = "/admin/content/block",
 *     "create" = "/block",
 *     "revision-delete-form" = "/admin/content/block/{block_content}/revision/{block_content_revision}/delete",
 *     "revision-revert-form" = "/admin/content/block/{block_content}/revision/{block_content_revision}/revert",
 *     "version-history" = "/admin/content/block/{block_content}/revisions",
 *   },
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "bundle" = "type",
 *     "label" = "info",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "published" = "status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log"
 *   },
 *   bundle_entity_type = "block_content_type",
 *   field_ui_base_route = "entity.block_content_type.edit_form",
 *   render_cache = FALSE,
 *  )
 *
 *  Note that render caching of block_content entities is disabled because they
 *  are always rendered as blocks, and blocks already have their own render
 *  caching.
 *  See https://www.drupal.org/node/2284917#comment-9132521 for more information.
 */
#[\Drupal\Core\Entity\Attribute\ContentEntityType(id: 'block_content', label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Content block'), label_collection: new \Drupal\Core\StringTranslation\TranslatableMarkup('Content blocks'), label_singular: new \Drupal\Core\StringTranslation\TranslatableMarkup('content block'), label_plural: new \Drupal\Core\StringTranslation\TranslatableMarkup('content blocks'), label_count: ['singular' => '@count content block', 'plural' => '@count content blocks'], bundle_label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Block type'), handlers: ['storage' => 'Drupal\Core\Entity\Sql\SqlContentEntityStorage', 'access' => 'Drupal\block_content\BlockContentAccessControlHandler', 'list_builder' => 'Drupal\block_content\BlockContentListBuilder', 'view_builder' => 'Drupal\block_content\BlockContentViewBuilder', 'views_data' => 'Drupal\block_content\BlockContentViewsData', 'form' => ['add' => 'Drupal\block_content\BlockContentForm', 'edit' => 'Drupal\block_content\BlockContentForm', 'delete' => 'Drupal\block_content\Form\BlockContentDeleteForm', 'default' => 'Drupal\block_content\BlockContentForm', 'revision-delete' => \Drupal\Core\Entity\Form\RevisionDeleteForm::class, 'revision-revert' => \Drupal\Core\Entity\Form\RevisionRevertForm::class], 'route_provider' => ['revision' => \Drupal\Core\Entity\Routing\RevisionHtmlRouteProvider::class], 'translation' => 'Drupal\block_content\BlockContentTranslationHandler'], admin_permission: 'administer block content', base_table: 'block_content', revision_table: 'block_content_revision', data_table: 'block_content_field_data', revision_data_table: 'block_content_field_revision', show_revision_ui: true, links: ['canonical' => '/admin/content/block/{block_content}', 'delete-form' => '/admin/content/block/{block_content}/delete', 'edit-form' => '/admin/content/block/{block_content}', 'collection' => '/admin/content/block', 'create' => '/block', 'revision-delete-form' => '/admin/content/block/{block_content}/revision/{block_content_revision}/delete', 'revision-revert-form' => '/admin/content/block/{block_content}/revision/{block_content_revision}/revert', 'version-history' => '/admin/content/block/{block_content}/revisions'], translatable: true, entity_keys: ['id' => 'id', 'revision' => 'revision_id', 'bundle' => 'type', 'label' => 'info', 'langcode' => 'langcode', 'uuid' => 'uuid', 'published' => 'status'], revision_metadata_keys: ['revision_user' => 'revision_user', 'revision_created' => 'revision_created', 'revision_log_message' => 'revision_log'], bundle_entity_type: 'block_content_type', field_ui_base_route: 'entity.block_content_type.edit_form', render_cache: false)]
class BlockContent extends EditorialContentEntityBase implements BlockContentInterface {

}
