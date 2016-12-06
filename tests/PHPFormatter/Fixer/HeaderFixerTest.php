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

namespace Mmoreram\PHPFormatter\Tests\Fixer;

use PHPUnit_Framework_TestCase;

use Mmoreram\PHPFormatter\Fixer\HeaderFixer;

/**
 * Class HeaderFixerTest.
 */
class HeaderFixerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test fixer.
     *
     * @dataProvider dataFix
     */
    public function testFix($data)
    {
        $header =
'/**
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
';

        $fixedDataExpected =
'<?php

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

';

        $headerFixer = new HeaderFixer($header);
        $fixedData = $headerFixer->fix($data);

        $this->assertEquals($fixedDataExpected, $fixedData);
    }

    /**
     * Data for testFix.
     */
    public function dataFix()
    {
        return [
            ['<?php

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
 */'],
            ['<?php //comment

// This is my comment namespace hola

## Some other commends namespace jaja

/*
   * lalala
     * namespace
*/

/**
 * Another comment
 */

'],
            ['   <?php//Comment
// This is my comment namespace hola // #*/

## Some other commends namespace jaja */

/*
   * lalala
     * namespace //
*/

/**
 * Another comment
 */

'],
        ];
    }
}
