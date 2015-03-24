<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Datasource;

use Symfony\Component\Finder\Finder;

/**
 * Data source for the filesystem.
 *
 * Params:
 *  - source_root	: the root directory for the main content.
 *  - layout_root	: the root directory for the layouts.
 *  - includes_root	: the root directory for the includes.
 *  - posts_root	: the root directory for the posts.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class FilesystemDataSource extends AbstractDataSource
{
	private $items;
	private $layouts;

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
	public function configure()
	{
		$this->items = [];
		$this->layouts = [];
	}

	/**
	 * @inheritDoc
	 */
	public function process()
	{
		$this->processPosts();
	}

	private function processPosts()
	{
		$finder = new Finder();
		$finder->in($this->params['posts_root'])->files();

		foreach ($finder as $file) {
			$id = '/posts/'.$file->getRelativePathname();
			$isBinary = $this->isBinary($file->getPathname());

			$this->items[$id] = new Item($file->getContents(), $id, [], $isBinary);
		}
	}

	private function isBinary($filename)
	{
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $filename);

		return 'text' !== substr($mimeType, 0, 4);
	}
}