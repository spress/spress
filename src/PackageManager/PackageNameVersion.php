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

    private $composerVersionConstraint;

    /**
     * Constructor.
     *
     * @param string $packagName Package's name.
     *                           e.g: "myvendor/foo v1.0"
     *
     * @throws RuntimeException If the parsed package's name is empty
     */
    public function __construct($packageName)
    {
        $versionParser = new VersionParser();
        $pair = $versionParser->parseNameVersionPairs([$packageName])[0];

        if (empty($pair['name']) === true) {
            throw new \RuntimeException('The package name could not be empty.');
        }

        $this->version = isset($pair['version']) ? $pair['version'] : '*';
        $this->name = $pair['name'];

        $this->composerVersionConstraint = $versionParser->parseConstraints($this->version);
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
     * Returns version Constraint for Composer.
     *
     * @return Composer\Semver\Constraint\ConstraintInterface
     */
    public function getComposerVersionConstraint()
    {
        return $this->composerVersionConstraint;
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
