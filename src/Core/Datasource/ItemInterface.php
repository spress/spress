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
 * Iterface for a data item
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface ItemInterface
{
	const SNAPSHOT_RAW = 'raw';
	const SNAPSHOT_LAST = 'last';
	const SNAPSHOT_AFTER_CONVERT = 'after_convert';
	const SNAPSHOT_AFTER_RENDER = 'after_render';

	/**
	 * Get the compiled content. A snapshot is the compiled content at a
	 * specific point during the compilation process. "last" snapshot is
	 * the most recent compiled content. Binary content cannot have snapshots.
	 * 
	 * @param $snapshotName The name of the snapshot. "last" by default.
	 *
	 * @return Mixed
	 */
	public function getContent($snapshotName);

	/**
	 * Set the compiled content.
	 *
	 * @param $content Mixed The compiled content.
	 * @param $snapshotName The name of the snapshot. The snapshot "last" is not valid.
	 */
	public function setContent($content, $snapshotName);

	/**
	 * True if the item is binary; false if it is not.
	 *
	 * @return @bool
	 */
	public function isBinary();
}