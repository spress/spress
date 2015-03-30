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
    private $params;

    /**
     * Constructor
     *
     * @param array $params. Format:
     *
     *  data_source_name
     *    class: "the_class_name"
     *    arguments:
     *      argument_1: "value"
     */
    public function __construct(Array $params)
    {
        $this->params = $params;
    }

    public function process()
    {
        foreach ($datasources as $name => $data) {
        }
    }
}
