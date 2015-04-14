<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\ContentManager\Permalink;

use Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkGenerator;
use Yosymfony\Spress\Core\DataSource\Item;

class PermalinkGeneratorTest extends \PHPUnit_Framework_TestCase
{
	public function testPathPermalink()
	{
		$pmg = new PermalinkGenerator('path');
		$permalink = $pmg->getPermalink($this->createItem('index.html'));

		$this->assertInstanceOf('\Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkInterface', $permalink);
		$this->assertEquals('index.html', $permalink->getPath());
		$this->assertEquals('/index.html', $permalink->getUrlPath());
	}

	public function testPrettyPermalink()
	{
		$pmg = new PermalinkGenerator('pretty');
		$permalink = $pmg->getPermalink($this->createItem('index.html'));

		$this->assertEquals('index.html', $permalink->getPath());
		$this->assertEquals('/', $permalink->getUrlPath());

		$permalink = $pmg->getPermalink($this->createItem('my-page/index.html'));

		$this->assertEquals('my-page/index.html', $permalink->getPath());
		$this->assertEquals('/my-page', $permalink->getUrlPath());
	}

	private function createItem($path)
	{
		$item = new Item('', $path);
		$item->setPath($path);

		return $item;
	}
}