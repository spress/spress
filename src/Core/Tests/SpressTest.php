<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\DataWriter\MemoryDataWriter;
use Yosymfony\Spress\Core\Spress;

class SpressTest extends TestCase
{
    public function testParseSite()
    {
        $dw = new MemoryDataWriter();

        $spress = new Spress();
        $spress['spress.config.site_dir'] = __dir__.'/fixtures/project';
        $spress['spress.dataWriter'] = $dw;
        $spress->parse();

        $this->assertCount(17, $dw->getItems());

        $this->assertTrue($dw->hasItem('about/index.html'));
        $this->assertTrue($dw->hasItem('pages/index.html'));
        $this->assertTrue($dw->hasItem('pages/page2/index.html'));

        $this->assertContains('<!DOCTYPE HTML>', $dw->getItem('about/index.html')->getContent());
        $this->assertContains('<!DOCTYPE HTML>', $dw->getItem('pages/index.html')->getContent());
        $this->assertContains('<!DOCTYPE HTML>', $dw->getItem('pages/page2/index.html')->getContent());
    }

    public function testParseWithDrafts()
    {
        $dw = new MemoryDataWriter();

        $spress = new Spress();
        $spress['spress.config.site_dir'] = __dir__.'/fixtures/project';
        $spress['spress.config.drafts'] = true;
        $spress['spress.dataWriter'] = $dw;
        $spress->parse();

        $this->assertCount(19, $dw->getItems());

        $this->assertTrue($dw->hasItem('books/2013/09/19/new-book/index.html'));

        $this->assertContains('<!DOCTYPE HTML>', $dw->getItem('books/2013/09/19/new-book/index.html')->getContent());
    }

    public function testParseSafe()
    {
        $dw = new MemoryDataWriter();

        $spress = new Spress();
        $spress['spress.config.site_dir'] = __dir__.'/fixtures/project';
        $spress['spress.config.safe'] = true;
        $spress['spress.dataWriter'] = $dw;
        $spress->parse();

        $this->assertCount(16, $dw->getItems());
    }

    public function testReParseSite()
    {
        $dw = new MemoryDataWriter();

        $spress = new Spress();
        $spress['spress.config.site_dir'] = __dir__.'/fixtures/project';
        $spress['spress.dataWriter'] = $dw;

        $spress->parse();

        $this->assertCount(17, $dw->getItems());

        $spress->parse();

        $this->assertCount(17, $dw->getItems());
    }
}
