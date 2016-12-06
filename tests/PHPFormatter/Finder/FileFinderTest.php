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

use Mmoreram\PHPFormatter\Finder\FileFinder;

/**
 * Class FileFinderTest.
 */
class FileFinderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test php file finder.
     */
    public function testFindPHPFilesByPath()
    {
        $path = dirname(__FILE__) . '/../Mocks/';
        $fileFinder = new FileFinder();
        $this->assertCount(3, $fileFinder->findPHPFilesByPath($path, []));
        $this->assertCount(2, $fileFinder->findPHPFilesByPath($path, ['directory']));
    }
}
