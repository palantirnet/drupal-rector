# 54 Rules Overview

<br>

## Categories

- [Drupal10](#drupal10) (3)

- [Drupal8](#drupal8) (18)

- [Drupal9](#drupal9) (26)

- [DrupalRector](#drupalrector) (7)

<br>

## Drupal10

### AnnotationToAttributeRector

Change annotations with value to attribute

:wrench: **configure it!**

- class: [`DrupalRector\Drupal10\Rector\Deprecation\AnnotationToAttributeRector`](../src/Drupal10/Rector/Deprecation/AnnotationToAttributeRector.php)

```diff
 namespace Drupal\Core\Action\Plugin\Action;

+use Drupal\Core\Action\Plugin\Action\Derivative\EntityPublishedActionDeriver;
+use Drupal\Core\Action\Attribute\Action;
 use Drupal\Core\Session\AccountInterface;
+use Drupal\Core\StringTranslation\TranslatableMarkup;

 /**
  * Publishes an entity.
- *
- * @Action(
- *   id = "entity:publish_action",
- *   action_label = @Translation("Publish"),
- *   deriver = "Drupal\Core\Action\Plugin\Action\Derivative\EntityPublishedActionDeriver",
- * )
  */
+#[Action(
+  id: 'entity:publish_action',
+  action_label: new TranslatableMarkup('Publish'),
+  deriver: EntityPublishedActionDeriver::class
+)]
 class PublishAction extends EntityActionBase {
```

<br>

### SystemTimeZonesRector

Fixes deprecated `system_time_zones()` calls

:wrench: **configure it!**

- class: [`DrupalRector\Drupal10\Rector\Deprecation\SystemTimeZonesRector`](../src/Drupal10/Rector/Deprecation/SystemTimeZonesRector.php)

```diff
-system_time_zones();
-system_time_zones(FALSE, TRUE);
-system_time_zones(NULL, FALSE);
-system_time_zones(TRUE, FALSE);
+\Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList();
+\Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsListByRegion();
+\Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList(NULL);
+\Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList(TRUE);
```

<br>

### WatchdogExceptionRector

Fixes deprecated watchdog_exception('update', `$exception)` calls

:wrench: **configure it!**

- class: [`DrupalRector\Drupal10\Rector\Deprecation\WatchdogExceptionRector`](../src/Drupal10/Rector/Deprecation/WatchdogExceptionRector.php)

```diff
-watchdog_exception('update', $exception);
+use \Drupal\Core\Utility\Error;
+$logger = \Drupal::logger('update');
+Error::logException($logger, $exception);
```

<br>

## Drupal8

### DBRector

Fixes deprecated `db_delete()` calls

:wrench: **configure it!**

- class: [`DrupalRector\Drupal8\Rector\Deprecation\DBRector`](../src/Drupal8/Rector/Deprecation/DBRector.php)

```diff
-db_delete($table, $options);
+\Drupal::database()->delete($table, $options);
```

<br>

```diff
-db_insert($table, $options);
+\Drupal::database()->insert($table, $options);
```

<br>

```diff
-db_query($query, $args, $options);
+\Drupal::database()->query($query, $args, $options);
```

<br>

```diff
-db_select($table, $alias, $options);
+\Drupal::database()->select($table, $alias, $options);
```

<br>

```diff
-db_update($table, $options);
+\Drupal::database()->update($table, $options);
```

<br>

### DrupalLRector

Fixes deprecated `\Drupal::l()` calls

- class: [`DrupalRector\Drupal8\Rector\Deprecation\DrupalLRector`](../src/Drupal8/Rector/Deprecation/DrupalLRector.php)

```diff
-\Drupal::l('User Login', \Drupal\Core\Url::fromRoute('user.login'));
+\Drupal\Core\Link::fromTextAndUrl('User Login', \Drupal\Core\Url::fromRoute('user.login'));
```

<br>

### DrupalServiceRenameRector

Renames the IDs in `Drupal::service()` calls

:wrench: **configure it!**

- class: [`DrupalRector\Drupal8\Rector\Deprecation\DrupalServiceRenameRector`](../src/Drupal8/Rector/Deprecation/DrupalServiceRenameRector.php)

```diff
-\Drupal::service('old')->foo();
+\Drupal::service('bar')->foo();
```

<br>

### DrupalSetMessageRector

Fixes deprecated `drupal_set_message()` calls

- class: [`DrupalRector\Drupal8\Rector\Deprecation\DrupalSetMessageRector`](../src/Drupal8/Rector/Deprecation/DrupalSetMessageRector.php)

```diff
-drupal_set_message('example status', 'status');
+\Drupal::messenger()->addStatus('example status');
```

<br>

### DrupalURLRector

Fixes deprecated `\Drupal::url()` calls

- class: [`DrupalRector\Drupal8\Rector\Deprecation\DrupalURLRector`](../src/Drupal8/Rector/Deprecation/DrupalURLRector.php)

```diff
-\Drupal::url('user.login');
+\Drupal\Core\Url::fromRoute('user.login')->toString();
```

<br>

### EntityCreateRector

Fixes deprecated `entity_create()` calls

- class: [`DrupalRector\Drupal8\Rector\Deprecation\EntityCreateRector`](../src/Drupal8/Rector/Deprecation/EntityCreateRector.php)

```diff
-entity_create('node', ['bundle' => 'page', 'title' => 'Hello world']);
+\Drupal::service('entity_type.manager)->getStorage('node')->create(['bundle' => 'page', 'title' => 'Hello world']);
```

<br>

### EntityDeleteMultipleRector

Fixes deprecated `entity_delete_multiple()` calls

- class: [`DrupalRector\Drupal8\Rector\Deprecation\EntityDeleteMultipleRector`](../src/Drupal8/Rector/Deprecation/EntityDeleteMultipleRector.php)

```diff
-entity_delete_multiple('node', [1, 2, 42]);
+\Drupal::service('entity_type.manager')->getStorage('node')->delete(\Drupal::service('entity_type.manager')->getStorage('node')->loadMultiple(1, 2, 42));
```

<br>

### EntityInterfaceLinkRector

Fixes deprecated `link()` calls

- class: [`DrupalRector\Drupal8\Rector\Deprecation\EntityInterfaceLinkRector`](../src/Drupal8/Rector/Deprecation/EntityInterfaceLinkRector.php)

```diff
-$url = $entity->link();
+$url = $entity->toLink()->toString();
```

<br>

### EntityLoadRector

Fixes deprecated `ENTITY_TYPE_load()` or `entity_load()` use

:wrench: **configure it!**

- class: [`DrupalRector\Drupal8\Rector\Deprecation\EntityLoadRector`](../src/Drupal8/Rector/Deprecation/EntityLoadRector.php)

```diff
-$entity = ENTITY_TYPE_load(123);
-$node = entity_load('node', 123);
+$entity = \Drupal::entityManager()->getStorage('ENTITY_TYPE')->load(123);
+$node = \Drupal::entityManager()->getStorage('node')->load(123);
```

<br>

### EntityManagerRector

Fixes deprecated `\Drupal::entityManager()` calls

- class: [`DrupalRector\Drupal8\Rector\Deprecation\EntityManagerRector`](../src/Drupal8/Rector/Deprecation/EntityManagerRector.php)

```diff
-$entity_manager = \Drupal::entityManager();
+$entity_manager = \Drupal::entityTypeManager();
```

<br>

### EntityViewRector

Fixes deprecated `entity_view()` use

- class: [`DrupalRector\Drupal8\Rector\Deprecation\EntityViewRector`](../src/Drupal8/Rector/Deprecation/EntityViewRector.php)

```diff
-$rendered = entity_view($entity, 'default');
+$rendered = \Drupal::entityTypeManager()->getViewBuilder($entity
+  ->getEntityTypeId())->view($entity, 'default');
```

<br>

### FileDefaultSchemeRector

Fixes deprecated file_default_scheme calls

- class: [`DrupalRector\Drupal8\Rector\Deprecation\FileDefaultSchemeRector`](../src/Drupal8/Rector/Deprecation/FileDefaultSchemeRector.php)

```diff
-$file_default_scheme = file_default_scheme();
+$file_default_scheme = \Drupal::config('system.file')->get('default_scheme');
```

<br>

### FunctionalTestDefaultThemePropertyRector

Adds `$defaultTheme` property to Functional and FunctionalJavascript tests which do not have them.

- class: [`DrupalRector\Drupal8\Rector\Deprecation\FunctionalTestDefaultThemePropertyRector`](../src/Drupal8/Rector/Deprecation/FunctionalTestDefaultThemePropertyRector.php)

```diff
 class SomeClassTest {
+  protected $defaultTheme = 'stark'
 }
```

<br>

### GetMockRector

Fixes deprecated `getMock()` calls

:wrench: **configure it!**

- class: [`DrupalRector\Drupal8\Rector\Deprecation\GetMockRector`](../src/Drupal8/Rector/Deprecation/GetMockRector.php)

```diff
-$this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
+$this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
```

<br>

### LinkGeneratorTraitLRector

Fixes deprecated `l()` calls

- class: [`DrupalRector\Drupal8\Rector\Deprecation\LinkGeneratorTraitLRector`](../src/Drupal8/Rector/Deprecation/LinkGeneratorTraitLRector.php)

```diff
-$this->l($text, $url);
+\Drupal\Core\Link::fromTextAndUrl($text, $url);
```

<br>

### RequestTimeConstRector

Fixes deprecated REQUEST_TIME calls

- class: [`DrupalRector\Drupal8\Rector\Deprecation\RequestTimeConstRector`](../src/Drupal8/Rector/Deprecation/RequestTimeConstRector.php)

```diff
-$request_time = REQUEST_TIME;
+$request_time = \Drupal::time()->getRequestTime();
```

<br>

### SafeMarkupFormatRector

Fixes deprecated `SafeMarkup::format()` calls

- class: [`DrupalRector\Drupal8\Rector\Deprecation\SafeMarkupFormatRector`](../src/Drupal8/Rector/Deprecation/SafeMarkupFormatRector.php)

```diff
-$safe_string_markup_object = \Drupal\Component\Utility\SafeMarkup::format('hello world');
+$safe_string_markup_object = new \Drupal\Component\Render\FormattableMarkup('hello world');
```

<br>

### StaticToFunctionRector

Fixes deprecated `\Drupal\Component\Utility\Unicode::strlen()` calls

:wrench: **configure it!**

- class: [`DrupalRector\Drupal8\Rector\Deprecation\StaticToFunctionRector`](../src/Drupal8/Rector/Deprecation/StaticToFunctionRector.php)

```diff
-$length = \Drupal\Component\Utility\Unicode::strlen('example');
+$length = mb_strlen('example');
```

<br>

```diff
-$string = \Drupal\Component\Utility\Unicode::strtolower('example');
+$string = mb_strtolower('example');
```

<br>

```diff
-$string = \Drupal\Component\Utility\Unicode::substr('example', 0, 2);
+$string = mb_substr('example', 0, 2);
```

<br>

## Drupal9

### AssertFieldByIdRector

Fixes deprecated `AssertLegacyTrait::assertFieldById()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\AssertFieldByIdRector`](../src/Drupal9/Rector/Deprecation/AssertFieldByIdRector.php)

```diff
-$this->assertFieldById('edit-name', NULL);
-    $this->assertFieldById('edit-name', 'Test name');
-    $this->assertFieldById('edit-description', NULL);
-    $this->assertFieldById('edit-description');
+$this->assertSession()->fieldExists('edit-name');
+    $this->assertSession()->fieldValueEquals('edit-name', 'Test name');
+    $this->assertSession()->fieldExists('edit-description');
+    $this->assertSession()->fieldValueEquals('edit-description', '');
```

<br>

### AssertFieldByNameRector

Fixes deprecated `AssertLegacyTrait::assertFieldByName()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\AssertFieldByNameRector`](../src/Drupal9/Rector/Deprecation/AssertFieldByNameRector.php)

```diff
-$this->assertFieldByName('field_name', 'expected_value');
-$this->assertFieldByName("field_name[0][value][date]", '', 'Date element found.');
-$this->assertFieldByName("field_name[0][value][time]", null, 'Time element found.');
+$this->assertSession()->fieldValueEquals('field_name', 'expected_value');
+$this->assertSession()->fieldValueEquals("field_name[0][value][date]", '');
+$this->assertSession()->fieldExists("field_name[0][value][time]");
```

<br>

### AssertLegacyTraitRector

Fixes deprecated `AssertLegacyTrait::METHOD()` calls

:wrench: **configure it!**

- class: [`DrupalRector\Drupal9\Rector\Deprecation\AssertLegacyTraitRector`](../src/Drupal9/Rector/Deprecation/AssertLegacyTraitRector.php)

```diff
-$this->assertLinkByHref('user/1/translations');
+$this->assertSession()->linkByHrefExists('user/1/translations');
```

<br>

```diff
-$this->assertLink('Anonymous comment title');
+$this->assertSession()->linkExists('Anonymous comment title');
```

<br>

```diff
-$this->assertNoEscaped('<div class="escaped">');
+$this->assertSession()->assertNoEscaped('<div class="escaped">');
```

<br>

```diff
-$this->assertNoFieldChecked('edit-settings-view-mode', 'default');
+$this->assertSession()->checkboxNotChecked('edit-settings-view-mode', 'default');
```

<br>

```diff
-$this->assertNoField('files[upload]', 'Found file upload field.');
+$this->assertSession()->fieldNotExists('files[upload]', 'Found file upload field.');
```

<br>

```diff
-$this->assertNoLinkByHref('user/2/translations');
+$this->assertSession()->linkByHrefNotExists('user/2/translations');
```

<br>

```diff
-$this->assertNoLink('Anonymous comment title');
+$this->assertSession()->linkNotExists('Anonymous comment title');
```

<br>

```diff
-$this->assertNoOption('edit-settings-view-mode', 'default');
+$this->assertSession()->optionNotExists('edit-settings-view-mode', 'default');
```

<br>

```diff
-$this->assertNoPattern('|<h4[^>]*></h4>|', 'No empty H4 element found.');
+$this->assertSession()->responseNotMatches('|<h4[^>]*></h4>|', 'No empty H4 element found.');
```

<br>

```diff
-$this->assertPattern('|<h4[^>]*></h4>|', 'No empty H4 element found.');
+$this->assertSession()->responseMatches('|<h4[^>]*></h4>|', 'No empty H4 element found.');
```

<br>

```diff
-$this->assertNoRaw('bartik/logo.svg');
+$this->assertSession()->responseNotContains('bartik/logo.svg');
```

<br>

```diff
-$this->assertRaw('bartik/logo.svg');
+$this->assertSession()->responseContains('bartik/logo.svg');
```

<br>

### AssertNoFieldByIdRector

Fixes deprecated `AssertLegacyTrait::assertNoFieldById()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\AssertNoFieldByIdRector`](../src/Drupal9/Rector/Deprecation/AssertNoFieldByIdRector.php)

```diff
-$this->assertNoFieldById('name');
-    $this->assertNoFieldById('name', 'not the value');
-    $this->assertNoFieldById('notexisting', NULL);
+$this->assertSession()->assertNoFieldById('name');
+    $this->assertSession()->fieldValueNotEquals('name', 'not the value');
+    $this->assertSession()->fieldNotExists('notexisting');
```

<br>

### AssertNoFieldByNameRector

Fixes deprecated `AssertLegacyTrait::assertNoFieldByName()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\AssertNoFieldByNameRector`](../src/Drupal9/Rector/Deprecation/AssertNoFieldByNameRector.php)

```diff
-$this->assertNoFieldByName('name');
-    $this->assertNoFieldByName('name', 'not the value');
-    $this->assertNoFieldByName('notexisting');
-    $this->assertNoFieldByName('notexisting', NULL);
+$this->assertSession()->fieldValueNotEquals('name', '');
+    $this->assertSession()->fieldValueNotEquals('name', 'not the value');
+    $this->assertSession()->fieldValueNotEquals('notexisting', '');
+    $this->assertSession()->fieldNotExists('notexisting');
```

<br>

### AssertNoUniqueTextRector

Fixes deprecated `AssertLegacyTrait::assertNoUniqueText()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\AssertNoUniqueTextRector`](../src/Drupal9/Rector/Deprecation/AssertNoUniqueTextRector.php)

