<?php

/**
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress;
 
use Michelf\MarkdownExtra;
 
/**
 * A class wrapper for a Markdown library
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class MarkdownWrapper
{
    /**
     * Parse Marksown string to HTML
     * 
     * @param string $markdownText
     * 
     * @return string HTML
     */
    public function parse($markdownText)
    {
        if(!is_string($markdownText))
        {
            throw new \InvalidArgumentException('Expected Markdown string to parse');
        }
        
        return MarkdownExtra::defaultTransform($markdownText);
    }
 }