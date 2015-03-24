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

use Yosymfony\Spress\Core\DataSource\FilesystemDataSource;

class FilesystemDataSourceTest extends \PHPUnit_Framework_TestCase
{
	public function testProcessItems()
	{
		$fsDataSource = new FilesystemDataSource([
			'source_root' 	=> '',
			'layout_root' 	=> '',
			'includes_root' => '',
			'posts_root' 	=> __dir__.'/../fixtures/project/_posts/',
		]);

		$fsDataSource->load();

		$this->assertTrue(is_array($fsDataSource->getItems()));
		$this->assertTrue(is_array($fsDataSource->getLayouts()));
	}
}