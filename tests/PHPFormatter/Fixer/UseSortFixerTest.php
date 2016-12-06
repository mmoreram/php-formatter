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

namespace Mmoreram\PHPFormatter\Tests\Sorter;

use PHPUnit_Framework_TestCase;

use Mmoreram\PHPFormatter\Fixer\UseSortFixer;

/**
 * Class UseSortFixerTest.
 */
class UseSortFixerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UseSortFixer
     *
     * Use sorter
     */
    protected $useSortFixer;

    /**
     * @var string
     *
     * Data
     */
    protected $data;

    /**
     * Setup.
     */
    public function setUp()
    {
        $this->useSortFixer = new UseSortFixer();
        $this->data = file_get_contents(
            dirname(__FILE__) . '/../Mocks/SimpleMock.php.mock'
        );
    }

    /**
     * Test sort.
     *
     * @dataProvider dataSort
     */
    public function testSort(
        $groups,
        $sortType,
        $sortDirection,
        $groupType,
        $result
    ) {
        $parsedData = $this
            ->useSortFixer
            ->setGroups($groups)
            ->setSortType($sortType)
            ->setSortDirection($sortDirection)
            ->setGroupType($groupType)
            ->fix($this->data);
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
     * Data for testSort.
     *
     * @return array Data
     */
    public function dataSort()
    {
        return [
            [
                [],
                UseSortFixer::SORT_TYPE_ALPHABETIC,
                UseSortFixer::SORT_DIRECTION_ASC,
                UseSortFixer::GROUP_TYPE_EACH,
'
use Test1\\Myclass1;
use Test1\\Myclass2;
use Test1\\MyFolder5\\File as MyFile;
use Test2\\Myclass3;
use Test2\\Myclass4;
use Test3\\File;
use Test3\\MyFolder\\Myclass;
use Test4\\Myclass3;
',
            ],
            [
                [],
                UseSortFixer::SORT_TYPE_ALPHABETIC,
                UseSortFixer::SORT_DIRECTION_ASC,
                UseSortFixer::GROUP_TYPE_ONE,
'
use Test1\\Myclass1,
    Test1\\Myclass2,
    Test1\\MyFolder5\\File as MyFile,
    Test2\\Myclass3,
    Test2\\Myclass4,
    Test3\\File,
    Test3\\MyFolder\\Myclass,
    Test4\\Myclass3;
',
            ],
            [
                [],
                UseSortFixer::SORT_TYPE_ALPHABETIC,
                UseSortFixer::SORT_DIRECTION_DESC,
                UseSortFixer::GROUP_TYPE_EACH,
'
use Test4\\Myclass3;
use Test3\\MyFolder\\Myclass;
use Test3\\File;
use Test2\\Myclass4;
use Test2\\Myclass3;
use Test1\\MyFolder5\\File as MyFile;
use Test1\\Myclass2;
use Test1\\Myclass1;
',
            ],
            [
                [],
                UseSortFixer::SORT_TYPE_ALPHABETIC,
                UseSortFixer::SORT_DIRECTION_DESC,
                UseSortFixer::GROUP_TYPE_ONE,
'
use Test4\\Myclass3,
    Test3\\MyFolder\\Myclass,
    Test3\\File,
    Test2\\Myclass4,
    Test2\\Myclass3,
    Test1\\MyFolder5\\File as MyFile,
    Test1\\Myclass2,
    Test1\\Myclass1;
',
            ],
            [
                [],
                UseSortFixer::SORT_TYPE_LENGTH,
                UseSortFixer::SORT_DIRECTION_DESC,
                UseSortFixer::GROUP_TYPE_EACH,
'
use Test1\\MyFolder5\\File as MyFile;
use Test3\\MyFolder\\Myclass;
use Test4\\Myclass3;
use Test2\\Myclass4;
use Test2\\Myclass3;
use Test1\\Myclass2;
use Test1\\Myclass1;
use Test3\\File;
',
            ],
            [
                [],
                UseSortFixer::SORT_TYPE_LENGTH,
                UseSortFixer::SORT_DIRECTION_DESC,
                UseSortFixer::GROUP_TYPE_ONE,
'
use Test1\\MyFolder5\\File as MyFile,
    Test3\\MyFolder\\Myclass,
    Test4\\Myclass3,
    Test2\\Myclass4,
    Test2\\Myclass3,
    Test1\\Myclass2,
    Test1\\Myclass1,
    Test3\\File;
',
            ],
            [
                [],
                UseSortFixer::SORT_TYPE_LENGTH,
                UseSortFixer::SORT_DIRECTION_ASC,
                UseSortFixer::GROUP_TYPE_EACH,
'
use Test3\\File;
use Test1\\Myclass1;
use Test1\\Myclass2;
use Test2\\Myclass3;
use Test2\\Myclass4;
use Test4\\Myclass3;
use Test3\\MyFolder\\Myclass;
use Test1\\MyFolder5\\File as MyFile;
',
            ],
            [
                [],
                UseSortFixer::SORT_TYPE_LENGTH,
                UseSortFixer::SORT_DIRECTION_ASC,
                UseSortFixer::GROUP_TYPE_ONE,
'
use Test3\\File,
    Test1\\Myclass1,
    Test1\\Myclass2,
    Test2\\Myclass3,
    Test2\\Myclass4,
    Test4\\Myclass3,
    Test3\\MyFolder\\Myclass,
    Test1\\MyFolder5\\File as MyFile;
',
            ],
            [
                ['_main'],
                UseSortFixer::SORT_TYPE_LENGTH,
                UseSortFixer::SORT_DIRECTION_ASC,
                UseSortFixer::GROUP_TYPE_ONE,
'
use Test3\\File,
    Test1\\Myclass1,
    Test1\\Myclass2,
    Test2\\Myclass3,
    Test2\\Myclass4,
    Test4\\Myclass3,
    Test3\\MyFolder\\Myclass,
    Test1\\MyFolder5\\File as MyFile;
',
            ],
            [
                ['_main', 'Test2'],
                UseSortFixer::SORT_TYPE_LENGTH,
                UseSortFixer::SORT_DIRECTION_ASC,
                UseSortFixer::GROUP_TYPE_ONE,
'
use Test3\\File,
    Test1\\Myclass1,
    Test1\\Myclass2,
    Test4\\Myclass3,
    Test3\\MyFolder\\Myclass,
    Test1\\MyFolder5\\File as MyFile;

use Test2\\Myclass3,
    Test2\\Myclass4;
',
            ],
            [
                ['Test2', '_main'],
                UseSortFixer::SORT_TYPE_LENGTH,
                UseSortFixer::SORT_DIRECTION_ASC,
                UseSortFixer::GROUP_TYPE_ONE,
'
use Test2\\Myclass3,
    Test2\\Myclass4;

use Test3\\File,
    Test1\\Myclass1,
    Test1\\Myclass2,
    Test4\\Myclass3,
    Test3\\MyFolder\\Myclass,
    Test1\\MyFolder5\\File as MyFile;
',
            ],
            [
                ['Test2', '_main', 'Test3'],
                UseSortFixer::SORT_TYPE_ALPHABETIC,
                UseSortFixer::SORT_DIRECTION_ASC,
                UseSortFixer::GROUP_TYPE_EACH,
'
use Test2\\Myclass3;
use Test2\\Myclass4;

use Test1\\Myclass1;
use Test1\\Myclass2;
use Test1\\MyFolder5\\File as MyFile;
use Test4\\Myclass3;

use Test3\\File;
use Test3\\MyFolder\\Myclass;
',
            ],
            [
                ['Test2', 'TestEmpty', '_main', 'Test3'],
                UseSortFixer::SORT_TYPE_ALPHABETIC,
                UseSortFixer::SORT_DIRECTION_ASC,
                UseSortFixer::GROUP_TYPE_EACH,
'
use Test2\\Myclass3;
use Test2\\Myclass4;

use Test1\\Myclass1;
use Test1\\Myclass2;
use Test1\\MyFolder5\\File as MyFile;
use Test4\\Myclass3;

use Test3\\File;
use Test3\\MyFolder\\Myclass;
',
            ],
            [
                ['Test2', ['Test1\MyFolder5', 'Test1'], '_main'],
                UseSortFixer::SORT_TYPE_ALPHABETIC,
                UseSortFixer::SORT_DIRECTION_ASC,
                UseSortFixer::GROUP_TYPE_EACH,
                '
use Test2\\Myclass3;
use Test2\\Myclass4;

use Test1\\MyFolder5\\File as MyFile;
use Test1\\Myclass1;
use Test1\\Myclass2;

use Test3\\File;
use Test3\\MyFolder\\Myclass;
use Test4\\Myclass3;
',
            ],
            [
                ['Test1', ['TestEmpty1', 'TestEmpty2'], '_main'],
                UseSortFixer::SORT_TYPE_ALPHABETIC,
                UseSortFixer::SORT_DIRECTION_ASC,
                UseSortFixer::GROUP_TYPE_EACH,
'
use Test1\\Myclass1;
use Test1\\Myclass2;
use Test1\\MyFolder5\\File as MyFile;

use Test2\\Myclass3;
use Test2\\Myclass4;
use Test3\\File;
use Test3\\MyFolder\\Myclass;
use Test4\\Myclass3;
',
            ],
        ];
    }

    /**
     * Test skip empty.
     */
    public function testGroupSkip()
    {
        $parsedData = $this
            ->useSortFixer
            ->setGroups(['Test2', 'TestEmpty', '_main', 'Test3'])
            ->setSortType(UseSortFixer::SORT_TYPE_ALPHABETIC)
            ->setSortDirection(UseSortFixer::SORT_DIRECTION_ASC)
            ->setGroupType(UseSortFixer::GROUP_TYPE_EACH)
            ->setGroupSkipEmpty(true)
            ->fix($this->data);

        $result =
            '<?php

/**
 * Copyright
 */

namespace PHPFormatter\\Tests\\Mocks;

use Test2\\Myclass3;
use Test2\\Myclass4;
use Test1\\Myclass1;
use Test1\\Myclass2;
use Test1\\MyFolder5\\File as MyFile;
use Test4\\Myclass3;
use Test3\\File;
use Test3\\MyFolder\\Myclass;

/**
 * Class SimpleMock
 */
class SimpleMock
{}';

        $this->assertEquals(
            $result,
            $parsedData
        );
    }

    /**
     * Test skip empty where consecutive empty groups are between used groups.
     *
     * @see https://github.com/mmoreram/php-formatter/issues/24
     */
    public function testGroupSkipWithMissingGroups()
    {
        $parsedData = $this
            ->useSortFixer
            ->setGroups(['Test1', 'TestEmpty1', ['TestEmpty2', 'TestEmpty3'], '_main'])
            ->setSortType(UseSortFixer::SORT_TYPE_ALPHABETIC)
            ->setSortDirection(UseSortFixer::SORT_DIRECTION_ASC)
            ->setGroupType(UseSortFixer::GROUP_TYPE_EACH)
            ->setGroupSkipEmpty(true)
            ->fix($this->data);

        $result =
            '<?php

/**
 * Copyright
 */

namespace PHPFormatter\\Tests\\Mocks;

use Test1\\Myclass1;
use Test1\\Myclass2;
use Test1\\MyFolder5\\File as MyFile;
use Test2\\Myclass3;
use Test2\\Myclass4;
use Test3\\File;
use Test3\\MyFolder\\Myclass;
use Test4\\Myclass3;

/**
 * Class SimpleMock
 */
class SimpleMock
{}';

        $this->assertEquals(
            $result,
            $parsedData
        );
    }

    /**
     * Test skip empty where consecutive non empty groups are between used groups.
     */
    public function testGroupNoSkipWithNotMissingGroups()
    {
        $parsedData = $this
            ->useSortFixer
            ->setGroups(['Test1', ['Test2', 'Test3'], 'Test4'])
            ->setSortType(UseSortFixer::SORT_TYPE_ALPHABETIC)
            ->setSortDirection(UseSortFixer::SORT_DIRECTION_ASC)
            ->setGroupType(UseSortFixer::GROUP_TYPE_EACH)
            ->setGroupSkipEmpty(false)
            ->fix($this->data);

        $result =
            '<?php

/**
 * Copyright
 */

namespace PHPFormatter\\Tests\\Mocks;

use Test1\\Myclass1;
use Test1\\Myclass2;
use Test1\\MyFolder5\\File as MyFile;

use Test2\\Myclass3;
use Test2\\Myclass4;
use Test3\\File;
use Test3\\MyFolder\\Myclass;

use Test4\\Myclass3;

/**
 * Class SimpleMock
 */
class SimpleMock
{}';

        $this->assertEquals(
            $result,
            $parsedData
        );
    }
}
