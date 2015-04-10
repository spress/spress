<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\DataSource\Filesystem;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Yosymfony\Spress\Core\DataSource\AbstractDataSource;
use Yosymfony\Spress\Core\DataSource\Item;

/**
 * Data source for the filesystem. Binary items don’t have their content
 * loaded in-memory. getPath() returns the path to the binary filename.
 * The item's attributes (metas) will be loaded from a block located at
 * the top of the each file (frontmatter) or from a separated metadata file.
 * e.g:
 *  - index.html
 *  - index.html.meta   <- metadata file with the attributes.
 *
 * Each item will automatically receive some extra attributes:
 *  - mtime:            : modified time.
 *  - filename          : the name of the file.
 *  - extension         : the extension of item's filename.
 *  - meta_filename     : if exists, the name of the filename with the item's attributes (metas).
 *
 * Params:
 *  - source_root       : the root directory for the main content.
 *  - layouts_root      : the root directory for the layouts.
 *  - includes_root     : the root directory for the includes.
 *  - posts_root        : the root directory for the posts.
 *  - include           : force to include files or directories.
 *  - exclude           : force to exclude files or directories.
 *  - text_extensions   : extension of the files considered as text files.
 *  - attribute_syntax  : syntax for describing attributes: "yaml" or "json". "yaml" by default.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class FilesystemDataSource extends AbstractDataSource
{
    private $items;
    private $layouts;
    private $includes;
    private $include;
    private $exclude;
    private $orgDir;
    private $attributeParser;
    private $textExtensions;

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function getLayouts()
    {
        return $this->layouts;
    }

    /**
     * @inheritDoc
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * @inheritDoc
     *
     * @throws \RuntimeException if bad configuration params
     */
    public function configure()
    {
        $this->items = [];
        $this->layouts = [];
        $this->includes = [];
        $this->include = [];
        $this->exclude = [];

        if (false === isset($this->params['source_root'])) {
            throw new \RuntimeException('The data source expected param: "source_root".');
        }

        if (false === isset($this->params['text_extensions'])) {
            throw new \RuntimeException('The data source expected param: "text_extensions".');
        }

        if (false === is_array($this->params['text_extensions'])) {
            throw new \RuntimeException('The data source expected a array at param: "text_extensions".');
        }

        if (false === is_string($this->params['source_root'])) {
            throw new \RuntimeException('The data source expected a string at param: "source_root".');
        }

        if (true === isset($this->params['posts_root']) && false === is_string($this->params['posts_root'])) {
            throw new \RuntimeException('The data source expected a string at param: "posts_root".');
        }

        if (true === isset($this->params['layouts_root']) && false === is_string($this->params['layouts_root'])) {
            throw new \RuntimeException('The data source expected a string at param: "layouts_root".');
        }

        if (true === isset($this->params['includes_root']) && false === is_string($this->params['includes_root'])) {
            throw new \RuntimeException('The data source expected a string at param: "includes_root".');
        }

        if (true === isset($this->params['include'])) {
            if (true === is_array($this->params['include'])) {
                $this->include = $this->params['include'];
            } else {
                throw new \RuntimeException('The data source expected an array at param: "include".');
            }
        }

        if (true === isset($this->params['exclude'])) {
            if (true === is_array($this->params['exclude'])) {
                $this->exclude = $this->params['exclude'];
            } else {
                throw new \RuntimeException('The data source expected an array at param: "exclude".');
            }
        }

        if (false === isset($this->params['attribute_syntax'])) {
            $this->params['attribute_syntax'] = 'yaml';
        }

        $this->textExtensions = $this->params['text_extensions'];

        switch ($this->params['attribute_syntax']) {
            case 'yaml':
                $this->attributeParser = new AttributeParser(AttributeParser::PARSER_YAML);
                break;
            case 'json':
                $this->attributeParser = new AttributeParser(AttributeParser::PARSER_JSON);
                break;
            default:
                throw new \RuntimeException(sprintf('Invalid value for attributte "attribute_syntax": "%s".', $this->params['attribute_syntax']));
        }
    }

    /**
     * @inheritDoc
     */
    public function process()
    {
        $this->processContentFiles();
        $this->processLayoutFiles();
        $this->processIncludeFiles();
    }

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $this->orgDir = getcwd();
        $this->setCurrentDir($this->params['source_root']);
    }

    /**
     * @inheritDoc
     */
    public function tearDown()
    {
        $this->setCurrentDir($this->orgDir);
    }

    private function processContentFiles()
    {
        $includedFiles = [];

        $finder = new Finder();
        $finder->in($this->params['source_root'])
            ->notPath('/^_/')
            ->notPath('config.yml')
            ->notPath('/config_.+\.yml/')
            ->notName('*.meta')
            ->files();

        if (isset($this->params['posts_root'])) {
            $finder->in($this->params['posts_root']);
        }

        foreach ($this->include as $item) {
            if (is_dir($item)) {
                $finder->in($item);
            } elseif (is_file($item)) {
                $includedFiles[] = new SplFileInfo($item, '', pathinfo($item, PATHINFO_BASENAME));
            }
        }

        $finder->append($includedFiles);

        foreach ($this->exclude as $item) {
            $finder->notPath($item);
        }

        $this->processItems($finder, Item::TYPE_ITEM);
    }

    private function processLayoutFiles()
    {
        if (false === isset($this->params['layouts_root'])) {
            return;
        }

        $finder = new Finder();
        $finder->in($this->params['layouts_root'])
            ->files();

        $this->processItems($finder, Item::TYPE_LAYOUT);
    }

    private function processIncludeFiles()
    {
        if (false === isset($this->params['includes_root'])) {
            return;
        }

        $finder = new Finder();
        $finder->in($this->params['includes_root'])
            ->files();

        $this->processItems($finder, Item::TYPE_INCLUDE);
    }

    private function processItems(Finder $finder, $type)
    {
        foreach ($finder as $file) {
            $id = $file->getRelativePathname();
            $isBinary = $this->isBinary($file);
            $contentRaw = $isBinary ? '' : $file->getContents();

            $item = new Item($contentRaw, $id, [], $isBinary, $type);
            $item->setPath($file->getRelativePathname());

            switch ($type) {
                case Item::TYPE_LAYOUT:
                    $this->processAttributes($item, $file);
                    $this->layouts[$id] = $item;
                    break;
                case Item::TYPE_INCLUDE:
                    $this->includes[$id] = $item;
                    break;
                default:
                    $this->processAttributes($item, $file);
                    $this->items[$id] = $item;
                    break;
            }
        }
    }

    private function processAttributes(Item $item, SplFileInfo $file)
    {
        $attributes = [];
        $attributesFile = $this->getAttributesFilename($file);

        if ($attributesFile && file_exists($attributesFile)) {
            $contentFile = file_get_contents($attributesFile);
            $attributes = $this->attributeParser->getAttributesFromString($contentFile);
            $attributes['meta_filename'] = $attributesFile;
        } elseif (false === $item->isBinary()) {
            $attributes = $this->attributeParser->getAttributesFromFrontmatter($item->getContent());
            $content = $this->attributeParser->getContentFromFrontmatter($item->getContent());
            $item->setContent($content, Item::SNAPSHOT_RAW);
        }

        $attributes['mtime'] = $this->getModifiedTime($file);
        $attributes['filename'] = $file->getFilename();
        $attributes['extension'] = $file->getExtension();

        $item->setAttributes($attributes);
    }

    private function isBinary(SplFileInfo $file)
    {
        $ext = $file->getExtension();

        return false === in_array($ext, $this->textExtensions);
    }

    private function getModifiedTime(SplFileInfo $file)
    {
        $dt = new \DateTime();
        $dt->setTimestamp($file->getMTime());

        return $dt->format(\DateTime::ISO8601);
    }

    private function getAttributesFilename(splfileinfo $file)
    {
        return $file->getRelativePathname().'.meta';
    }

    private function setCurrentDir($path)
    {
        if (false === file_exists($path)) {
            throw new \RuntimeException(sprintf('The source root folder not exists: "%s".', $path));
        }

        if (false === chdir($path)) {
            throw new \RuntimeException(sprintf('Error changing the current dir to "%s".', $path));
        }
    }
}
