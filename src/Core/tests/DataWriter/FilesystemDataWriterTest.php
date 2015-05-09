<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\DataSource;

use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\DataWriter\FilesystemDataWriter;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemDataWriterTest extends \PHPUnit_Framework_TestCase
{
    protected $tmpDir;

    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir().'/spress-tests';
    }

    public function tearDown()
    {
        $fs = new FileSystem();
        $fs->remove($this->tmpDir);
    }

    public function testWriteItem()
    {
        $item = new Item('Test content', 'my-id');
        $item->setPath('index.html');

        $dw = new FilesystemDataWriter(new Filesystem(), $this->tmpDir);
        $dw->write($item);

        $this->assertFileExists($this->tmpDir.'/index.html');
    }

    public function testCleanUp()
    {
        $fs = new FileSystem();
        $fs->dumpFile($this->tmpDir.'/dummy-file', 'Dummy content.');

        $dw = new FilesystemDataWriter(new Filesystem(), $this->tmpDir);
        $dw->setUp();

        $this->assertFileNotExists($this->tmpDir.'/dummy-file');
    }
}
