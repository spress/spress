<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\DataSource;

use Yosymfony\Spress\Core\DataSource\DataSourceManager;
use Yosymfony\Spress\Core\DataSource\Filesystem\FilesystemDataSource;

class DataSourceManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatingDataSourceManager()
    {
        $dsm = new DataSourceManager();

        $this->assertTrue(is_array($dsm->getItems()));
        $this->assertTrue(is_array($dsm->getLayouts()));
        $this->assertTrue(is_array($dsm->getIncludes()));

        $this->assertCount(0, $dsm->getItems());
        $this->assertCount(0, $dsm->getLayouts());
        $this->assertCount(0, $dsm->getIncludes());
    }

    public function testAddDataSource()
    {
        $dsm = new DataSourceManager();
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../fixtures/project/',
            'layouts_root'      => __dir__.'/../fixtures/project/_layouts/',
            'includes_root'     => __dir__.'/../fixtures/project/_includes/',
            'posts_root'        => __dir__.'/../fixtures/project/_posts/',
            'text_extensions'   => ['htm', 'html', 'md', 'mkd', 'xml'],
        ]);

        $dsm->addDataSource($fsDataSource, 'filesystem');
        $dsm->load();

        $this->assertCount(12, $dsm->getItems());
        $this->assertCount(1, $dsm->getLayouts());
        $this->assertCount(1, $dsm->getIncludes());

        $item = current($dsm->getItems());

        $this->assertArrayHasKey('datasource_name', $item->getAttributes());

        $item = current($dsm->getLayouts());

        $this->assertArrayHasKey('datasource_name', $item->getAttributes());

        $item = current($dsm->getIncludes());

        $this->assertArrayHasKey('datasource_name', $item->getAttributes());
    }

    public function testReLoad()
    {
        $dsm = new DataSourceManager();
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../fixtures/project/',
            'text_extensions'   => ['htm', 'html', 'md', 'mkd', 'xml'],
        ]);

        $dsm->addDataSource($fsDataSource, 'filesystem');
        $dsm->load();
        $dsm->load();

        $this->assertCount(8, $dsm->getItems());
        $this->assertCount(0, $dsm->getLayouts());
        $this->assertCount(0, $dsm->getIncludes());
    }

    public function testAddDSomeDataSources()
    {
        $dsm = new DataSourceManager();
        $fsDataSource1 = new FilesystemDataSource([
            'source_root'       => __dir__.'/../fixtures/project/',
            'text_extensions'   => ['htm', 'html', 'md', 'mkd', 'xml'],
        ]);

        $fsDataSource2 = new FilesystemDataSource([
            'source_root'       => __dir__.'/../fixtures/extra_pages/',
            'text_extensions'   => ['htm', 'html', 'md', 'mkd', 'xml'],
        ]);

        $dsm->addDataSource($fsDataSource1, 'filesystem_1');
        $dsm->addDataSource($fsDataSource2, 'filesystem_2');
        $dsm->load();

        $this->assertCount(2, $dsm->getDataSources());

        $this->assertCount(10, $dsm->getItems());
        $this->assertCount(0, $dsm->getLayouts());
        $this->assertCount(0, $dsm->getIncludes());
    }

    public function testRemoveDataSource()
    {
        $dsm = new DataSourceManager();
        $fsDataSource1 = new FilesystemDataSource([
            'source_root'       => __dir__.'/../fixtures/project/',
            'text_extensions'   => ['htm', 'html', 'md', 'mkd', 'xml'],
        ]);

        $fsDataSource2 = new FilesystemDataSource([
            'source_root'       => __dir__.'/../fixtures/extra_pages/',
            'text_extensions'   => ['htm', 'html', 'md', 'mkd', 'xml'],
        ]);

        $dsm->addDataSource($fsDataSource1, 'filesystem_1');
        $dsm->addDataSource($fsDataSource2, 'filesystem_2');
        $dsm->removeDataSource('filesystem_1');
        $dsm->load();

        $this->assertCount(1, $dsm->getDataSources());
        $this->assertArrayHasKey('filesystem_2', $dsm->getDataSources());
        $this->assertArrayNotHasKey('filesystem_1', $dsm->getDataSources());

        $this->assertCount(2, $dsm->getItems());
        $this->assertCount(0, $dsm->getLayouts());
        $this->assertCount(0, $dsm->getIncludes());
    }
}
