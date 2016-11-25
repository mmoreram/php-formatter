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

namespace Mmoreram\PHPFormatter\Tests\Fixer;

use PHPUnit_Framework_TestCase;

use Mmoreram\PHPFormatter\Fixer\HeaderFixer;

/**
 * Class EmptyHeaderFixerTest.
 */
class EmptyHeaderFixerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test fixer.
     */
    public function testFix()
    {
        $data = "<?php

namespace 'App';";

        $header = '';

        $fixedDataExpected =
"<?php

namespace 'App';";

        $headerFixer = new HeaderFixer($header);
        $fixedData = $headerFixer->fix($data);
        $fixedData = $headerFixer->fix($fixedData);

        $this->assertEquals($fixedDataExpected, $fixedData);
    }
}
