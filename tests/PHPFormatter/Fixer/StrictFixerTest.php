<?php
/**
 * File header placeholder.
 */

namespace Mmoreram\PHPFormatter\Tests\Fixer;

use Mmoreram\PHPFormatter\Fixer\StrictFixer;
use PHPUnit_Framework_TestCase;

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
    public function testFix($data, $withHeader, $strict)
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

        $this->assertEquals($fixedDataExpected, $fixedData);
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

namespace 'namespace';", true, true, ],
            ["<?php

/**
 * Header
 */

namespace 'namespace';", true, true, ],
            ["<?php namespace 'namespace';", false, true, ],
            ["<?php
     namespace 'namespace';", false, true, ],
            ["<?php
namespace 'namespace';", false, true, ],
            ["<?php
     declare(strict_types= 1);
namespace 'namespace';", false, true, ],
            ["<?php
     declare(strict_types=0);
namespace 'namespace';", false, true, ],
            ["<?php
declare(strict_types=0);
namespace 'namespace';", false, true, ],
            ["<?php declare(strict_types=0); namespace 'namespace';", false, true, ],
            ["<?php
namespace 'namespace';", false, false, ],
            ["<?php
     declare(strict_types= 1);
namespace 'namespace';", false, false, ],
            ["<?php
     declare(strict_types=0);
namespace 'namespace';", false, false, ],
            ["<?php

/**
 * Header
 */

declare(strict_types=1);

namespace 'namespace';", true, '~', ],
            ["<?php

/**
 * Header
 */

namespace 'namespace';", true, '~', ],
            ["<?php namespace 'namespace';", false, '~', ],
            ["<?php
     namespace 'namespace';", false, '~', ],
            ["<?php
namespace 'namespace';", false, '~', ],
            ["<?php
     declare(strict_types= 1);
namespace 'namespace';", false, '~', ],
            ["<?php
     declare(strict_types=0);
namespace 'namespace';", false, '~', ],
            ["<?php
declare(strict_types=0);
namespace 'namespace';", false, '~', ],
            ["<?php declare(strict_types=0); namespace 'namespace';", false, '~', ],
        ];
    }
}
