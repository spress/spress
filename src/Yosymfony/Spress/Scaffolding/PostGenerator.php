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
 * Post generator
 *
 * @author Victor Puertas <vpuertas@gmail.com>
 */
class PostGenerator extends Generator
{
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
	public function generate($targetDir, \DateTime $date, $title, $layout, array $tags, array $categories)
	{
		if(0 === strlen(trim($title)))
		{
			throw new \RuntimeException('Unable to generate the post as the title is empty.');
		}

		if(file_exists($targetDir))
		{
			if(false === is_dir($targetDir))
			{
				throw new \RuntimeException(sprintf('Unable to generate the post as the target directory "%s" exists but is a file.', $targetDir));
			}

			if(false === is_writable($targetDir))
			{
				throw new \RuntimeException(sprintf('Unable to generate the post as the target directory "%s" is not writable.', $targetDir));
			}
		}

		$model = [
			'layout' 		=> $layout,
			'title'			=> $title,
			'categories'	=> $categories,
			'tags'			=> $tags,
		];

		$files = [];

		$target = $targetDir . '/' . $this->getPostFilename($date, $title);
		$files[] = $target;
		$this->renderFile('post/post.md.twig', $target, $model);

		return $files;
	}

	protected function getPostFilename(\DateTime $date, $title, $extension = 'md')
	{
		return sprintf('%s-%s.%s', $date->format('Y-m-d'), Utils::slugify($title), $extension);
	}
}
