<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\DataSource;

/**
 * Data source manager. Each item will automatically receive an
 * extra attribute "datasource_name".
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class DataSourceManager
{
    const ATTRIBUTE_DATASOURCE_NAME = 'datasource_name';

    private $dataSources;
    private $items;
    private $layouts;
    private $includes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dataSources = [];
        $this->initialize();
    }

     /**
     * Returns the list of items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Returns the list of items with type "layout".
     *
     * @return array
     */
    public function getLayouts()
    {
        return $this->layouts;
    }

    /**
     * Returns the list of items with type "include".
     *
     * @return array
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * Load the items across registered data sources
     */
    public function load()
    {
        $this->initialize();

        foreach ($this->dataSources as $name => $dataSource) {
            $dataSource->load();

            $this->processItems($dataSource->getItems(), $name);
            $this->processLayouts($dataSource->getLayouts(), $name);
            $this->processIncludes($dataSource->getIncludes(), $name);
        }
    }

    /**
     * List of data sources registered.
     *
     * @return array Associative array with the name of the data source as key.
     */
    public function getDataSources()
    {
        return $this->dataSources;
    }

    /**
     * Add a new data source
     *
     * @param AbstractDataSource $dataSource
     * @param string             $name       The name of the data source
     *
     * @throws RuntimeException if a previous data sources exists with the same name
     */
    public function addDataSource(AbstractDataSource $dataSource, $name)
    {
        if (isset($this->dataSources[$name])) {
            throw new \RuntimeException(sprintf('A previous data source exists with the same name: "%s".', $name));
        }

        $this->dataSources[$name] = $dataSource;
    }

    /**
     * Remove a data source from the list
     *
     * @param string $name The name of the data source
     */
    public function removeDataSource($name)
    {
        unset($this->dataSources[$name]);
    }

    private function initialize()
    {
        $this->items = [];
        $this->layouts = [];
        $this->includes = [];
    }

    private function processItems(array $items, $dataSourceName)
    {
        foreach ($items as $item) {
            $id = $item->getId();

            if (true === isset($this->items[$id])) {
                throw new \RuntimeException(sprintf('A previous item exists with the same id: "%s".', $id));
            }

            $this->addAttribute($item, self::ATTRIBUTE_DATASOURCE_NAME, $dataSourceName);
            $this->items[$id] = $item;
        }
    }

    private function processLayouts(array $items, $dataSourceName)
    {
        foreach ($items as $item) {
            $id = $item->getId();

            if (true === isset($this->layouts[$id])) {
                throw new \RuntimeException(sprintf('A previous layout item exists with the same id: "%s".', $id));
            }

            $this->addAttribute($item, self::ATTRIBUTE_DATASOURCE_NAME, $dataSourceName);
            $this->layouts[$id] = $item;
        }
    }

    private function processIncludes(array $items, $dataSourceName)
    {
        foreach ($items as $item) {
            $id = $item->getId();

            if (true === isset($this->includes[$id])) {
                throw new \RuntimeException(sprintf('A previous include item exists with the same id: "%s".', $id));
            }

            $this->addAttribute($item, self::ATTRIBUTE_DATASOURCE_NAME, $dataSourceName);
            $this->includes[$id] = $item;
        }
    }

    private function addAttribute(Item $item, $key, $value)
    {
        $attributes = $item->getAttributes();
        $attributes[$key] = $value;

        $item->setAttributes($attributes);
    }
}
