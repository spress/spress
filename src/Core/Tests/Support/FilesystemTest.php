<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\Support;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\Support\Filesystem;

class FilesystemTest extends TestCase
{
    private $fs;
    private $tmpDir;

    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir().'/spress-tests';
        $this->fs = new Filesystem();
    }

    public function tearDown()
    {
        $this->fs->remove($this->tmpDir);
    }

    public function testReadFileMustReturnTheContentOfAFile()
    {
        $filename = $this->tmpDir.'/testReadFile.txt';
        $this->fs->dumpFile($filename, 'ACME');

        $this->assertEquals('ACME', $this->fs->readFile($filename));
    }
}
