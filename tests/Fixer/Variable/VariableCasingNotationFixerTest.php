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

namespace PhpCsFixer\Tests\Fixer\Variable;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Marc Aubé
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Variable\VariableCasingNotationFixer
 */
final class VariableCasingNotationFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfigMissingKey(): void
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[variable_casing_notation\] Invalid configuration: The option "a" does not exist\. Defined options are: "case"\.$#');

        $this->fixer->configure(['a' => 1]);
    }

    public function testInvalidConfigValue(): void
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[variable_casing_notation\] Invalid configuration: The option "case" with value "double" is invalid\. Accepted values are: "snake_case", "camel_case"\.$#');

        $this->fixer->configure(['case' => 'double']);
    }

    /**
     * @dataProvider provideFixCases
     */
    public function testDefaultFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideSnakeCaseFixCases
     */
    public function testSnakeCaseFix(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['case' => 'snake_case']);
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            [
                '<?php $foo_bar = null;',
                '<?php $fooBar = null;'
            ],
            [
                '<?php $foo_bar_baz = null;',
                '<?php $fooBarBaz = null;'
            ],
            [
                '<?php $foo_bar_1 = null;',
                '<?php $fooBar1 = null;'
            ],
            [
                '<?php $$foo_bar = null;',
                '<?php $$fooBar = null;'
            ],
            [
                '<?php
function foo($bar_baz, $foo_baz)
{
    // function body
}',
                '<?php
function foo( $barBaz, $fooBaz )
{
    // function body
}'
            ],
            [
                '<?php $foo = "$bar_baz";',
                '<?php $foo = "$bar_baz";'
            ]
        ];
    }

    public function provideSnakeCaseFixCases(): array
    {
        return [
            [
                '<?php $fooBar = null;',
                '<?php $foo_bar = null;'
            ],
            [
                '<?php $fooBarBaz = null;',
                '<?php $foo_bar_baz = null;'
            ],
            [
                '<?php $fooBar1 = null;',
                '<?php $foo_bar_1 = null;'
            ],
            [
                '<?php $$fooBar = null;',
                '<?php $$foo_bar = null;'
            ],
            [
                '<?php
function foo($barBaz, $fooBaz)
{
    // function body
}',
                '<?php
function foo( $bar_baz, $foo_baz )
{
    // function body
}'
            ],
            [
                '<?php $foo = "$barBaz";',
                '<?php $foo = "$barBaz";'
            ]
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     * @requires PHP 8.0
     */
    public function testDefaultFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix80Cases(): \Generator
    {
        yield [
            '<?php $foo_bar = null;',
            '<?php $fooBar = null;',
        ];
    }

    /**
     * @dataProvider provideSnakeCaseFix80Cases
     * @requires PHP 8.0
     */
    public function testSnakeCaseFix80(string $expected, string $input): void
    {
        $this->fixer->configure(['case' => 'snake_case']);
        $this->doTest($expected, $input);
    }

    public function provideSnakeCaseFix80Cases(): \Generator
    {
        yield [
            '<?php $fooBar = null;',
            '<?php $foo_bar = null;',
        ];
    }
}
