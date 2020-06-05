# All 50 Rectors Overview

## `BrowserTestBaseGetMockRector`

- class: [`DrupalRector\Rector\Deprecation\BrowserTestBaseGetMockRector`](/../master/drupal-rector/src/Rector/Deprecation/BrowserTestBaseGetMockRector.php)

Fixes deprecated getMock() calls

```diff
-$this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
+$this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
```

<br>

## `DBDeleteRector`

- class: [`DrupalRector\Rector\Deprecation\DBDeleteRector`](/../master/drupal-rector/src/Rector/Deprecation/DBDeleteRector.php)

Fixes deprecated db_insert() calls

```diff
-db_delete($table, $options);
+\Drupal::database()->delete($table, $options);
```

<br>

## `DBInsertRector`

- class: [`DrupalRector\Rector\Deprecation\DBInsertRector`](/../master/drupal-rector/src/Rector/Deprecation/DBInsertRector.php)

Fixes deprecated db_insert() calls

```diff
-db_insert($table, $options);
+\Drupal::database()->insert($table, $options);
```

<br>

## `DBQueryRector`

- class: [`DrupalRector\Rector\Deprecation\DBQueryRector`](/../master/drupal-rector/src/Rector/Deprecation/DBQueryRector.php)

Fixes deprecated db_query() calls

```diff
-db_query($query, $args, $options);
+\Drupal::database()->query($query, $args, $options);
```

<br>

## `DBSelectRector`

- class: [`DrupalRector\Rector\Deprecation\DBSelectRector`](/../master/drupal-rector/src/Rector/Deprecation/DBSelectRector.php)

Fixes deprecated db_select() calls

```diff
-db_select($table, $alias, $options);
+\Drupal::database()->select($table, $alias, $options);
```

<br>

## `DBUpdateRector`

- class: [`DrupalRector\Rector\Deprecation\DBUpdateRector`](/../master/drupal-rector/src/Rector/Deprecation/DBUpdateRector.php)

Fixes deprecated db_update() calls

```diff
-db_update($table, $options);
+\Drupal::database()->update($table, $options);
```

<br>

## `DatetimeDateStorageFormatRector`

- class: [`DrupalRector\Rector\Deprecation\DatetimeDateStorageFormatRector`](/../master/drupal-rector/src/Rector/Deprecation/DatetimeDateStorageFormatRector.php)

Fixes deprecated DATETIME_DATE_STORAGE_FORMAT use

```diff
 use Drupal\Core\Datetime\DrupalDateTime;
+use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
 $date = new DrupalDateTime('now', new \DateTimezone('America/Los_Angeles'));
-$now = $date->format(DATETIME_DATE_STORAGE_FORMAT);
+$now = $date->format(DateTimeItemInterface::DATE_STORAGE_FORMAT);
```

<br>

## `DatetimeDatetimeStorageFormatRector`

- class: [`DrupalRector\Rector\Deprecation\DatetimeDatetimeStorageFormatRector`](/../master/drupal-rector/src/Rector/Deprecation/DatetimeDatetimeStorageFormatRector.php)

Fixes deprecated DATETIME_DATETIME_STORAGE_FORMAT use

```diff
 use Drupal\Core\Datetime\DrupalDateTime;
+use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
 $date = new DrupalDateTime('now', new \DateTimezone('America/Los_Angeles'));
-$now = $date->format(DATETIME_DATETIME_STORAGE_FORMAT);
+$now = $date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
```

<br>

## `DatetimeStorageTimezoneRector`

- class: [`DrupalRector\Rector\Deprecation\DatetimeStorageTimezoneRector`](/../master/drupal-rector/src/Rector/Deprecation/DatetimeStorageTimezoneRector.php)

Fixes deprecated DATETIME_STORAGE_TIMEZONE use

```diff
-$timezone = new \DateTimeZone(DATETIME_STORAGE_TIMEZONE);
+use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
+$timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);
```

<br>

## `DrupalLRector`

- class: [`DrupalRector\Rector\Deprecation\DrupalLRector`](/../master/drupal-rector/src/Rector/Deprecation/DrupalLRector.php)

Fixes deprecated \Drupal::l() calls

