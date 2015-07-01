<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests;

use Yosymfony\Spress\Core\Spress;

class SpressTest extends \PHPUnit_Framework_TestCase
{
    public function testParseSite()
    {
        $spress = new Spress();
        $spress['spress.config.site_dir'] = __dir__.'/fixtures/project';
        $spress->parse();
    }
}
