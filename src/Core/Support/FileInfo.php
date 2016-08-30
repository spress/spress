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

/**
 * Extends \SplFileInfo.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class FileInfo extends \SplFileInfo
{
    private $hasPredefinedExt = false;
    private $filename;
    private $extension;
    private $predefinedExtensions;

    /**
     * Constructor.
     *
     * @param string $file                 The file name
     * @param array  $predefinedExtensions Predefined extensions
     */
    public function __construct($file, array $predefinedExtensions = [])
    {
        parent::__construct($file);

        $this->predefinedExtensions = $predefinedExtensions;
    }

    /**
     * Gets the filename.
     *
     * @return string
     */
    public function getFilename()
    {
        if (is_null($this->filename) === true) {
            $str = new StringWrapper(parent::getFilename());
            $this->filename = $str->deleteSufix('.'.$this->getExtension());
        }

        return $this->filename;
    }

    /**
     * Gets the extension of the file.
     *
     * @return string
     */
    public function getExtension()
    {
        if (is_null($this->extension) === true) {
            $filename = parent::getFilename();
            $str = new StringWrapper($filename);
            $this->extension = $str->getFirstEndMatch($this->predefinedExtensions);
            $this->hasPredefinedExt = true;

            if ($this->extension === '') {
                $this->hasPredefinedExt = false;
                $this->extension = parent::getExtension();
            }
        }

        return $this->extension;
    }

    /**
     * Has a predefined extension?
     *
     * @return bool
     */
    public function hasPredefinedExtension()
    {
        $this->getExtension();

        return $this->hasPredefinedExt;
    }
}