```diff
-$this->assertNoUniqueText('Duplicated message');
+$page_text = $this->getSession()->getPage()->getText();
+$nr_found = substr_count($page_text, 'Duplicated message');
+$this->assertGreaterThan(1, $nr_found, "'Duplicated message' found more than once on the page");
```

<br>

### AssertOptionSelectedRector

Fixes deprecated `AssertLegacyTrait::assertOptionSelected()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\AssertOptionSelectedRector`](../src/Drupal9/Rector/Deprecation/AssertOptionSelectedRector.php)

```diff
-$this->assertOptionSelected('options', 2);
+$this->assertTrue($this->assertSession()->optionExists('options', 2)->hasAttribute('selected'));
```

<br>

### ConstructFieldXpathRector

Fixes deprecated `AssertLegacyTrait::constructFieldXpath()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\ConstructFieldXpathRector`](../src/Drupal9/Rector/Deprecation/ConstructFieldXpathRector.php)

```diff
-$this->constructFieldXpath('id', 'edit-preferred-admin-langcode');
+$this->getSession()->getPage()->findField('edit-preferred-admin-langcode');
```

<br>

### ExtensionPathRector

Fixes deprecated `drupal_get_filename()` calls

:wrench: **configure it!**

- class: [`DrupalRector\Drupal9\Rector\Deprecation\ExtensionPathRector`](../src/Drupal9/Rector/Deprecation/ExtensionPathRector.php)

