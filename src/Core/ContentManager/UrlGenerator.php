<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager;

/**
 * Generate a valid URLs from templates or permalink
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class UrlGenerator
{
    /**
     * Get URL
     *
     * Usage
     *  <code>
     *      $generator = new UrlGenerator();
     *      $url = $generator->getUrl('/:categories/:year/:title', [
     *          ':categories' =>'news/event',
     *          ':year' => 2013,
     *          ':title' => 'tech-event'
     *      ]);
     *
     *      // or Permalink
     *
     *      $url = $generator->getUrl('http://my-blog.com/hello-world');
     *  </code>
     *
     * @param string $template
     * @param array  $placeholders
     *
     * @return string
     */
    public function getUrl($template, array $placeholders = [])
    {
        if (0 == strlen($template)) {
            throw new \InvalidArgumentException('The template param must be a template or a URL');
        }

        $url = str_replace(array_keys($placeholders), $placeholders, $template, $count);

        return $this->sanitize($url);
    }

    private function sanitize($url)
    {
        $count = 0;
        $result = preg_replace('/\/\/+/', '/', $url);
        $result = str_replace(':/', '://', $result, $count);

        if ($count > 1) {
            throw new \UnexpectedValueException(sprintf('Bad URL "%s"', $result));
        }

        if (false !== strpos($result, ' ')) {
            throw new \UnexpectedValueException(sprintf('Bad URL "%s". Contain white space/s', $result));
        }

        return $result;
    }
}
