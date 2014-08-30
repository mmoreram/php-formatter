<?php

/**
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author Aldo Chiecchia <zimage@tiscali.it>
 */

namespace Mmoreram\PHPFormatter\Tests\Fixer;

use Mmoreram\PHPFormatter\Fixer\HeaderFixer;
use PHPUnit_Framework_TestCase;

/**
 * Class HeaderFixerTest
 */
class HeaderFixerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test fixer
     *
     * @dataProvider dataFix
     */
    public function testFix($data)
    {
        $header =
"/**
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
";

        $fixedDataExpected =
"<?php

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

";

        $headerFixer =  new HeaderFixer($header);
        $fixedData = $headerFixer->fix($data);

        $this->assertEquals($fixedDataExpected, $fixedData);
    }

    /**
     * Data for testFix
     */
    public function dataFix()
    {
        return array(
            array("<?php

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
 */"        ),
            array("<?php //comment

// This is my comment namespace hola

## Some other commends namespace jaja

/*
   * lalala
     * namespace
*/

/**
 * Another comment
 */

"           ),
            array("   <?php//Comment
// This is my comment namespace hola // #*/

## Some other commends namespace jaja */

/*
   * lalala
     * namespace //
*/

/**
 * Another comment
 */

"           ),
        );
    }
}