```diff
-drupal_get_filename('module', 'node');
-drupal_get_filename('theme', 'seven');
-drupal_get_filename('profile', 'standard');
+\Drupal::service('extension.list.module')->getPathname('node');
+\Drupal::service('extension.list.theme')->getPathname('seven');
+\Drupal::service('extension.list.profile')->getPathname('standard');
```

<br>

```diff
-drupal_get_path('module', 'node');
-drupal_get_path('theme', 'seven');
-drupal_get_path('profile', 'standard');
+\Drupal::service('extension.list.module')->getPath('node');
+\Drupal::service('extension.list.theme')->getPath('seven');
+\Drupal::service('extension.list.profile')->getPath('standard');
```

<br>

### FileBuildUriRector

Fixes deprecated `file_build_uri()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\FileBuildUriRector`](../src/Drupal9/Rector/Deprecation/FileBuildUriRector.php)

```diff
-$uri1 = file_build_uri('path/to/file.txt');
+$uri1 = \Drupal::service('stream_wrapper_manager')->normalizeUri(\Drupal::config('system.file')->get('default_scheme') . ('://' . 'path/to/file.txt'));
 $path = 'path/to/other/file.png';
-$uri2 = file_build_uri($path);
+$uri2 = \Drupal::service('stream_wrapper_manager')->normalizeUri(\Drupal::config('system.file')->get('default_scheme') . ('://' . $path));
```

