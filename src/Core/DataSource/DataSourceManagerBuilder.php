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
 * Data source manager builder
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class DataSourceManagerBuilder
{
    /**
     * Build a data source manager with data sources
     * loaded from config array.
     *
     * Config array structure:
     * .Array
     * (
     *   [data_source_name_1] => Array
     *        (
     *            [class] => Yosymfony\Spress\Core\DataSource\Filesystem\FilesystemDataSource
     *            [arguments] => Array
     *                (
     *                    [source_root] => ./
     *                )
     *        )
     *
     *    [data_source_name_2] => Array
     *        (
     *        )
     * )
     *
     * @param array $config Configuration array with data about data sources.
     *
     * @return \Yosymfony\Spress\Core\DataSource\DataSourceManager
     *
     * @throws \RuntimeException if params "class" not found or the class pointed by "class" params not exists
     */
    public function buildFromConfigArray(array $config)
    {
        $dsm = new DataSourceManager();

        foreach ($config as $dataSourceName => $data) {
            if (false === isset($data['class'])) {
                throw new \RuntimeException(sprintf('Expected param "class" at the configuration of the data source: "%s".', $dataSourceName));
            }

            $classname = $data['class'];
            $arguments = true === isset($data['arguments']) ? $data['arguments'] : [];

            if (false === class_exists($classname)) {
                throw new \RuntimeException(sprintf('Data source "%s" class not found: "%s".', $dataSourceName, $classname));
            }

            $ds = new $classname($arguments);
            $dsm->addDataSource($dataSourceName, $ds);
        }

        return $dsm;
    }
}