```diff
-\Drupal::l('User Login', \Drupal\Core\Url::fromRoute('user.login'));
+\Drupal\Core\Link::fromTextAndUrl('User Login', \Drupal\Core\Url::fromRoute('user.login'));
```

<br>

## `DrupalRealpathRector`

- class: [`DrupalRector\Rector\Deprecation\DrupalRealpathRector`](/../master/drupal-rector/src/Rector/Deprecation/DrupalRealpathRector.php)

Fixes deprecated drupal_realpath() calls

```diff
-$path = drupal_realpath($path);
+$path = \Drupal::service('file_system')
+    ->realpath($path);
```

<br>

## `DrupalRenderRector`

- class: [`DrupalRector\Rector\Deprecation\DrupalRenderRector`](/../master/drupal-rector/src/Rector/Deprecation/DrupalRenderRector.php)

Fixes deprecated drupal_render() calls

```diff
-$result = drupal_render($elements);
+$result = \Drupal::service('renderer')->render($elements);
```

<br>

## `DrupalRenderRootRector`

- class: [`DrupalRector\Rector\Deprecation\DrupalRenderRootRector`](/../master/drupal-rector/src/Rector/Deprecation/DrupalRenderRootRector.php)

Fixes deprecated drupal_render_root() calls

```diff
-$result = drupal_render_root($elements);
+$result = \Drupal::service('renderer')->renderRoot($elements);
```

<br>

## `DrupalSetMessageRector`

- class: [`DrupalRector\Rector\Deprecation\DrupalSetMessageRector`](/../master/drupal-rector/src/Rector/Deprecation/DrupalSetMessageRector.php)

Fixes deprecated drupal_set_message() calls

```diff
-drupal_set_message('example status', 'status');
+\Drupal::messenger()->addStatus('example status');
```

<br>

## `DrupalURLRector`

- class: [`DrupalRector\Rector\Deprecation\DrupalURLRector`](/../master/drupal-rector/src/Rector/Deprecation/DrupalURLRector.php)

Fixes deprecated \Drupal::url() calls

```diff
-\Drupal::url('user.login');
+\Drupal\Core\Url::fromRoute('user.login')->toString();
```

<br>

## `EntityCreateRector`

- class: [`DrupalRector\Rector\Deprecation\EntityCreateRector`](/../master/drupal-rector/src/Rector/Deprecation/EntityCreateRector.php)

Fixes deprecated entity_create() calls

```diff
-entity_create('node', ['bundle' => 'page', 'title' => 'Hello world']);
+\Drupal::service('entity_type.manager)->getStorage('node')->create(['bundle' => 'page', 'title' => 'Hello world']);
```

<br>

## `EntityGetDisplayRector`

- class: [`DrupalRector\Rector\Deprecation\EntityGetDisplayRector`](/../master/drupal-rector/src/Rector/Deprecation/EntityGetDisplayRector.php)

Fixes deprecated entity_get_display() calls

```diff
-$display = entity_get_display($entity_type, $bundle, $view_mode)
+$display = \Drupal::service('entity_display.repository')
+    ->getViewDisplay($entity_type, $bundle, $view_mode);
```

<br>

## `EntityGetFormDisplayRector`

- class: [`DrupalRector\Rector\Deprecation\EntityGetFormDisplayRector`](/../master/drupal-rector/src/Rector/Deprecation/EntityGetFormDisplayRector.php)

Fixes deprecated entity_get_form_display() calls

```diff
-$display = entity_get_form_display($entity_type, $bundle, $form_mode)
+$display = \Drupal::service('entity_display.repository')
+    ->getFormDisplay($entity_type, $bundle, $form_mode);
```

<br>

## `EntityInterfaceLinkRector`

- class: [`DrupalRector\Rector\Deprecation\EntityInterfaceLinkRector`](/../master/drupal-rector/src/Rector/Deprecation/EntityInterfaceLinkRector.php)

Fixes deprecated link() calls

```diff
-$url = $entity->link();
+$url = $entity->toLink()->toString();
```

<br>

## `EntityInterfaceUrlInfoRector`

- class: [`DrupalRector\Rector\Deprecation\EntityInterfaceUrlInfoRector`](/../master/drupal-rector/src/Rector/Deprecation/EntityInterfaceUrlInfoRector.php)

Fixes deprecated urlInfo() calls

