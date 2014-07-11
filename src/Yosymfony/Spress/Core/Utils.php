<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core;

/**
 * @author Victor Puertas <vpgugr@gmail.com>
 */
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
        $result = preg_replace('/[^\\pL\d]+/u', '-', $text);
        
        // trim
        $result = trim($result, '-');
        
        // transliterate
        $result = iconv('UTF-8', 'US-ASCII//TRANSLIT', $result);
        
        // lowercase
        $result = strtolower($result);
        
        // remove unwanted characters
        $result = preg_replace('/[^-\w]+/', '', $result);
        
        return $result;
    }
}