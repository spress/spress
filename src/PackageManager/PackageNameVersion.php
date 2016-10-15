<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\PackageManager;

use Composer\Package\Version\VersionParser;

/**
 * Parse a Package name-version pair.
 * e.g: "myvendor/foo v1.0" or "myvendor/foo:v1.0".
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PackageNameVersion
{
    /** @var string */
    private $name;

    /** @var string */
    private $version;

    /**
     * Constructor.
     *
     * @param string $packagName Package's name.
     *                           e.g: "myvendor/foo v1.0"
     */
    public function __construct($packageName)
    {
        $versionParser = new VersionParser();
        $pair = $versionParser->parseNameVersionPairs([$packageName])[0];

        $this->version = isset($pair['version']) ? $pair['version'] : '*';
        $this->name = $pair['name'];
    }

    /**
     * Returns the Package's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the package's version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Returns a normalized name-version pair.
     *
     * @return string
     */
    public function getNormalizedNameVersion()
    {
        return $this->name.' '.$this->version;
    }

    /**
     * Convert to string the name and version of the package.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getNormalizedNameVersion();
    }
}
