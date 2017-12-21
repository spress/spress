<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\SiteMetadata;

use Yosymfony\Spress\Core\SiteMetadata\Exception\InvalidSiteMetadataException;
use Yosymfony\Spress\Core\SiteMetadata\Exception\LoadSiteMetadataException;
use Yosymfony\Spress\Core\SiteMetadata\Exception\SaveSiteMetadataException;
use Yosymfony\Spress\Core\Support\Filesystem;

/**
 * Metadata site stored in a file.
 */
class FileMetadata extends MemoryMetadata
{
    protected $filename;
    protected $fs;

    /**
     * Constructor.
     *
     * @param string $filename Site metadata filename path.
     * @param Filesystem $filesystem
     */
    public function __construct($filename, Filesystem $filesystem)
    {
        $this->filename = $filename;
        $this->fs = $filesystem;
    }

    /**
     * {@inheritdoc}
     *
     * @throws LoadSiteMetadataException In case of error loading a site metadata file.
     * @throws InvalidSiteMetadataException In case of invalid site metadata format.
     */
    public function load()
    {
        if ($this->fs->exists($this->filename) === false) {
            return;
        }

        try {
            $data = $this->fs->readFile($this->filename);
        } catch (\Exception $e) {
            $message = sprintf('Error loading the site metadata: %s', $e->getMessage());
            throw new LoadSiteMetadataException($message, 0, $e, $this->filename);
        }

        $this->metadata = json_decode($data, true);

        if (0 < $errorCode = json_last_error()) {
            $this->throwInvalidMetadataException($errorCode);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws SaveMetadataException In case of error saving site metadata in a file
     * @throws InvalidSiteMetadataException In case of invalid site metadata format.
     */
    public function save()
    {
        $data = json_encode($this->metadata, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($data === false) {
            $this->throwInvalidMetadataException(json_last_error());
        }

        try {
            $this->fs->dumpFile($this->filename, $data);
        } catch (\Exception $e) {
            $message = sprintf('Error saving the site metadata: %s', $e->getMessage());
            throw new SaveSiteMetadataException($message, 0, $e, $this->filename);
        }
    }

    /**
     * @param int $errorCode
     *
     * @return string
     */
    private function getJSONErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return 'Unknown error';
        }
    }

    /**
     * @param int $errorCode
     */
    private function throwInvalidMetadataException($errorCode)
    {
        $message = sprintf('Error parsing JSON - "%s". File: "%s".', $this->getJSONErrorMessage($errorCode), $this->filename);
        throw new InvalidSiteMetadataException($message, 0, null, $this->filename);
    }
}
