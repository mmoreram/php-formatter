<?php

/*
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

namespace Mmoreram\PHPFormatter\Tests\Sorter;

use PHPUnit_Framework_TestCase;

use Mmoreram\PHPFormatter\Sorter\UseSorter;

/**
 * Class UseSorterTest
 */
class UseSorterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UseSorter
     *
     * Use sorter
     */
    protected $useSorter;

    /**
     * @var string
     *
     * Data
     */
    protected $data;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->useSorter = new UseSorter();
        $this->data = file_get_contents(
            dirname(__FILE__) . '/../Mocks/SimpleMock.php.mock'
        );

    }

    /**
     * Test sort
     *
     * @dataProvider dataSort
     */
    public function testSort(
        $groups,
        $sortType,
        $sortDirection,
        $groupType,
        $result
    )
    {
        $parsedData = $this
            ->useSorter
            ->setGroups($groups)
            ->setSortType($sortType)
            ->setSortDirection($sortDirection)
            ->setGroupType($groupType)
            ->sort($this->data);
        $realResult =
"<?php

/**
 * Copyright
 */

namespace PHPFormatter\\Tests\\Mocks;
$result
/**
 * Class SimpleMock
 */
class SimpleMock
{}";

        $this->assertEquals(
            $realResult,
            $parsedData
        );
    }

    /**
     * Data for testSort
     *
     * @return array Data
     */
    public function dataSort()
    {
        return array(
            array(
                [],
                UseSorter::SORT_TYPE_ALPHABETIC,
                UseSorter::SORT_DIRECTION_ASC,
                UseSorter::GROUP_TYPE_EACH,
"
use Test1\\Myclass1;
use Test1\\Myclass2;
use Test1\\MyFolder5\\File as MyFile;
use Test2\\Myclass3;
use Test2\\Myclass4;
use Test3\\File;
use Test3\\MyFolder\\Myclass;
use Test4\\Myclass3;
"
            ),
            array(
                [],
                UseSorter::SORT_TYPE_ALPHABETIC,
                UseSorter::SORT_DIRECTION_ASC,
                UseSorter::GROUP_TYPE_ONE,
"
use Test1\\Myclass1,
    Test1\\Myclass2,
    Test1\\MyFolder5\\File as MyFile,
    Test2\\Myclass3,
    Test2\\Myclass4,
    Test3\\File,
    Test3\\MyFolder\\Myclass,
    Test4\\Myclass3;
"
            ),
            array(
                [],
                UseSorter::SORT_TYPE_ALPHABETIC,
                UseSorter::SORT_DIRECTION_DESC,
                UseSorter::GROUP_TYPE_EACH,
"
use Test4\\Myclass3;
use Test3\\MyFolder\\Myclass;
use Test3\\File;
use Test2\\Myclass4;
use Test2\\Myclass3;
use Test1\\MyFolder5\\File as MyFile;
use Test1\\Myclass2;
use Test1\\Myclass1;
"
            ),
            array(
                [],
                UseSorter::SORT_TYPE_ALPHABETIC,
                UseSorter::SORT_DIRECTION_DESC,
                UseSorter::GROUP_TYPE_ONE,
"
use Test4\\Myclass3,
    Test3\\MyFolder\\Myclass,
    Test3\\File,
    Test2\\Myclass4,
    Test2\\Myclass3,
    Test1\\MyFolder5\\File as MyFile,
    Test1\\Myclass2,
    Test1\\Myclass1;
"
            ),
            array(
                [],
                UseSorter::SORT_TYPE_LENGTH,
                UseSorter::SORT_DIRECTION_DESC,
                UseSorter::GROUP_TYPE_EACH,
"
use Test1\\MyFolder5\\File as MyFile;
use Test3\\MyFolder\\Myclass;
use Test4\\Myclass3;
use Test2\\Myclass4;
use Test2\\Myclass3;
use Test1\\Myclass2;
use Test1\\Myclass1;
use Test3\\File;
"
            ),
            array(
                [],
                UseSorter::SORT_TYPE_LENGTH,
                UseSorter::SORT_DIRECTION_DESC,
                UseSorter::GROUP_TYPE_ONE,
"
use Test1\\MyFolder5\\File as MyFile,
    Test3\\MyFolder\\Myclass,
    Test4\\Myclass3,
    Test2\\Myclass4,
    Test2\\Myclass3,
    Test1\\Myclass2,
    Test1\\Myclass1,
    Test3\\File;
"
            ),
            array(
                [],
                UseSorter::SORT_TYPE_LENGTH,
                UseSorter::SORT_DIRECTION_ASC,
                UseSorter::GROUP_TYPE_EACH,
"
use Test3\\File;
use Test1\\Myclass1;
use Test1\\Myclass2;
use Test2\\Myclass3;
use Test2\\Myclass4;
use Test4\\Myclass3;
use Test3\\MyFolder\\Myclass;
use Test1\\MyFolder5\\File as MyFile;
"
            ),
            array(
                [],
                UseSorter::SORT_TYPE_LENGTH,
                UseSorter::SORT_DIRECTION_ASC,
                UseSorter::GROUP_TYPE_ONE,
"
use Test3\\File,
    Test1\\Myclass1,
    Test1\\Myclass2,
    Test2\\Myclass3,
    Test2\\Myclass4,
    Test4\\Myclass3,
    Test3\\MyFolder\\Myclass,
    Test1\\MyFolder5\\File as MyFile;
"
            ),
            array(
                ['_main'],
                UseSorter::SORT_TYPE_LENGTH,
                UseSorter::SORT_DIRECTION_ASC,
                UseSorter::GROUP_TYPE_ONE,
"
use Test3\\File,
    Test1\\Myclass1,
    Test1\\Myclass2,
    Test2\\Myclass3,
    Test2\\Myclass4,
    Test4\\Myclass3,
    Test3\\MyFolder\\Myclass,
    Test1\\MyFolder5\\File as MyFile;
"
            ),
            array(
                ['_main', 'Test2'],
                UseSorter::SORT_TYPE_LENGTH,
                UseSorter::SORT_DIRECTION_ASC,
                UseSorter::GROUP_TYPE_ONE,
"
use Test3\\File,
    Test1\\Myclass1,
    Test1\\Myclass2,
    Test4\\Myclass3,
    Test3\\MyFolder\\Myclass,
    Test1\\MyFolder5\\File as MyFile;

use Test2\\Myclass3,
    Test2\\Myclass4;
"
            ),
            array(
                ['Test2', '_main'],
                UseSorter::SORT_TYPE_LENGTH,
                UseSorter::SORT_DIRECTION_ASC,
                UseSorter::GROUP_TYPE_ONE,
"
use Test2\\Myclass3,
    Test2\\Myclass4;

use Test3\\File,
    Test1\\Myclass1,
    Test1\\Myclass2,
    Test4\\Myclass3,
    Test3\\MyFolder\\Myclass,
    Test1\\MyFolder5\\File as MyFile;
"
            ),
            array(
                ['Test2', '_main', 'Test3'],
                UseSorter::SORT_TYPE_ALPHABETIC,
                UseSorter::SORT_DIRECTION_ASC,
                UseSorter::GROUP_TYPE_EACH,
"
use Test2\\Myclass3;
use Test2\\Myclass4;

use Test1\\Myclass1;
use Test1\\Myclass2;
use Test1\\MyFolder5\\File as MyFile;
use Test4\\Myclass3;

use Test3\\File;
use Test3\\MyFolder\\Myclass;
"
            )
        );
    }
}