```diff
-$url = $entity->urlInfo();
+$url = $entity->toUrl();
```

<br>

## `EntityLoadRector`

- class: [`DrupalRector\Rector\Deprecation\EntityLoadRector`](/../master/drupal-rector/src/Rector/Deprecation/EntityLoadRector.php)

Fixes deprecated entity_load() use

```diff
-$node = entity_load('node', 123);
+$node = \Drupal::entityManager()->getStorage('node')->load(123);
```

<br>

## `EntityManagerRector`

- class: [`DrupalRector\Rector\Deprecation\EntityManagerRector`](/../master/drupal-rector/src/Rector/Deprecation/EntityManagerRector.php)

Fixes deprecated \Drupal::entityManager() calls

```diff
-$entity_manager = \Drupal::entityManager();
+$entity_manager = \Drupal::entityTypeManager();
```

<br>

## `FileCreateDirectoryRector`

- class: [`DrupalRector\Rector\Deprecation\FileCreateDirectoryRector`](/../master/drupal-rector/src/Rector/Deprecation/FileCreateDirectoryRector.php)

Fixes deprecated FILE_CREATE_DIRECTORY use

```diff
-$result = \Drupal::service('file_system')->prepareDirectory($directory, FILE_CREATE_DIRECTORY);
+$result = \Drupal::service('file_system')->prepareDirectory($directory, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);
```

<br>

## `FileDefaultSchemeRector`

- class: [`DrupalRector\Rector\Deprecation\FileDefaultSchemeRector`](/../master/drupal-rector/src/Rector/Deprecation/FileDefaultSchemeRector.php)

Fixes deprecated file_default_scheme calls

```diff
-$file_default_scheme = file_default_scheme();
+$file_default_scheme = \Drupal::config('system.file')->get('default_scheme');
```

<br>

## `FileDirectoryOsTempRector`

- class: [`DrupalRector\Rector\Deprecation\FileDirectoryOsTempRector`](/../master/drupal-rector/src/Rector/Deprecation/FileDirectoryOsTempRector.php)

Fixes deprecated file_directory_temp() calls

```diff
-$dir = file_directory_os_temp();
+$dir = \Drupal\Component\FileSystem\FileSystem::getOsTemporaryDirectory();
```

<br>

## `FileDirectoryTempRector`

- class: [`DrupalRector\Rector\Deprecation\FileDirectoryTempRector`](/../master/drupal-rector/src/Rector/Deprecation/FileDirectoryTempRector.php)

Fixes deprecated file_directory_temp() calls

```diff
-$dir = file_directory_temp();
+$dir = \Drupal::service('file_system')->getTempDirectory();
```

<br>

## `FileExistsRenameRector`

- class: [`DrupalRector\Rector\Deprecation\FileExistsRenameRector`](/../master/drupal-rector/src/Rector/Deprecation/FileExistsRenameRector.php)

Fixes deprecated FILE_EXISTS_RENAME use

```diff
-$result = file_unmanaged_copy($source, $destination, FILE_EXISTS_RENAME);
+$result = file_unmanaged_copy($source, $destination, \Drupal\Core\File\FileSystemInterface::EXISTS_RENAME);
```

<br>

## `FileExistsReplaceRector`

- class: [`DrupalRector\Rector\Deprecation\FileExistsReplaceRector`](/../master/drupal-rector/src/Rector/Deprecation/FileExistsReplaceRector.php)

Fixes deprecated FILE_EXISTS_REPLACE use

```diff
-$result = file_copy($file, $dest, FILE_EXISTS_REPLACE);
+$result = file_copy($file, $dest, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE);
```

<br>

## `FileLoadRector`

- class: [`DrupalRector\Rector\Deprecation\FileLoadRector`](/../master/drupal-rector/src/Rector/Deprecation/FileLoadRector.php)

Fixes deprecated file_load() use

```diff
-$file = file_load(123);
+$file = \Drupal::entityManager()->getStorage('file')->load(123);
```

<br>

## `FileModifyPermissionsRector`

- class: [`DrupalRector\Rector\Deprecation\FileModifyPermissionsRector`](/../master/drupal-rector/src/Rector/Deprecation/FileModifyPermissionsRector.php)

Fixes deprecated FILE_MODIFY_PERMISSIONS use

