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

namespace PhpCsFixer\Fixer\Variable;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PhpCsFixer\Utils;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class VariableCasingNotationFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @internal
     */
    public const CAMEL_CASE = 'camel_case';

    /**
     * @internal
     */
    public const SNAKE_CASE = 'snake_case';

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Enforce camel (or snake) case for variables, following configuration.',
            [
                new CodeSample(
                    '<?php
$foo_bar = 2;
'
                ),
                new CodeSample(
                    '<?php
$fooBar = 2;
',
                    ['case' => self::SNAKE_CASE]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     *
     */
    public function getPriority(): int
    {
        return 0;
    }

    public function isCandidate( Tokens $tokens ): bool
    {
        return $tokens->isTokenKindFound(T_VARIABLE);
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('case', 'Apply camel or snake case to variables'))
                ->setAllowedValues([self::CAMEL_CASE, self::SNAKE_CASE])
                ->setDefault(self::CAMEL_CASE)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token)
        {
            $variable = $tokens[$index + 1]->getContent();

            if (self::CAMEL_CASE === $this->configuration['case'])
            {
                $parts = explode('_', $variable);
                foreach($parts as $part)
                {
                    echo $part;
                    die();
                }
            }
        }
    }

    private function updateVariableCasing(string $functionName): string
    {
        $parts = explode('::', $functionName);

        $functionNamePart = array_pop($parts);

        if (self::CAMEL_CASE === $this->configuration['case']) {
            $newFunctionNamePart = $functionNamePart;
            $newFunctionNamePart = ucwords($newFunctionNamePart, '_');
            $newFunctionNamePart = str_replace('_', '', $newFunctionNamePart);
            $newFunctionNamePart = lcfirst($newFunctionNamePart);
        } else {
            $newFunctionNamePart = Utils::camelCaseToUnderscore($functionNamePart);
        }

        $parts[] = $newFunctionNamePart;

        return implode('::', $parts);
    }
}
