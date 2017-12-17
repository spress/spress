<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Support;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as FilesystemBase;

/**
 * An extension for Symfony Filesystem component.
 */
class Filesystem extends FilesystemBase
{
    /**
     * Reads the content of a filename.
     *
     * @param string The file to which to read the content.
     *
     * @return string The content of the filename.
     *
     * @throws FileNotFoundException When the filename does not exist.
     * @throws IOException           When the filename cannot be read or there is another problem.
     */
    public function readFile($filename)
    {
        if (is_file($filename) === false) {
            throw new FileNotFoundException(sprintf('File "%s" does not exist.', $filename));
        }
        if (is_readable($filename) === false) {
            throw new IOException(sprintf('File "%s" cannot be read.', $filename));
        }

        $content = file_get_contents($filename);

        if ($content === false) {
            throw new IOException(sprintf('Error reading the content of the file "%s".', $filename));
        }

        return $content;
    }
}