```diff
-$result = file_prepare_directory($destination, FILE_MODIFY_PERMISSIONS);
+$result = file_prepare_directory($destination, \Drupal\Core\File\FileSystemInterface::MODIFY_PERMISSIONS);
```

<br>

## `FilePrepareDirectoryRector`

- class: [`DrupalRector\Rector\Deprecation\FilePrepareDirectoryRector`](/../master/drupal-rector/src/Rector/Deprecation/FilePrepareDirectoryRector.php)

Fixes deprecated file_prepare_directory() calls

```diff
-$result = file_prepare_directory($directory, $options);
+$result = \Drupal::service('file_system')->prepareDirectory($directory, $options);
```

<br>

## `FileScanDirectoryRector`

- class: [`DrupalRector\Rector\Deprecation\FileScanDirectoryRector`](/../master/drupal-rector/src/Rector/Deprecation/FileScanDirectoryRector.php)

Fixes deprecated file_scan_directory() calls

```diff
-$files = file_scan_directory($directory);
+$files = \Drupal::service('file_system')->scanDirectory($directory);
```

<br>

## `FileUnmanagedSaveDataRector`

- class: [`DrupalRector\Rector\Deprecation\FileUnmanagedSaveDataRector`](/../master/drupal-rector/src/Rector/Deprecation/FileUnmanagedSaveDataRector.php)

Fixes deprecated file_unmanaged_save_data() calls

```diff
-$result = file_unmanaged_save_data($data, $destination, $replace);
+$result = \Drupal::service('file_system')->saveData($data, $destination, $replace);
```

<br>

## `FileUriTargetRector`

- class: [`DrupalRector\Rector\Deprecation\FileUriTargetRector`](/../master/drupal-rector/src/Rector/Deprecation/FileUriTargetRector.php)

Fixes deprecated file_uri_target() calls

```diff
-$result = file_uri_target($uri)
+$result = \Drupal::service('stream_wrapper_manager')->getTarget($uri);
```

<br>

## `FormatDateRector`

- class: [`DrupalRector\Rector\Deprecation\FormatDateRector`](/../master/drupal-rector/src/Rector/Deprecation/FormatDateRector.php)

Fixes deprecated format_date() calls

```diff
-$date = format_date($timestamp, $type, $format, $timezone, $langcode);
+$date = \Drupal::service('date.formatter')->format($timestamp, $type, $format, $timezone, $langcode);
```

<br>

## `KernelTestBaseGetMockRector`

- class: [`DrupalRector\Rector\Deprecation\KernelTestBaseGetMockRector`](/../master/drupal-rector/src/Rector/Deprecation/KernelTestBaseGetMockRector.php)

Fixes deprecated getMock() calls

```diff
-$this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
+$this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
```

<br>

## `LinkGeneratorTraitLRector`

- class: [`DrupalRector\Rector\Deprecation\LinkGeneratorTraitLRector`](/../master/drupal-rector/src/Rector/Deprecation/LinkGeneratorTraitLRector.php)

Fixes deprecated l() calls

```diff
-$this->l($text, $url);
+\Drupal\Core\Link::fromTextAndUrl($text, $url);
```

<br>

## `NodeLoadRector`

- class: [`DrupalRector\Rector\Deprecation\NodeLoadRector`](/../master/drupal-rector/src/Rector/Deprecation/NodeLoadRector.php)

Fixes deprecated node_load() use

```diff
-$node = node_load(123);
+$node = \Drupal::entityManager()->getStorage('node')->load(123);
```

<br>

## `PathAliasManagerServiceNameRector`

- class: [`DrupalRector\Rector\Deprecation\PathAliasManagerServiceNameRector`](/../master/drupal-rector/src/Rector/Deprecation/PathAliasManagerServiceNameRector.php)

Renames the IDs in Drupal::service() calls

```diff
-\Drupal::service('old')->foo();
+\Drupal::service('bar')->foo();
```

<br>

## `PathAliasRepositoryRector`

- class: [`DrupalRector\Rector\Deprecation\PathAliasRepositoryRector`](/../master/drupal-rector/src/Rector/Deprecation/PathAliasRepositoryRector.php)

Renames the IDs in Drupal::service() calls

