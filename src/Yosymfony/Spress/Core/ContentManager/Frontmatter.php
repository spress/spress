<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager;

use Yosymfony\Spress\Core\Configuration;

/**
 * Frontmatter is the configuration seccion of pages and posts.
 * The configuration is set between triple dashed lines.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Frontmatter
{
    private $content;
    private $configuration;
    private $repository;
    private $pattern = '/^---\r*\n(.*)\r*\n?---\r*\n?/isU';
    private $fm;
    private $result = false;

    /**
     * Constructor
     *
     * @param string $content
     * @param Yosymfony\Spress\Configuration $configuration Transform Front-matter to repository
     */
    public function __construct($content, Configuration $configuration)
    {
        $this->content = $content;
        $this->configuration = $configuration;
        $this->process();
    }

    /**
     * Set a new Front-matter
     *
     * @param array Front-matter key-value set
     */
    public function setFrontmatter(array $frontmatter)
    {
        $this->repository = $this->configuration->createBlankRepository();
        $this->repository->load($frontmatter);
        $this->fm = '';
        $this->result = true;
    }

    /**
     * The content has Front-matter?
     *
     * @return bool
     */
    public function hasFrontmatter()
    {
        return $this->result;
    }

    /**
     * Get the Front-matter as string (no parsed)
     *
     * @return string FALSE if any problem occurs
     */
    public function getFrontmatterString()
    {
        $result = false;

        if($this->hasFrontmatter())
        {
            $result = $this->fm;
        }

        return $result;
    }

    /**
     * Get Front-matter as array
     *
     * @return array
     */
    public function getFrontmatterArray()
    {
        return $this->repository->getRaw();
    }

    /**
     * Get Front-matter as repository
     *
     * @return Yosymfony\Silex\ConfigServiceProvider\ConfigRepository
     */
    public function getFrontmatter()
    {
        return $this->repository;
    }

    /**
     * @return string FALSE if any problem occurs
     */
    public function getFrontmatterWithDashedLines()
    {
        $result = false;

        if ($this->hasFrontmatter())
        {
            $result = sprintf("---\n%s\n---", $this->fm);
        }

        return $result;
    }

    /**
     * Get content without Front-matter
     *
     * @return string
     */
    public function getContentNotFrontmatter()
    {
        $result = preg_replace($this->pattern, '', $this->content, 1);
        return ltrim($result);
    }

    private function process()
    {
        $result = preg_match($this->pattern, $this->content, $matches);

        if(1 === $result)
        {
            $this->fm = $matches[1];
            $this->result = true;
        }

        $this->repository = $this->configuration->getRepositoryInline($this->getFrontmatterString());
    }
}
