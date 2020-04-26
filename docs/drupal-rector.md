# All 22 Rectors Overview

- [Rector](#rector)

## Rector

### `BrowserTestBaseGetMockRector`

- class: [`DrupalRector\Rector\Deprecation\BrowserTestBaseGetMockRector`](/../master/drupal-rector/src/Rector/Deprecation/BrowserTestBaseGetMockRector.php)

Fixes deprecated getMock() calls

```diff
-$this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
+$this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
```

<br>

### `DBInsertRector`

- class: [`DrupalRector\Rector\Deprecation\DBInsertRector`](/../master/drupal-rector/src/Rector/Deprecation/DBInsertRector.php)

Fixes deprecated db_insert() calls

```diff
-db_insert($table, $options);
+\Drupal::database()->insert($table, $options);
```

<br>

### `DBQueryRector`

- class: [`DrupalRector\Rector\Deprecation\DBQueryRector`](/../master/drupal-rector/src/Rector/Deprecation/DBQueryRector.php)

Fixes deprecated db_query() calls

```diff
-db_query($query, $args, $options);
+\Drupal::database()->query($query, $args, $options);
```

<br>

### `DBSelectRector`

- class: [`DrupalRector\Rector\Deprecation\DBSelectRector`](/../master/drupal-rector/src/Rector/Deprecation/DBSelectRector.php)

Fixes deprecated db_select() calls

```diff
-db_select($table, $alias, $options);
+\Drupal::database()->select($table, $alias, $options);
```

<br>

### `DrupalLRector`

- class: [`DrupalRector\Rector\Deprecation\DrupalLRector`](/../master/drupal-rector/src/Rector/Deprecation/DrupalLRector.php)

Fixes deprecated \Drupal::l() calls

```diff
-\Drupal::l('User Login', \Drupal::service('url_generator')->generateFromRoute('user.login'));
+\Drupal::service('link_generator')->generate('User Login', \Drupal::service('url_generator')->generateFromRoute('user.login'));
```

<br>

### `DrupalRenderRector`

- class: [`DrupalRector\Rector\Deprecation\DrupalRenderRector`](/../master/drupal-rector/src/Rector/Deprecation/DrupalRenderRector.php)

Fixes deprecated drupal_render() calls

```diff
-$result = drupal_render($elements);
+$result = \Drupal::service('renderer')->render($elements);
```

<br>

### `DrupalRenderRootRector`

- class: [`DrupalRector\Rector\Deprecation\DrupalRenderRootRector`](/../master/drupal-rector/src/Rector/Deprecation/DrupalRenderRootRector.php)

Fixes deprecated drupal_render_root() calls

```diff
-$result = drupal_render_root($elements);
+$result = \Drupal::service('renderer')->renderRoot($elements);
```

<br>

### `DrupalSetMessageRector`

- class: [`DrupalRector\Rector\Deprecation\DrupalSetMessageRector`](/../master/drupal-rector/src/Rector/Deprecation/DrupalSetMessageRector.php)

Fixes deprecated drupal_set_message() calls

```diff
-drupal_set_message('example status', 'status');
+\Drupal::messenger()->addStatus('example status');
```

<br>

### `DrupalURLRector`

- class: [`DrupalRector\Rector\Deprecation\DrupalURLRector`](/../master/drupal-rector/src/Rector/Deprecation/DrupalURLRector.php)

Fixes deprecated \Drupal::url() calls

```diff
-\Drupal::url('user.login');
+\Drupal::service('url_generator')->generateFromRoute('user.login');
```

<br>

### `EntityManagerRector`

- class: [`DrupalRector\Rector\Deprecation\EntityManagerRector`](/../master/drupal-rector/src/Rector/Deprecation/EntityManagerRector.php)

Fixes deprecated \Drupal::entityManager() calls

```diff
-$entity_manager = \Drupal::entityManager();
+$entity_manager = \Drupal::entityTypeManager();
```

<br>

### `FileCreateDirectoryRector`

- class: [`DrupalRector\Rector\Deprecation\FileCreateDirectoryRector`](/../master/drupal-rector/src/Rector/Deprecation/FileCreateDirectoryRector.php)

Fixes deprecated FILE_CREATE_DIRECTORY use

```diff
-$result = \Drupal::service('file_system')->prepareDirectory($directory, FILE_CREATE_DIRECTORY);
+$result = \Drupal::service('file_system')->prepareDirectory($directory, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);
```

<br>

### `FileExistsReplaceRector`

- class: [`DrupalRector\Rector\Deprecation\FileExistsReplaceRector`](/../master/drupal-rector/src/Rector/Deprecation/FileExistsReplaceRector.php)

Fixes deprecated FILE_EXISTS_REPLACE use

```diff
-$result = file_copy($file, $dest, FILE_EXISTS_REPLACE);
+$result = file_copy($file, $dest, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE);
```

<br>

### `FilePrepareDirectoryRector`

- class: [`DrupalRector\Rector\Deprecation\FilePrepareDirectoryRector`](/../master/drupal-rector/src/Rector/Deprecation/FilePrepareDirectoryRector.php)

Fixes deprecated file_prepare_directory() calls

```diff
-$result = file_prepare_directory($directory, $options);
+$result = \Drupal::service('file_system')->prepareDirectory($directory, $options);
```

<br>

### `FileUnmanagedSaveDataRector`

- class: [`DrupalRector\Rector\Deprecation\FileUnmanagedSaveDataRector`](/../master/drupal-rector/src/Rector/Deprecation/FileUnmanagedSaveDataRector.php)

Fixes deprecated file_unmanaged_save_data() calls

```diff
-$result = file_unmanaged_save_data($data, $destination, $replace);
+$result = \Drupal::service('file_system')->saveData($data, $destination, $replace);
```

<br>

### `FormatDateRector`

- class: [`DrupalRector\Rector\Deprecation\FormatDateRector`](/../master/drupal-rector/src/Rector/Deprecation/FormatDateRector.php)

Fixes deprecated format_date() calls

```diff
-$date = format_date($timestamp, $type, $format, $timezone, $langcode);
+$date = \Drupal::service('date.formatter')->format($timestamp, $type, $format, $timezone, $langcode);
```

<br>

### `KernelTestBaseGetMockRector`

- class: [`DrupalRector\Rector\Deprecation\KernelTestBaseGetMockRector`](/../master/drupal-rector/src/Rector/Deprecation/KernelTestBaseGetMockRector.php)

Fixes deprecated getMock() calls

```diff
-$this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
+$this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
```

<br>

### `PathAliasManagerServiceNameRector`

- class: [`DrupalRector\Rector\Deprecation\PathAliasManagerServiceNameRector`](/../master/drupal-rector/src/Rector/Deprecation/PathAliasManagerServiceNameRector.php)

Renames the IDs in Drupal::service() calls

```diff
-\Drupal::service('old')->foo();
+\Drupal::service('bar')->foo();
```

<br>

### `PathAliasRepositoryRector`

- class: [`DrupalRector\Rector\Deprecation\PathAliasRepositoryRector`](/../master/drupal-rector/src/Rector/Deprecation/PathAliasRepositoryRector.php)

Renames the IDs in Drupal::service() calls

```diff
-\Drupal::service('old')->foo();
+\Drupal::service('bar')->foo();
```

<br>

### `PathAliasWhitelistServiceNameRector`

- class: [`DrupalRector\Rector\Deprecation\PathAliasWhitelistServiceNameRector`](/../master/drupal-rector/src/Rector/Deprecation/PathAliasWhitelistServiceNameRector.php)

Renames the IDs in Drupal::service() calls

```diff
-\Drupal::service('old')->foo();
+\Drupal::service('bar')->foo();
```

<br>

### `PathProcessorAliasServiceNameRector`

- class: [`DrupalRector\Rector\Deprecation\PathProcessorAliasServiceNameRector`](/../master/drupal-rector/src/Rector/Deprecation/PathProcessorAliasServiceNameRector.php)

Renames the IDs in Drupal::service() calls

```diff
-\Drupal::service('old')->foo();
+\Drupal::service('bar')->foo();
```

<br>

### `PathSubscriberServiceNameRector`

- class: [`DrupalRector\Rector\Deprecation\PathSubscriberServiceNameRector`](/../master/drupal-rector/src/Rector/Deprecation/PathSubscriberServiceNameRector.php)

Renames the IDs in Drupal::service() calls

```diff
-\Drupal::service('old')->foo();
+\Drupal::service('bar')->foo();
```

<br>

### `UnitTestCaseGetMockRector`

- class: [`DrupalRector\Rector\Deprecation\UnitTestCaseGetMockRector`](/../master/drupal-rector/src/Rector/Deprecation/UnitTestCaseGetMockRector.php)

Fixes deprecated getMock() calls

```diff
-$this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
+$this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
```

<br>

