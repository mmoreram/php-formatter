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

use Mmoreram\PHPFormatter\Fixer\StrictFixer;

/**
 * Class StrictFixerTest.
 */
class StrictFixerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test fixer.
     *
     * @dataProvider dataFix
     */
    public function testFix($data, $withHeader, $strict, $changes = true)
    {
        $fixedDataExpected =
'<?php' . ($withHeader ? '

/**
 * Header
 */' : '') . '

' . (is_bool($strict) ? 'declare(strict_types=' . ($strict ? '1' : '0') . ');

' : '') . "namespace 'namespace';";

        $strictFixer = new StrictFixer($strict);
        $fixedData = $strictFixer->fix($data);

        if ($changes) {
            $this->assertEquals($fixedDataExpected, $fixedData);
        } else {
            $this->assertFalse($fixedData);
        }
    }

    /**
     * Data for testFix.
     */
    public function dataFix()
    {
        return [
            ["<?php

/**
 * Header
 */

declare(strict_types=1);

namespace 'namespace';", true, true, false],
            ["<?php

/**
 * Header
 */

namespace 'namespace';", true, true],
            ["<?php namespace 'namespace';", false, true],
            ["<?php
     namespace 'namespace';", false, true],
            ["<?php
namespace 'namespace';", false, true],
            ["<?php
     declare(strict_types= 1);
namespace 'namespace';", false, true],
            ["<?php
     declare(strict_types=0);
namespace 'namespace';", false, true],
            ["<?php
declare(strict_types=0);
namespace 'namespace';", false, true],
            ["<?php declare(strict_types=0); namespace 'namespace';", false, true],
            ["<?php
namespace 'namespace';", false, false],
            ["<?php
     declare(strict_types= 1);
namespace 'namespace';", false, false],
            ["<?php
     declare(strict_types=0);
namespace 'namespace';", false, false],
            ["<?php

/**
 * Header
 */

declare(strict_types=1);

namespace 'namespace';", true, '~'],
            ["<?php

/**
 * Header
 */

namespace 'namespace';", true, '~', false],
            ["<?php namespace 'namespace';", false, '~'],
            ["<?php
     namespace 'namespace';", false, '~'],
            ["<?php
namespace 'namespace';", false, '~'],
            ["<?php
     declare(strict_types= 1);
namespace 'namespace';", false, '~'],
            ["<?php
     declare(strict_types=0);
namespace 'namespace';", false, '~'],
            ["<?php
declare(strict_types=0);
namespace 'namespace';", false, '~'],
            ["<?php declare(strict_types=0); namespace 'namespace';", false, '~'],
        ];
    }
}
