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
 * Data source manager
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class DataSourceManager
{
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
     * List with the name of the data sources registered.
     *
     * @return array
     */
    public function getDataSourceNames()
    {
        return array_keys($this->dataSources);
    }

    /**
     * Get a data source
     *
     * @return \Yosymfony\Spress\Core\DataSource\AbstractDataSource
     *
     * @throws \RuntimeException if data source not found
     */
    public function getDataSource($name)
    {
        if (false === $this->hasDataSource($name)) {
            throw new \RuntimeException(sprintf('Data source: "%s" not found.', $name));
        }

        return $this->dataSources[$name];
    }

    /**
     * Exists the data source?
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasDataSource($name)
    {
        return isset($this->dataSources[$name]);
    }

    /**
     * Add a new data source
     *
     * @param AbstractDataSource $dataSource
     * @param string             $name       The name of the data source
     *
     * @throws \RuntimeException if a previous data sources exists with the same name
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

            $this->includes[$id] = $item;
        }
    }
}