<br>

### FileCreateUrlRector

Fixes deprecated `file_create_url()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\FileCreateUrlRector`](../src/Drupal9/Rector/Deprecation/FileCreateUrlRector.php)

```diff
-file_create_url($uri);
+\Drupal::service('file_url_generator')->generateAbsoluteString($uri);
```

<br>

### FileUrlTransformRelativeRector

Fixes deprecated `file_url_transform_relative()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\FileUrlTransformRelativeRector`](../src/Drupal9/Rector/Deprecation/FileUrlTransformRelativeRector.php)

```diff
-file_url_transform_relative($uri);
+\Drupal::service('file_url_generator')->transformRelative($uri);
```

<br>

### FromUriRector

Fixes deprecated `file_create_url()` calls from `\Drupal\Core\Url::fromUri().`

- class: [`DrupalRector\Drupal9\Rector\Deprecation\FromUriRector`](../src/Drupal9/Rector/Deprecation/FromUriRector.php)

```diff
-\Drupal\Core\Url::fromUri(file_create_url($uri));
+\Drupal::service('file_url_generator')->generate($uri);
```

<br>

### FunctionToEntityTypeStorageMethod

Refactor function call to an entity storage method

:wrench: **configure it!**

- class: [`DrupalRector\Drupal9\Rector\Deprecation\FunctionToEntityTypeStorageMethod`](../src/Drupal9/Rector/Deprecation/FunctionToEntityTypeStorageMethod.php)

