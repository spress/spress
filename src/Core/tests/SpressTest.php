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

use Yosymfony\Spress\Core\DataWriter\MemoryDataWriter;
use Yosymfony\Spress\Core\Spress;

class SpressTest extends \PHPUnit_Framework_TestCase
{
    public function testParseSite()
    {
        $dw = new MemoryDataWriter();

        $spress = new Spress();
        $spress['spress.config.site_dir'] = __dir__.'/fixtures/project';
        $spress['spress.dataWriter'] = $dw;
        $spress->parse();

        $this->assertCount(14, $dw->getItems());

        $this->assertTrue($dw->hasItem('about/index.html'));
        $this->assertTrue($dw->hasItem('pages/index.html'));
        $this->assertTrue($dw->hasItem('pages/page2/index.html'));

        $this->assertContains('<!DOCTYPE HTML>', $dw->getItem('about/index.html')->getContent());
        $this->assertContains('<!DOCTYPE HTML>', $dw->getItem('pages/index.html')->getContent());
        $this->assertContains('<!DOCTYPE HTML>', $dw->getItem('pages/page2/index.html')->getContent());
    }
}
