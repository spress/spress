<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\ContentManager;

use Yosymfony\Spress\Core\ContentManager\ContentManager;
use Yosymfony\Spress\Core\DataSource\DataSourceManagerBuilder;
use Yosymfony\Spress\Core\DataWriter\MemoryDataWriter;

class ContentManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testParseSite()
    {
    	$cm = $this->getContentManager();
    }

    protected function getContentManager()
    {
    	$dsm = $this->getDataSourceManager();
    	$dw = new MemoryDataWriter();

    	return new ContentManager($dsm, $dw);
    }

    protected function getDataSourceManager()
    {
        $config = [
            'data_source_name_1' => [
                'class' => 'Yosymfony\Spress\Core\DataSource\Filesystem\FilesystemDataSource',
                'arguments' => [
                    'source_root' => __dir__.'/../fixtures/project/',
                ],
            ],
        ];

        $builder = new DataSourceManagerBuilder();

        return $builder->buildFromConfigArray($config);
    }
}