```diff
-taxonomy_terms_static_reset();
+\Drupal::entityTypeManager()->getStorage('taxonomy_term')->resetCache();

-taxonomy_vocabulary_static_reset($vids);
+\Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->resetCache($vids);
```

<br>

### FunctionToFirstArgMethodRector

Fixes deprecated `taxonomy_implode_tags()` calls

:wrench: **configure it!**

- class: [`DrupalRector\Drupal9\Rector\Deprecation\FunctionToFirstArgMethodRector`](../src/Drupal9/Rector/Deprecation/FunctionToFirstArgMethodRector.php)

```diff
-$url = taxonomy_term_uri($term);
-$name = taxonomy_term_title($term);
+$url = $term->toUrl();
+$name = $term->label();
```

<br>

### GetAllOptionsRector

Fixes deprecated `AssertLegacyTrait::getAllOptions()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\GetAllOptionsRector`](../src/Drupal9/Rector/Deprecation/GetAllOptionsRector.php)

```diff
 $this->drupalGet('/form-test/select');
-    $this->assertCount(6, $this->getAllOptions($this->cssSelect('select[name="opt_groups"]')[0]));
+    $this->assertCount(6, $this->cssSelect('select[name="opt_groups"]')[0]->findAll('xpath', '//option'));
```

<br>

