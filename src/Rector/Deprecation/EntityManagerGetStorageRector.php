<?php

  namespace DrupalRector\Rector\Deprecation;

  use DrupalRector\Rector\Deprecation\Base\MethodToMethodBase;
  use Rector\Core\RectorDefinition\CodeSample;
  use Rector\Core\RectorDefinition\RectorDefinition;

  /**
   * Replaces EntityManagerInterface::getStorage() with EntityTypeManager::getStorage().
   *
   * See https://www.drupal.org/node/2549139 for change record.
   *
   * What is covered:
   * - Change the class the method belongs to from current interface.
   *
   */
  final class EntityManagerGetStorageRector extends MethodToMethodBase
  {



    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
      return new RectorDefinition('Move getStorage() to a new class from current interface.',[
        new CodeSample(
          <<<'CODE_BEFORE'

CODE_BEFORE
          ,
          <<<'CODE_AFTER'

CODE_AFTER
        )
      ]);
    }

  }