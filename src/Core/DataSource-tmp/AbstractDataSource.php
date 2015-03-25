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
 * It is the superclass for all data sources
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
abstract class AbstractDataSource
{
	protected $referenceCounter;
	protected $params;
	protected $isConfigured;

	/**
	 * Constructor
	 */
	public function __construct(array $params)
	{
		$this->params = $params;
		$this->referenceCounter = 0;
		$this->isConfigured = false;
	}

	/**
	 * Returns the list of items
	 * 
	 * @return array
	 */
	abstract public function getItems();

	/**
	 * Returns the list of items with type "layout".
	 *
	 * @return array
	 */
	abstract public function getLayouts();

	/**
	 * Creates a new item or layout in the data source
	 *
	 * @param Item $item
	 */
	public function addItem(Item $item)
	{

	}

	/**
	 * Loads the data source
	 */
	public function load()
	{
		$this->addUse();
		$this->process();
		$this->removeUse();
	}

	/**
	 * Marks as used the data source.
	 * This method increases the internal reference count.
	 * When the internal reference count goes from 0 to 1 setUp method
	 * is invoked.
	 */
	public function addUse()
	{
		if (false === $this->isConfigured) {
			$this->configure();
			$this->isConfigured = true;
		}

		if ($this->referenceCounter < 0) {
			$this->referenceCounter = 0;
		}

		if ($this->referenceCounter === 0) {
			$this->setUp();
		}

		$this->referenceCounter++;
	}

	/**
	 * Marks as unused the data source.
	 * This method decreases the internal reference count.
	 * When the internal reference count is 0 tearDown method
	 * is invoked.
	 */
	public function removeUse()
	{
		$this->referenceCounter--;

		if ($this->referenceCounter === 0) {
			$this->tearDown();
		}
	}

	/**
	 * Brings up the connections to the data.
	 * e.g: this is the ideal place to connect to the database.
	 */
	public function setUp()
	{

	}

	/**
	 * Brings down the connections to the data.
	 * e.g: a database connection established in setUp should be closed in this method.
	 */
	public function tearDown()
	{

	}

	/**
	 * This method is used for setting up a site’s data source for the first time.
	 */
	public function configure()
	{

	}

	/**
	 * All data source data manipulations and queries
	 */
	public function process()
	{

	}
}