### GetRawContentRector

Fixes deprecated `AssertLegacyTrait::getRawContent()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\GetRawContentRector`](../src/Drupal9/Rector/Deprecation/GetRawContentRector.php)

```diff
-$this->getRawContent();
+$this->getSession()->getPage()->getContent();
```

<br>

### ModuleLoadRector

Fixes deprecated `module_load_install()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\ModuleLoadRector`](../src/Drupal9/Rector/Deprecation/ModuleLoadRector.php)

```diff
-module_load_install('example');
+\Drupal::moduleHandler()->loadInclude('example', 'install');
 $type = 'install';
 $module = 'example';
 $name = 'name';
-module_load_include($type, $module, $name);
-module_load_include($type, $module);
+\Drupal::moduleHandler()->loadInclude($module, $type, $name);
+\Drupal::moduleHandler()->loadInclude($module, $type);
```

<br>

### PassRector

Fixes deprecated `AssertLegacyTrait::pass()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\PassRector`](../src/Drupal9/Rector/Deprecation/PassRector.php)

```diff
-// Check for pass
-$this->pass('The whole transaction is rolled back when a duplicate key insert occurs.');
+// Check for pass
```

<br>

### ProtectedStaticModulesPropertyRector

"public static `$modules"` will have its visibility changed to protected.

- class: [`DrupalRector\Drupal9\Rector\Property\ProtectedStaticModulesPropertyRector`](../src/Drupal9/Rector/Property/ProtectedStaticModulesPropertyRector.php)

```diff
 class SomeClassTest {
-  public static $modules = [];
+  protected static $modules = [];
 }
```

<br>

### SystemSortByInfoNameRector

Fixes deprecated `system_sort_modules_by_info_name()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\SystemSortByInfoNameRector`](../src/Drupal9/Rector/Deprecation/SystemSortByInfoNameRector.php)

```diff
-uasort($modules, 'system_sort_modules_by_info_name');
+uasort($modules, [ModuleExtensionList::class, 'sortByName']);
```

<br>

### TaxonomyTermLoadMultipleByNameRector

Refactor function call to an entity storage method

- class: [`DrupalRector\Drupal9\Rector\Deprecation\TaxonomyTermLoadMultipleByNameRector`](../src/Drupal9/Rector/Deprecation/TaxonomyTermLoadMultipleByNameRector.php)

```diff
-$terms = taxonomy_term_load_multiple_by_name(
-    'Foo',
-    'topics'
-);
+$terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
+    'name' => 'Foo',
+    'vid' => 'topics',
+]);
```

<br>

### TaxonomyVocabularyGetNamesDrupalStaticResetRector

Refactor drupal_static_reset('taxonomy_vocabulary_get_names') to entity storage reset cache

- class: [`DrupalRector\Drupal9\Rector\Deprecation\TaxonomyVocabularyGetNamesDrupalStaticResetRector`](../src/Drupal9/Rector/Deprecation/TaxonomyVocabularyGetNamesDrupalStaticResetRector.php)

```diff
-drupal_static_reset('taxonomy_vocabulary_get_names');
+\Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->resetCache();
```

<br>

### TaxonomyVocabularyGetNamesRector

Refactor function call to an entity storage method

- class: [`DrupalRector\Drupal9\Rector\Deprecation\TaxonomyVocabularyGetNamesRector`](../src/Drupal9/Rector/Deprecation/TaxonomyVocabularyGetNamesRector.php)

```diff
-$vids = taxonomy_vocabulary_get_names();
+$vids = \Drupal::entityQuery('taxonomy_vocabulary')->execute();
```

<br>

### UiHelperTraitDrupalPostFormRector

Fixes deprecated `UiHelperTrait::drupalPostForm()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\UiHelperTraitDrupalPostFormRector`](../src/Drupal9/Rector/Deprecation/UiHelperTraitDrupalPostFormRector.php)

```diff
 $edit = [];
 $edit['action'] = 'action_goto_action';
