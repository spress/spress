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
use Yosymfony\Spress\Core\Support\FileInfo;
use Yosymfony\Spress\Core\Support\StringWrapper;

/**
 * Filesystem data source. Binary items don’t have their content
 * loaded in-memory. getPath method returns the path to the binary filename.
 *
 * The directory separator is '/' in any case.
 *
 * Source-root structure:
 * |- themes
 * | |- theme1
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
 * If the filename is a date filename, a filename that matched a patter yyyy-mm-dd-title.extension,
 * receive some extra attributes:
 *  - title
 *  - title_path
 *  - date
 *
 * If the filename is located in a subfolder of "posts/" receive an extra attribute "categories".
 *
 * Params:
 *  - source_root      (string): the root directory.
 *  - include           (array): force to include files or directories. e.g:"/tmp/files".
 *  - exclude           (array): force to exclude files or directories. e.g: "post".
 *  - text_extensions   (array): extension of the files considered as text files. e.g: "html".
 *  - attribute_syntax (string): syntax for describing attributes: "yaml" or "json". "yaml" by default.
 *  - avoid_renderizer_path (array): the files belong to declared path will have set up `avoid_renderizer` to true.
 *  - avoid_renderizer_extension (array): the filenames with an extension belong to declared will have set up `avoid_renderizer` to true.
 *  - theme_name (string): The name of the theme. Empty string by default. e.g: "theme1" or "vendor1/theme".
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class FilesystemDataSource extends AbstractDataSource
{
    /** @var Item[] */
    private $items;

    /** @var Item[] */
    private $layouts;

    /** @var Item[] */
    private $includes;

    /** @var AttributeParser */
    private $attributeParser;

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function getLayouts()
    {
        return $this->layouts;
    }

    /**
     * {@inheritdoc}
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException   If the attributes don't validate the rules
     * @throws Yosymfony\Spress\Core\ContentManager\Exception\MissingAttributeException If missing attribute
     */
    public function configure()
    {
        $this->items = [];
        $this->layouts = [];
        $this->includes = [];

        $resolver = $this->createResolver();
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
     * {@inheritdoc}
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
        $path = $this->composeSubPath('layouts');

        if (file_exists($path) === false) {
            return;
        }

        $finder = new Finder();

        $finder->in($path)
            ->files();

        if (empty($this->params['theme_name']) === false) {
            $finder->in($this->composeThemeSubPath('layouts'));
        }

        $this->processItems($finder, Item::TYPE_LAYOUT);
    }

    private function processIncludeFiles()
    {
        $path = $this->composeSubPath('includes');

        if (file_exists($path) === false) {
            return;
        }

        $finder = new Finder();
        $finder->in($path)
            ->files();

        if (empty($this->params['theme_name']) === false) {
            $finder->in($this->composeThemeSubPath('includes'));
        }

        $this->processItems($finder, Item::TYPE_INCLUDE);
    }

    private function processItems(Finder $finder, $type)
    {
        $files = iterator_to_array($finder);

        foreach ($files as $file) {
            $id = $this->normalizeDirSeparator($file->getRelativePathname());
            $isBinary = $this->isBinary($file);
            $contentRaw = $isBinary ? '' : $file->getContents();

            $item = new Item($contentRaw, $id, [], $isBinary, $type);
            $item->setPath($this->normalizeDirSeparator($file->getRelativePathname()), Item::SNAPSHOT_PATH_RELATIVE);

            if ($isBinary === true) {
                $item->setPath($this->normalizeDirSeparator($file->getRealPath()), Item::SNAPSHOT_PATH_SOURCE);
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
        $isItemType = $item->getType() === Item::TYPE_ITEM;

        if ($attributesFile && file_exists($attributesFile)) {
            $contentFile = file_get_contents($attributesFile);
            $attributes = $this->attributeParser->getAttributesFromString($contentFile);
        } elseif (false === $item->isBinary()) {
            $attributes = $this->attributeParser->getAttributesFromFrontmatter($item->getContent());
            $content = $this->attributeParser->getContentFromFrontmatter($item->getContent());
            $item->setContent($content, Item::SNAPSHOT_RAW);
        }

        $fileInfo = new FileInfo($file->getPathname(), $this->params['text_extensions']);
        $avoidRender = $this->avoidRenderizer($fileInfo->getExtension(), $file->getRelativePath());

        if ($isItemType && $avoidRender === true && isset($attributes['avoid_renderizer']) === false) {
            $attributes['avoid_renderizer'] = true;
        }

        $attributes['mtime'] = $this->getModifiedTime($file);
        $attributes['filename'] = $fileInfo->getFilename();
        $attributes['extension'] = $fileInfo->getExtension();

        if ($data = $this->isDateFilename($attributes['filename'])) {
            $attributes['title_path'] = $data[3];

            if (isset($attributes['title']) === false) {
                $attributes['title'] = implode(' ', explode('-', $attributes['title_path']));
            }

            if (isset($attributes['date']) === false) {
                $attributes['date'] = implode('-', [$data[0], $data[1], $data[2]]);
            }
        }

        $str = new StringWrapper($this->normalizeDirSeparator($file->getRelativePath()));

        if ($isItemType && $str->startWith('posts/') === true && array_key_exists('categories', $attributes) === false) {
            $categories = explode('/', $str->deletePrefix('posts/'));

            if ($categories[0] === '') {
                unset($categories[0]);
            }

            $attributes['categories'] = $categories;
        }

        $item->setAttributes($attributes);
    }

    private function avoidRenderizer($extension, $relativePath)
    {
        $strPath = new StringWrapper($relativePath);

        foreach ($this->params['avoid_renderizer_path'] as $path) {
            if ($relativePath == $path || $strPath->startWith($path.'/') === true) {
                return true;
            }
        }

        if (in_array($extension, $this->params['avoid_renderizer_extension']) === true) {
            return true;
        }

        return false;
    }

    private function isBinary(SplFileInfo $file)
    {
        $fileInfo = new FileInfo($file->getPathname(), $this->params['text_extensions']);

        return false === $fileInfo->hasPredefinedExtension();
    }

    private function isDateFilename($filename)
    {
        if (preg_match('/(\d{4})-(\d{2})-(\d{2})-(.+?)$/', $filename, $matches)) {
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
        $relativePathname = $this->normalizeDirSeparator($file->getRelativePathname());

        return $this->composeSubPath(sprintf('content/%s.meta', $relativePathname));
    }

    private function createResolver()
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
            })
            ->setDefault('avoid_renderizer_path', [], 'array')
            ->setDefault('avoid_renderizer_extension', [], 'array')
            ->setDefault('theme_name', '', 'string');

        return $resolver;
    }

    private function composeSubPath($path)
    {
        return $this->params['source_root'].'/'.$path;
    }

    /**
     * @return string
     */
    private function composeThemeSubPath($path)
    {
        return sprintf('%s/themes/%s/src/%s',
            $this->params['source_root'],
            $this->params['theme_name'],
            $path
        );
    }

    private function normalizeDirSeparator($path)
    {
        if (DIRECTORY_SEPARATOR === '/') {
            return $path;
        }

        return str_replace(DIRECTORY_SEPARATOR, '/', $path);
    }
}
