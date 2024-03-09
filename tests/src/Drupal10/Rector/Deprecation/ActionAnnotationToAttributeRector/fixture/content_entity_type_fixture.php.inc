<?php

namespace Drupal\node\Entity;

use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the node entity class.
 *
 * @ContentEntityType(
 *   id = "node",
 *   label = @Translation("Content"),
 *   label_collection = @Translation("Content"),
 *   label_singular = @Translation("content item"),
 *   label_plural = @Translation("content items"),
 *   label_count = @PluralTranslation(
 *     singular = "@count content item",
 *     plural = "@count content items"
 *   ),
 *   bundle_label = @Translation("Content type"),
 *   handlers = {
 *     "storage" = "Drupal\node\NodeStorage",
 *     "storage_schema" = "Drupal\node\NodeStorageSchema",
 *     "view_builder" = "Drupal\node\NodeViewBuilder",
 *     "access" = "Drupal\node\NodeAccessControlHandler",
 *     "views_data" = "Drupal\node\NodeViewsData",
 *     "form" = {
 *       "default" = "Drupal\node\NodeForm",
 *       "delete" = "Drupal\node\Form\NodeDeleteForm",
 *       "edit" = "Drupal\node\NodeForm",
 *       "delete-multiple-confirm" = "Drupal\node\Form\DeleteMultiple"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\node\Entity\NodeRouteProvider",
 *     },
 *     "list_builder" = "Drupal\node\NodeListBuilder",
 *     "translation" = "Drupal\node\NodeTranslationHandler"
 *   },
 *   base_table = "node",
 *   data_table = "node_field_data",
 *   revision_table = "node_revision",
 *   revision_data_table = "node_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   list_cache_contexts = { "user.node_grants:view" },
 *   entity_keys = {
 *     "id" = "nid",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "title",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *     "published" = "status",
 *     "uid" = "uid",
 *     "owner" = "uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   bundle_entity_type = "node_type",
 *   field_ui_base_route = "entity.node_type.edit_form",
 *   common_reference_target = TRUE,
 *   permission_granularity = "bundle",
 *   links = {
 *     "canonical" = "/node/{node}",
 *     "delete-form" = "/node/{node}/delete",
 *     "delete-multiple-form" = "/admin/content/node/delete",
 *     "edit-form" = "/node/{node}/edit",
 *     "version-history" = "/node/{node}/revisions",
 *     "revision" = "/node/{node}/revisions/{node_revision}/view",
 *     "create" = "/node",
 *   }
 * )
 */
class Node extends EditorialContentEntityBase implements NodeInterface {

}

-----
<?php

namespace Drupal\node\Entity;

use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the node entity class.
 *
 * @ContentEntityType(
 *   id = "node",
 *   label = @Translation("Content"),
 *   label_collection = @Translation("Content"),
 *   label_singular = @Translation("content item"),
 *   label_plural = @Translation("content items"),
 *   label_count = @PluralTranslation(
 *     singular = "@count content item",
 *     plural = "@count content items"
 *   ),
 *   bundle_label = @Translation("Content type"),
 *   handlers = {
 *     "storage" = "Drupal\node\NodeStorage",
 *     "storage_schema" = "Drupal\node\NodeStorageSchema",
 *     "view_builder" = "Drupal\node\NodeViewBuilder",
 *     "access" = "Drupal\node\NodeAccessControlHandler",
 *     "views_data" = "Drupal\node\NodeViewsData",
 *     "form" = {
 *       "default" = "Drupal\node\NodeForm",
 *       "delete" = "Drupal\node\Form\NodeDeleteForm",
 *       "edit" = "Drupal\node\NodeForm",
 *       "delete-multiple-confirm" = "Drupal\node\Form\DeleteMultiple"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\node\Entity\NodeRouteProvider",
 *     },
 *     "list_builder" = "Drupal\node\NodeListBuilder",
 *     "translation" = "Drupal\node\NodeTranslationHandler"
 *   },
 *   base_table = "node",
 *   data_table = "node_field_data",
 *   revision_table = "node_revision",
 *   revision_data_table = "node_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   list_cache_contexts = { "user.node_grants:view" },
 *   entity_keys = {
 *     "id" = "nid",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "title",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *     "published" = "status",
 *     "uid" = "uid",
 *     "owner" = "uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   bundle_entity_type = "node_type",
 *   field_ui_base_route = "entity.node_type.edit_form",
 *   common_reference_target = TRUE,
 *   permission_granularity = "bundle",
 *   links = {
 *     "canonical" = "/node/{node}",
 *     "delete-form" = "/node/{node}/delete",
 *     "delete-multiple-form" = "/admin/content/node/delete",
 *     "edit-form" = "/node/{node}/edit",
 *     "version-history" = "/node/{node}/revisions",
 *     "revision" = "/node/{node}/revisions/{node_revision}/view",
 *     "create" = "/node",
 *   }
 * )
 */
#[\Drupal\Core\Entity\Attribute\ContentEntityType(id: 'node', label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Content'), label_collection: new \Drupal\Core\StringTranslation\TranslatableMarkup('Content'), label_singular: new \Drupal\Core\StringTranslation\TranslatableMarkup('content item'), label_plural: new \Drupal\Core\StringTranslation\TranslatableMarkup('content items'), label_count: ['singular' => '@count content item', 'plural' => '@count content items'], bundle_label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Content type'), handlers: ['storage' => 'Drupal\node\NodeStorage', 'storage_schema' => 'Drupal\node\NodeStorageSchema', 'view_builder' => 'Drupal\node\NodeViewBuilder', 'access' => 'Drupal\node\NodeAccessControlHandler', 'views_data' => 'Drupal\node\NodeViewsData', 'form' => ['default' => 'Drupal\node\NodeForm', 'delete' => 'Drupal\node\Form\NodeDeleteForm', 'edit' => 'Drupal\node\NodeForm', 'delete-multiple-confirm' => 'Drupal\node\Form\DeleteMultiple'], 'route_provider' => ['html' => 'Drupal\node\Entity\NodeRouteProvider'], 'list_builder' => 'Drupal\node\NodeListBuilder', 'translation' => 'Drupal\node\NodeTranslationHandler'], base_table: 'node', data_table: 'node_field_data', revision_table: 'node_revision', revision_data_table: 'node_field_revision', show_revision_ui: true, translatable: true, list_cache_contexts: ['user.node_grants:view'], entity_keys: ['id' => 'nid', 'revision' => 'vid', 'bundle' => 'type', 'label' => 'title', 'langcode' => 'langcode', 'uuid' => 'uuid', 'status' => 'status', 'published' => 'status', 'uid' => 'uid', 'owner' => 'uid'], revision_metadata_keys: ['revision_user' => 'revision_uid', 'revision_created' => 'revision_timestamp', 'revision_log_message' => 'revision_log'], bundle_entity_type: 'node_type', field_ui_base_route: 'entity.node_type.edit_form', common_reference_target: true, permission_granularity: 'bundle', links: ['canonical' => '/node/{node}', 'delete-form' => '/node/{node}/delete', 'delete-multiple-form' => '/admin/content/node/delete', 'edit-form' => '/node/{node}/edit', 'version-history' => '/node/{node}/revisions', 'revision' => '/node/{node}/revisions/{node_revision}/view', 'create' => '/node'])]
class Node extends EditorialContentEntityBase implements NodeInterface {

}