-$this->drupalPostForm('admin/config/system/actions', $edit, 'Create');
+$this->drupalGet('admin/config/system/actions');
+$this->submitForm($edit, 'Create');
 $edit['action'] = 'action_goto_action_1';
-$this->drupalPostForm(null, $edit, 'Edit');
+$this->submitForm($edit, 'Edit');
```

<br>

### UserPasswordRector

Fixes deprecated `user_password()` calls

- class: [`DrupalRector\Drupal9\Rector\Deprecation\UserPasswordRector`](../src/Drupal9/Rector/Deprecation/UserPasswordRector.php)

```diff
-$pass = user_password();
-$shorter_pass = user_password(8);
+$pass = \Drupal::service('password_generator')->generate();
+$shorter_pass = \Drupal::service('password_generator')->generate(8);
```

<br>

## DrupalRector

### ClassConstantToClassConstantRector

Fixes deprecated class contant use, used in Drupal 9.1 deprecations

:wrench: **configure it!**

- class: [`DrupalRector\Rector\Deprecation\ClassConstantToClassConstantRector`](../src/Rector/Deprecation/ClassConstantToClassConstantRector.php)

```diff
-$value = Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_NAME;
-$value2 = Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT;
-$value3 = Symfony\Cmf\Component\Routing\RouteObjectInterface::CONTROLLER_NAME;
+$value = \Drupal\Core\Routing\RouteObjectInterface::ROUTE_NAME;
+$value2 = \Drupal\Core\Routing\RouteObjectInterface::ROUTE_OBJECT;
+$value3 = \Drupal\Core\Routing\RouteObjectInterface::CONTROLLER_NAME;
```

<br>

### ConstantToClassConstantRector

Fixes deprecated contant use, used in Drupal 8 and 9 deprecations

:wrench: **configure it!**

- class: [`DrupalRector\Rector\Deprecation\ConstantToClassConstantRector`](../src/Rector/Deprecation/ConstantToClassConstantRector.php)

```diff
-$result = file_unmanaged_copy($source, $destination, DEPRECATED_CONSTANT);
+$result = file_unmanaged_copy($source, $destination, \Drupal\MyClass::CONSTANT);
```

<br>

### DeprecationHelperRemoveRector

Remove DeprecationHelper calls for versions before configured minimum requirement

:wrench: **configure it!**

- class: [`DrupalRector\Rector\Deprecation\DeprecationHelperRemoveRector`](../src/Rector/Deprecation/DeprecationHelperRemoveRector.php)

```diff
 $settings = [];
 $filename = 'simple_filename.yaml';
-DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', fn() => new_function(), fn() => old_function());
-DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => SettingsEditor::rewrite($filename, $settings), fn() => drupal_rewrite_settings($settings, $filename));
+drupal_rewrite_settings($settings, $filename);
 DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => new_function(), fn() => old_function());
