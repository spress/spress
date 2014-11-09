<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Scaffolding;

use Yosymfony\Spress\Core\Utils;

/**
 * Plugin generator
 *
 * @author Victor Puertas <vpuertas@gmail.com>
 */
class PluginGenerator extends Generator
{
	protected $files;

	/**
	 * Generate a post
	 *
	 * @param $targetDir string
	 * @param $tdate DateTime
	 * @param $title string
	 * @param $layout string
	 * @param $categories array
	 * @param $tags array
	 *
	 * @return array
	 */
	public function generate($targetDir, $name, $namespace = '', $author = '', $description = '', $license = 'MIT')
	{
		$this->files = [];

		if(0 === strlen(trim($name)))
		{
			throw new \RuntimeException('Unable to generate the plugin as the name is empty.');
		}

		if(file_exists($targetDir))
		{
			if(false === is_dir($targetDir))
			{
				throw new \RuntimeException(sprintf('Unable to generate the plugin as the target directory "%s" exists but is a file.', $targetDir));
			}

			if(false === is_writable($targetDir))
			{
				throw new \RuntimeException(sprintf('Unable to generate the plugin as the target directory "%s" is not writable.', $targetDir));
			}
		}

		$pluginDir = $targetDir . '/' . $this->getPluginDir($name);

		if(file_exists($pluginDir))
		{
			throw new \RuntimeException(sprintf('Unable to generate the plugin as the plugin directory "%s" exists.', $pluginDir));
		}

		$model = [
			'name'			=> $name,
			'classname'		=> $this->getClassname($name),
			'namespace'		=> $namespace,
			'author'		=> $author,
			'description'	=> $description,
			'license'		=> $license,
		];

		$this->cleanFilesAffected();
		
		$this->renderFile('plugin/plugin.php.twig', $pluginDir . '/' . $this->getPluginFilename($name), $model);
		$this->renderFile('plugin/composer.json.twig', $pluginDir . '/composer.json', $model);

		return $this->getFilesAffected();
	}

	protected function getClassname($name)
	{
        // replace non letter or digits by empty string
        $result = preg_replace('/[^\\pL\d]+/u', '', $name);
        
        // trim
        $result = trim($result, '-');
        
        // transliterate
        $result = iconv('UTF-8', 'US-ASCII//TRANSLIT', $result);
        
        // lowercase
        $result = strtolower($result);
        
        // remove unwanted characters
        $result = preg_replace('/[^-\w]+/', '', $result);
        
        return ucfirst($result);
	}

	protected function getPluginDir($name)
	{
		return $this->getClassname($name);
	}

	protected function getPluginFilename($name)
	{
		return sprintf('%s.php', $this->getClassname($name));
	}
}
