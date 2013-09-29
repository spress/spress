<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress;

class Utils
{
    /**
     * Slugify string
     * 
     * @author: http://stackoverflow.com/questions/2955251/php-function-to-make-slug-url-string/2955878#2955878
     * 
     * @param string $text Input text
     * 
     * @return string
     */
    static public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('/[^\\pL\d]+/u', '-', $text);
        
        // trim
        $text = trim($text, '-');
        
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        
        // lowercase
        $text = strtolower($text);
        
        // remove unwanted characters
        $text = preg_replace('/[^-\w]+/', '', $text);
        
        return $text;
    }
}