```

<br>

### FunctionToServiceRector

Fixes deprecated function to service calls, used in Drupal 8 and 9 deprecations

:wrench: **configure it!**

- class: [`DrupalRector\Rector\Deprecation\FunctionToServiceRector`](../src/Rector/Deprecation/FunctionToServiceRector.php)

```diff
-$path = drupal_realpath($path);
+$path = \Drupal::service('file_system')
+    ->realpath($path);
```

<br>

```diff
-$result = drupal_render($elements);
+$result = \Drupal::service('renderer')->render($elements);
```

<br>

```diff
-$result = drupal_render_root($elements);
+$result = \Drupal::service('renderer')->renderRoot($elements);
```

<br>

```diff
-$display = entity_get_display($entity_type, $bundle, $view_mode)
+$display = \Drupal::service('entity_display.repository')
+    ->getViewDisplay($entity_type, $bundle, $view_mode);
```

<br>

```diff
-$display = entity_get_form_display($entity_type, $bundle, $form_mode)
+$display = \Drupal::service('entity_display.repository')
+    ->getFormDisplay($entity_type, $bundle, $form_mode);
```

<br>

```diff
-file_copy();
+\Drupal::service('file.repository')->copy();
```

<br>

```diff
-$dir = file_directory_temp();
+$dir = \Drupal::service('file_system')->getTempDirectory();
```

<br>

```diff
-file_move();
+\Drupal::service('file.repository')->move();
```

<br>

```diff
-$result = file_prepare_directory($directory, $options);
+$result = \Drupal::service('file_system')->prepareDirectory($directory, $options);
```

<br>

```diff
-file_save_data($data);
+\Drupal::service('file.repository')->writeData($data);
```

<br>

```diff
-$files = file_scan_directory($directory);
+$files = \Drupal::service('file_system')->scanDirectory($directory);
```

<br>

```diff
-$result = file_unmanaged_save_data($data, $destination, $replace);
+$result = \Drupal::service('file_system')->saveData($data, $destination, $replace);
```

<br>

```diff
-$result = file_uri_target($uri)
+$result = \Drupal::service('stream_wrapper_manager')->getTarget($uri);
```

<br>

```diff
-$date = format_date($timestamp, $type, $format, $timezone, $langcode);
+$date = \Drupal::service('date.formatter')->format($timestamp, $type, $format, $timezone, $langcode);
```

<br>

```diff
-$date = format_date($timestamp, $type, $format, $timezone, $langcode);
+$date = \Drupal::service('date.formatter')->format($timestamp, $type, $format, $timezone, $langcode);
```

<br>

```diff
-$output = render($build);
+$output = \Drupal::service('renderer')->render($build);
```

<br>

### FunctionToStaticRector

Fixes deprecated `file_directory_os_temp()` calls, used in Drupal 8, 9 and 10 deprecations

:wrench: **configure it!**

- class: [`DrupalRector\Rector\Deprecation\FunctionToStaticRector`](../src/Rector/Deprecation/FunctionToStaticRector.php)

```diff
-$dir = file_directory_os_temp();
+$dir = \Drupal\Component\FileSystem\FileSystem::getOsTemporaryDirectory();
```

<br>

```diff
 $settings = [];
 $filename = 'simple_filename.yaml';
-drupal_rewrite_settings($settings, $filename);
+SettingsEditor::rewrite($filename, $settings);
```

<br>

```diff
 $settings = [];
 $filename = 'simple_filename.yaml';
-drupal_rewrite_settings($settings, $filename);
+SettingsEditor::rewrite($filename, $settings);
```

<br>

### MethodToMethodWithCheckRector

Fixes deprecated `MetadataBag::clearCsrfTokenSeed()` calls, used in Drupal 8 and 9 deprecations

:wrench: **configure it!**

- class: [`DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector`](../src/Rector/Deprecation/MethodToMethodWithCheckRector.php)

```diff
 $metadata_bag = new \Drupal\Core\Session\MetadataBag(new \Drupal\Core\Site\Settings([]));
-$metadata_bag->clearCsrfTokenSeed();
+$metadata_bag->stampNew();
```

<br>

```diff
-$url = $entity->urlInfo();
+$url = $entity->toUrl();
```

<br>

```diff
 /* @var \Drupal\node\Entity\Node $node */
 $node = \Drupal::entityTypeManager()->getStorage('node')->load(123);
 $entity_type = $node->getEntityType();
-$entity_type->getLowercaseLabel();
+$entity_type->getSingularLabel();
```

<br>

### ShouldCallParentMethodsRector

PHPUnit based tests should call parent methods (setUp, tearDown)

- class: [`DrupalRector\Rector\PHPUnit\ShouldCallParentMethodsRector`](../src/Rector/PHPUnit/ShouldCallParentMethodsRector.php)

```diff
 namespace Drupal\Tests\Rector\Deprecation\PHPUnit\ShouldCallParentMethodsRector\fixture;

 use Drupal\KernelTests\KernelTestBase;

 final class SetupVoidTest extends KernelTestBase {

     protected function setUp(): void
     {
+        parent::setUp();
         $test = 'doing things';
     }

     protected function tearDown(): void
     {
+        parent::tearDown();
         $test = 'doing things';
     }

 }
```

<br>
