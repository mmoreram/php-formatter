<?php

/**
 * This file is part of the php-formatter package
 *
 * Copyright (c) 2014 Marc Morera
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Mmoreram\PHPFormatter\Tests\Loader;

use PHPUnit_Framework_TestCase;

use Mmoreram\PHPFormatter\Loader\ConfigLoader;
use Mmoreram\PHPFormatter\Command\UseSortCommand;

/**
 * Class ConfigLoaderTest
 */
class ConfigLoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     *
     * Default values
     */
    protected $defaultValues = array(
        'group' => array(
            'Symfony',
            '_main',
            'Mmoreram',
        ),
        'group-type' => 'each',
        'sort-type' => 'alph',
        'sort-direction' => 'desc',
    );

    /**
     * Test load config values
     *
     * @dataProvider dataLoadConfigValues
     */
    public function testLoadConfigValues(
        $configValues,
        $commandValues,
        $defaultValues,
        $usableValues
    )
    {
        $configLoader = new ConfigLoader();
        $this->assertEquals(
            $usableValues,
            $configLoader->loadConfigValues(
                UseSortCommand::COMMAND_NAME,
                $configValues,
                $commandValues,
                $defaultValues
            )
        );
    }

    /**
     * data for testLoadConfigValues
     *
     * @return array Values
     */
    public function dataLoadConfigValues()
    {
        return array(
            array(
                array(),
                array(),
                $this->defaultValues,
                $this->defaultValues,
            ),

            array(
                array(
                    'use-sort' => array(
                        'group' => array(
                            'Doctrine',
                            'Elcodi',
                        ),
                        'group-type' => 'one',
                    )
                ),
                array(),
                $this->defaultValues,
                array(
                    'group' => array(
                        'Doctrine',
                        'Elcodi',
                    ),
                    'group-type' => 'one',
                    'sort-type' => 'alph',
                    'sort-direction' => 'desc',
                ),
            ),

            array(
                array(
                    'use-sort' => array(
                        'group' => array(
                            'Doctrine',
                            'Elcodi',
                        ),
                        'group-type' => 'one',
                    )
                ),
                array(
                    'sort-type' => 'length',
                    'sort-direction' => 'asc',
                ),
                $this->defaultValues,
                array(
                    'group' => array(
                        'Doctrine',
                        'Elcodi',
                    ),
                    'group-type' => 'one',
                    'sort-type' => 'length',
                    'sort-direction' => 'asc',
                ),
            ),

            array(
                array(),
                array(
                    'sort-type' => 'length',
                    'sort-direction' => 'asc',
                ),
                $this->defaultValues,
                array(
                    'group' => array(
                        'Symfony',
                        '_main',
                        'Mmoreram',
                    ),
                    'group-type' => 'each',
                    'sort-type' => 'length',
                    'sort-direction' => 'asc',
                ),
            ),
        );
    }
}
