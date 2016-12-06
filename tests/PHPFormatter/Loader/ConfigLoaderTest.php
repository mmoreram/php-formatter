<?php

/*
 * This file is part of the php-formatter package
 *
 * Copyright (c) >=2014 Marc Morera
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Mmoreram\PHPFormatter\Tests\Loader;

use PHPUnit_Framework_TestCase;

use Mmoreram\PHPFormatter\Loader\ConfigLoader;

/**
 * Class ConfigLoaderTest.
 */
class ConfigLoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     *
     * Default values
     */
    protected $defaultValues = [
        'group' => [
            'Symfony',
            '_main',
            'Mmoreram',
        ],
        'group-type' => 'each',
        'sort-type' => 'alph',
        'sort-direction' => 'desc',
    ];

    /**
     * Test load config values.
     *
     * @dataProvider dataLoadConfigValues
     */
    public function testLoadConfigValues(
        $configValues,
        $commandValues,
        $defaultValues,
        $usableValues
    ) {
        $configLoader = new ConfigLoader();
        $this->assertEquals(
            $usableValues,
            $configLoader->loadConfigValues(
                'use-sort',
                $configValues,
                $commandValues,
                $defaultValues
            )
        );
    }

    /**
     * data for testLoadConfigValues.
     *
     * @return array Values
     */
    public function dataLoadConfigValues()
    {
        return [
            [
                [],
                [],
                $this->defaultValues,
                $this->defaultValues,
            ],

            [
                [
                    'use-sort' => [
                        'group' => [
                            'Doctrine',
                            'Elcodi',
                        ],
                        'group-type' => 'one',
                    ],
                ],
                [],
                $this->defaultValues,
                [
                    'group' => [
                        'Doctrine',
                        'Elcodi',
                    ],
                    'group-type' => 'one',
                    'sort-type' => 'alph',
                    'sort-direction' => 'desc',
                ],
            ],

            [
                [
                    'use-sort' => [
                        'group' => [
                            'Doctrine',
                            'Elcodi',
                        ],
                        'group-type' => 'one',
                    ],
                ],
                [
                    'sort-type' => 'length',
                    'sort-direction' => 'asc',
                ],
                $this->defaultValues,
                [
                    'group' => [
                        'Doctrine',
                        'Elcodi',
                    ],
                    'group-type' => 'one',
                    'sort-type' => 'length',
                    'sort-direction' => 'asc',
                ],
            ],

            [
                [],
                [
                    'sort-type' => 'length',
                    'sort-direction' => 'asc',
                ],
                $this->defaultValues,
                [
                    'group' => [
                        'Symfony',
                        '_main',
                        'Mmoreram',
                    ],
                    'group-type' => 'each',
                    'sort-type' => 'length',
                    'sort-direction' => 'asc',
                ],
            ],
        ];
    }
}
