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
use Yosymfony\Spress\Core\Support\AttributesResolver;
use Yosymfony\Spress\Core\Support\StringWrapper;

/**
 * Data source for the filesystem. Binary items don’t have their content
 * loaded in-memory. getPath() returns the path to the binary filename.
 *
 * Source-root structure:
 * |- includes
 * |- layouts
 * |- plugins
 * |- content
 * | |- posts
 * | |- index.html
 * | |- ...
 *
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
 *
 * If the filename is a date filename (a filename that mathed a patter yyyy-mm-dd-title.extension)
 * receive some extra attributes:
 *  - title
 *  - date
 *
 * Params:
 *  - source_root       : the root directory.
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
    private $attributeParser;

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
     * @throws \Yosymfony\Spress\Core\Exception\AttributeValueException   If the attributes don't validate the rules.
     * @throws \Yosymfony\Spress\Core\Exception\MissingAttributeException If missing attribute.
     */
    public function configure()
    {
        $this->items = [];
        $this->layouts = [];
        $this->includes = [];

        $resolver = $this->getResolver();
        $this->params = $resolver->resolve($this->params);

        switch ($this->params['attribute_syntax']) {
            case 'yaml':
                $this->attributeParser = new AttributeParser(AttributeParser::PARSER_YAML);
                break;
            case 'json':
                $this->attributeParser = new AttributeParser(AttributeParser::PARSER_JSON);
                break;
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

    private function processContentFiles()
    {
        $includedFiles = [];

        $finder = new Finder();
        $finder->in($this->composeSubPath('content'))
            ->notName('*.meta')
            ->files();

        foreach ($this->params['include'] as $item) {
            if (is_dir($item)) {
                $finder->in($item);
            } elseif (is_file($item)) {
                $includedFiles[] = new SplFileInfo($item, '', pathinfo($item, PATHINFO_BASENAME));
            }
        }

        $finder->append($includedFiles);

        foreach ($this->params['exclude'] as $item) {
            $finder->notPath($item);
        }

        $this->processItems($finder, Item::TYPE_ITEM);
    }

    private function processLayoutFiles()
    {
        $finder = new Finder();
        $finder->in($this->composeSubPath('layouts'))
            ->files();

        $this->processItems($finder, Item::TYPE_LAYOUT);
    }

    private function processIncludeFiles()
    {
        $finder = new Finder();
        $finder->in($this->composeSubPath('includes'))
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

            if ($isBinary === false) {
                $item->setPath($file->getRelativePathname(), Item::SNAPSHOT_PATH_RELATIVE);
            } else {
                $item->setPath($file->getRelativePathname(), Item::SNAPSHOT_PATH_RELATIVE);
                $item->setPath($file->getRealPath(), Item::SNAPSHOT_PATH_SOURCE);
            }

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
        } elseif (false === $item->isBinary()) {
            $attributes = $this->attributeParser->getAttributesFromFrontmatter($item->getContent());
            $content = $this->attributeParser->getContentFromFrontmatter($item->getContent());
            $item->setContent($content, Item::SNAPSHOT_RAW);
        }

        $attributes['mtime'] = $this->getModifiedTime($file);
        $attributes['filename'] = $file->getFilename();
        $attributes['extension'] = $file->getExtension();

        if ($data = $this->isDateFilename($file)) {
            $attributes['title_path'] = implode(' ', explode('-', $data[3]));

            if (isset($attributes['title']) === false) {
                $attributes['title'] = $attributes['title_path'];
            }

            if (isset($attributes['date']) === false) {
                $attributes['date'] = implode('-', [$data[0], $data[1], $data[2]]);
            }
        }

        $str = new StringWrapper($file->getRelativePath());

        if ($str->startWith('posts/') === true && array_key_exists('categories', $attributes) === false) {
            $categories = explode('/', $str->deletePrefix('posts/'));

            if ($categories[0] === '') {
                unset($categories[0]);
            }

            $attributes['categories'] = $categories;
        }

        $item->setAttributes($attributes);
    }

    private function isBinary(SplFileInfo $file)
    {
        $ext = $file->getExtension();

        return false === in_array($ext, $this->params['text_extensions']);
    }

    private function isDateFilename(SplFileInfo $file)
    {
        $filename = $file->getFilename();

        if (preg_match('/(\d{4})-(\d{2})-(\d{2})-(.+?)(\.[^\.]+|\.[^\.]+\.[^\.]+)$/', $filename, $matches)) {
            return [$matches[1], $matches[2], $matches[3], $matches[4]];
        }
    }

    private function getModifiedTime(SplFileInfo $file)
    {
        $dt = new \DateTime();
        $dt->setTimestamp($file->getMTime());

        return $dt->format(\DateTime::ISO8601);
    }

    private function getAttributesFilename(splfileinfo $file)
    {
        return $this->composeSubPath(sprintf('content/%s.meta', $file->getRelativePathname()));
    }

    private function getResolver()
    {
        $resolver = new AttributesResolver();
        $resolver->setDefault('source_root', '', 'string', true)
            ->setDefault('include', [], 'array')
            ->setDefault('exclude', [], 'array')
            ->setDefault('text_extensions', [], 'array', true)
            ->setDefault('attribute_syntax', 'yaml', 'string')
            ->setValidator('attribute_syntax', function ($value) {
                switch ($value) {
                    case 'yaml':
                    case 'json':
                        return true;
                        break;
                    default:
                        return false;
                        break;
                }
            });

        return $resolver;
    }

    private function composeSubPath($path)
    {
        return $this->params['source_root'].'/'.$path;
    }
}
