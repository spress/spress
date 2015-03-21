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
	/**
	 * @inheritDoc
	 */
	public function getItems()
	{

	}

	/**
	 * @inheritDoc
	 */
	public function getLayouts()
	{

	}

	/**
	 * @inheritDoc
	 */
	public function configure()
	{
	}
}