<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\MethodToMethodBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ClearCsrfTokenSeed extends MethodToMethodBase {

    protected $deprecatedMethodName = 'clearCsrfTokenSeed';

    protected $methodName = 'stampNew';

    protected $className = 'Drupal\Core\Session\MetadataBag';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated MetadataBag::clearCsrfTokenSeed() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
$metadata_bag = new \Drupal\Core\Session\MetadataBag(new \Drupal\Core\Site\Settings([]));
$metadata_bag->clearCsrfTokenSeed();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$metadata_bag = new \Drupal\Core\Session\MetadataBag(new \Drupal\Core\Site\Settings([]));
$metadata_bag->stampNew();
CODE_AFTER
            )
        ]);
    }

}