```diff
-\Drupal::service('old')->foo();
+\Drupal::service('bar')->foo();
```

<br>

## `PathAliasWhitelistServiceNameRector`

- class: [`DrupalRector\Rector\Deprecation\PathAliasWhitelistServiceNameRector`](/../master/drupal-rector/src/Rector/Deprecation/PathAliasWhitelistServiceNameRector.php)

Renames the IDs in Drupal::service() calls

```diff
-\Drupal::service('old')->foo();
+\Drupal::service('bar')->foo();
```

<br>

## `PathProcessorAliasServiceNameRector`

- class: [`DrupalRector\Rector\Deprecation\PathProcessorAliasServiceNameRector`](/../master/drupal-rector/src/Rector/Deprecation/PathProcessorAliasServiceNameRector.php)

Renames the IDs in Drupal::service() calls

```diff
-\Drupal::service('old')->foo();
+\Drupal::service('bar')->foo();
```

<br>

## `PathSubscriberServiceNameRector`

- class: [`DrupalRector\Rector\Deprecation\PathSubscriberServiceNameRector`](/../master/drupal-rector/src/Rector/Deprecation/PathSubscriberServiceNameRector.php)

Renames the IDs in Drupal::service() calls

```diff
-\Drupal::service('old')->foo();
+\Drupal::service('bar')->foo();
```

<br>

## `RequestTimeConstRector`

- class: [`DrupalRector\Rector\Deprecation\RequestTimeConstRector`](/../master/drupal-rector/src/Rector/Deprecation/RequestTimeConstRector.php)

Fixes deprecated REQUEST_TIME calls

```diff
-$request_time = REQUEST_TIME;
+$request_time = \Drupal::time()->getRequestTime();
```

<br>

## `SafeMarkupFormatRector`

- class: [`DrupalRector\Rector\Deprecation\SafeMarkupFormatRector`](/../master/drupal-rector/src/Rector/Deprecation/SafeMarkupFormatRector.php)

Fixes deprecated SafeMarkup::format() calls

```diff
-$safe_string_markup_object = \Drupal\Component\Utility\SafeMarkup::format('hello world');
+$safe_string_markup_object = new \Drupal\Component\Render\FormattableMarkup('hello world');
```

<br>

## `UnicodeStrlenRector`

- class: [`DrupalRector\Rector\Deprecation\UnicodeStrlenRector`](/../master/drupal-rector/src/Rector/Deprecation/UnicodeStrlenRector.php)

Fixes deprecated \Drupal\Component\Utility\Unicode::strlen() calls

```diff
-$length = \Drupal\Component\Utility\Unicode::strlen('example');
+$length = mb_strlen('example');
```

<br>

## `UnicodeStrtolowerRector`

- class: [`DrupalRector\Rector\Deprecation\UnicodeStrtolowerRector`](/../master/drupal-rector/src/Rector/Deprecation/UnicodeStrtolowerRector.php)

Fixes deprecated \Drupal\Component\Utility\Unicode::strtolower() calls

```diff
-$string = \Drupal\Component\Utility\Unicode::strtolower('example');
+$string = mb_strtolower('example');
```

<br>

## `UnicodeSubstrRector`

- class: [`DrupalRector\Rector\Deprecation\UnicodeSubstrRector`](/../master/drupal-rector/src/Rector/Deprecation/UnicodeSubstrRector.php)

Fixes deprecated \Drupal\Component\Utility\Unicode::substr() calls

```diff
-$string = \Drupal\Component\Utility\Unicode::substr('example', 0, 2);
+$string = mb_substr('example', 0, 2);
```

<br>

## `UnitTestCaseGetMockRector`

- class: [`DrupalRector\Rector\Deprecation\UnitTestCaseGetMockRector`](/../master/drupal-rector/src/Rector/Deprecation/UnitTestCaseGetMockRector.php)

Fixes deprecated getMock() calls

```diff
-$this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
+$this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
```

<br>

## `UserLoadRector`

- class: [`DrupalRector\Rector\Deprecation\UserLoadRector`](/../master/drupal-rector/src/Rector/Deprecation/UserLoadRector.php)

Fixes deprecated user_load() use

```diff
-$user = user_load(123);
+$user = \Drupal::entityManager()->getStorage('user')->load(123);
```

<br>

