<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\DataWriter;

use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * File data writer
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class FilesystemDataWriter implements DataWriterInterface
{
    protected $filesystem;
    protected $outputDir;

    /**
     * Constructor
     *
     * @param Symfony\Component\Filesystem\Filesystem $filesystem
     * @param string                                  $outputDir  The output folder. e.g: "_site"
     */
    public function __construct(Filesystem $filesystem, $outputDir)
    {
        $this->filesystem = $filesystem;
        $this->outputDir = $outputDir;
    }

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $this->filesystem->remove($this->outputDir);
    }

    /**
     * @inheritDoc
     */
    public function write(ItemInterface $item)
    {
        $outputPath = $this->outputDir.'/'.$item->getPath();

        if ($item->isBinary() === false) {
            $this->filesystem->dumpFile($outputPath, $item->getContent());
        } else {
            $this->copy($item->getPath(), $outputPath);
        }
    }

    /**
     * @inheritDoc
     */
    public function tearDown()
    {
    }
}
