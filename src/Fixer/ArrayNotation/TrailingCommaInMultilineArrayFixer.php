<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\ArrayNotation;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @deprecated
 */
final class TrailingCommaInMultilineArrayFixer extends AbstractProxyFixer implements ConfigurableFixerInterface, DeprecatedFixerInterface
{
    /**
     * @var TrailingCommaInMultilineFixer
     */
    private $fixer;

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'PHP multi-line arrays should have a trailing comma.',
            [
                new CodeSample("<?php\narray(\n    1,\n    2\n);\n"),
                new VersionSpecificCodeSample(
                    <<<'SAMPLE'
<?php
    $x = [
        'foo',
        <<<EOD
            bar
            EOD
    ];

SAMPLE
                    ,
                    new VersionSpecification(70300),
                    ['after_heredoc' => true]
                ),
            ]
        );
    }

    public function configure(array $configuration): void
    {
        $configuration['elements'] = [TrailingCommaInMultilineFixer::ELEMENTS_ARRAYS];
        $this->getFixer()->configure($configuration);
        $this->configuration = $configuration;
    }

    public function getConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            $this->getFixer()->getConfigurationDefinition()->getOptions()[0],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessorsNames(): array
    {
        return array_keys($this->proxyFixers);
    }

    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers(): array
    {
        return [$this->getFixer()];
    }

    private function getFixer()
    {
        if (null === $this->fixer) {
            $this->fixer = new TrailingCommaInMultilineFixer();
        }

        return $this->fixer;
    }
}
