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

namespace Mmoreram\PHPFormatter\Tests\Finder;

use PHPUnit_Framework_TestCase;

use Mmoreram\PHPFormatter\Finder\ConfigFinder;

/**
 * Class ConfigFinderTest.
 */
class ConfigFinderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test right load of config file.
     */
    public function testFindConfigFileAndFound()
    {
        $path = dirname(__FILE__) . '/../Mocks/';
        $fileFinder = new ConfigFinder();
        $data = $fileFinder->findConfigFile($path);

        $this->assertEquals($data, [
            'use-sort' => [
                'group' => [
                    'Symfony',
                    '_main',
                    'Mmoreram',
                ],
                'group-type' => 'each',
                'sort-type' => 'alph',
                'sort-direction' => 'desc',
            ],
        ]);
    }
}
