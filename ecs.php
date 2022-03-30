<?php

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('vendor/sylius-labs/coding-standard/ecs.php');

    $params = $containerConfigurator->parameters();

    $params->set(Option::PATHS, [
        'src',
        'tests/Behat',
        'tests/Integration',
        'tests/Stub',
    ]);

    $containerConfigurator->parameters()->set(Option::SKIP, [
        VisibilityRequiredFixer::class => ['*Spec.php'],
    ]);
};
