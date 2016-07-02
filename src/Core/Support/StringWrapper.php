<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Support;

/**
 * A wrapper for working with string.
 *
 * Based on https://github.com/laravel/framework/blob/5.0/src/Illuminate/Support/Arr.php
 * and https://github.com/danielstjules/Stringy/blob/master/src/Stringy.php
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class StringWrapper
{
    protected $str;
    protected $chars;

    /**
     * Constructor.
     *
     * @param string $str The string.
     */
    public function __construct($str = '')
    {
        $this->str = $str;
    }

    /**
     * Sets the string.
     *
     * @param string $str The string.
     *
     * @return \Yosymfony\Spress\Core\Support This instance.
     */
    public function setString($str)
    {
        $this->str = $str;

        return $this;
    }

    /**
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param bool $removeUnsupported Whether or not to remove the
     *                                unsupported characters
     *
     * @return string
     */
    public function toAscii($removeUnsupported = true)
    {
        $str = $this->str;
        foreach ($this->getChars() as $key => $value) {
            $str = str_replace($value, $key, $str);
        }
        if ($removeUnsupported) {
            $str = preg_replace('/[^\x20-\x7E]/u', '', $str);
        }

        return $str;
    }

    /**
     * Generate a URL friendly "slug".
     *
     * @param string $separator
     *
     * @return string
     */
    public function slug($separator = '-')
    {
        $str = $this->toAscii();

        $flip = $separator == '-' ? '_' : '-';
        $str = str_replace('.', $separator, $str);
        $str = preg_replace('!['.preg_quote($flip).']+!u', $separator, $str);
        $str = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($str));
        $str = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $str);

        return trim($str, $separator);
    }

    /**
     * Determine if a the string starts with a given substring.
     *
     * @param string $value
     *
     * @return bool
     */
    public function startWith($value)
    {
        return $value != '' && strpos($this->str, $value) === 0;
    }

    /**
     * Determine if a the string ends with a given substring.
     *
     * @param string $value
     *
     * @return bool
     */
    public function endWith($value)
    {
        return (string) $value === substr($this->str, -strlen($value));
    }

    /**
     * Gets the first element of the argument matching with
     * the ends of the string.
     *
     * @param array $strings List of strings.
     *
     * @return string The first element or empty string if no matching found.
     */
    public function getFirstEndMatch(array $strings)
    {
        foreach ($strings as $value) {
            if ($this->endWith($value)) {
                return $value;
            }
        }

        return '';
    }

    /**
     * Deletes a prefix of the string.
     *
     * @param string $prefix The prefix.
     *
     * @return string The string without prefix.
     */
    public function deletePrefix($prefix)
    {
        if ($this->startWith($prefix) === true) {
            return substr($this->str, strlen($prefix));
        }

        return $this->str;
    }

    /**
     * Deletes a sufix of the string.
     *
     * @param string $sufix The sufix.
     *
     * @return string The string without sufix.
     */
    public function deleteSufix($sufix)
    {
        if ($this->endWith($sufix) === true) {
            return substr($this->str, 0, -strlen($sufix));
        }

        return $this->str;
    }

    /**
     * Convert the given string to lower-case.
     *
     * @return string
     */
    public function lower()
    {
        return mb_strtolower($this->str, 'UTF-8');
    }

    /**
     * Convert the given string to upper-case.
     *
     * @return string
     */
    public function upper()
    {
        return mb_strtoupper($this->str, 'UTF-8');
    }

    /**
     * Returns the string wrapped.
     *
     * @return string The current value of the wrapper.
     */
    public function __toString()
    {
        return $this->str;
    }

    /**
     * Gets the conversion table.
     *
     * @return array
     */
    protected function getChars()
    {
        if (isset($this->chars) === true) {
            return $this->chars;
        }

        $this->chars = [
            'a' => [
                        'à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ',
                        'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'ä', 'ā', 'ą',
                        'å', 'α', 'ά', 'ἀ', 'ἁ', 'ἂ', 'ἃ', 'ἄ', 'ἅ', 'ἆ',
                        'ἇ', 'ᾀ', 'ᾁ', 'ᾂ', 'ᾃ', 'ᾄ', 'ᾅ', 'ᾆ', 'ᾇ', 'ὰ',
                        'ά', 'ᾰ', 'ᾱ', 'ᾲ', 'ᾳ', 'ᾴ', 'ᾶ', 'ᾷ', 'а', 'أ', ],
            'b' => ['б', 'β', 'Ъ', 'Ь', 'ب'],
            'c' => ['ç', 'ć', 'č', 'ĉ', 'ċ'],
            'd' => ['ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ',
                        'д', 'δ', 'د', 'ض', ],
            'e' => ['é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ',
                        'ệ', 'ë', 'ē', 'ę', 'ě', 'ĕ', 'ė', 'ε', 'έ', 'ἐ',
                        'ἑ', 'ἒ', 'ἓ', 'ἔ', 'ἕ', 'ὲ', 'έ', 'е', 'ё', 'э',
                        'є', 'ə', ],
            'f' => ['ф', 'φ', 'ف'],
            'g' => ['ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ج'],
            'h' => ['ĥ', 'ħ', 'η', 'ή', 'ح', 'ه'],
            'i' => ['í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į',
                            'ı', 'ι', 'ί', 'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ',
                            'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ', 'ῑ', 'ῒ', 'ΐ', 'ῖ',
                            'ῗ', 'і', 'ї', 'и', ],
            'j' => ['ĵ', 'ј', 'Ј'],
            'k' => ['ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك'],
            'l' => ['ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل'],
            'm' => ['м', 'μ', 'م'],
            'n' => ['ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن'],
            'o' => ['ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ',
                            'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő',
                            'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ', 'ὃ', 'ὄ', 'ὅ', 'ὸ', 'ό',
                            'ö', 'о', 'و', 'θ', ],
            'p' => ['п', 'π'],
            'r' => ['ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر'],
            's' => ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص'],
            't' => ['ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط'],
            'u' => ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ',
                            'ự', 'ü', 'û', 'ū', 'ů', 'ű', 'ŭ', 'ų', 'µ', 'у', ],
            'v' => ['в'],
            'w' => ['ŵ', 'ω', 'ώ'],
            'x' => ['χ'],
            'y' => ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ',
                            'ϋ', 'ύ', 'ΰ', 'ي', ],
            'z' => ['ź', 'ž', 'ż', 'з', 'ζ', 'ز'],
            'aa' => ['ع'],
            'ae' => ['æ'],
            'ch' => ['ч'],
            'dj' => ['ђ', 'đ'],
            'dz' => ['џ'],
            'gh' => ['غ'],
            'kh' => ['х', 'خ'],
            'lj' => ['љ'],
            'nj' => ['њ'],
            'oe' => ['œ'],
            'ps' => ['ψ'],
            'sh' => ['ш'],
            'shch' => ['щ'],
            'ss' => ['ß'],
            'th' => ['þ', 'ث', 'ذ', 'ظ'],
            'ts' => ['ц'],
            'ya' => ['я'],
            'yu' => ['ю'],
            'zh' => ['ж'],
            '(c)' => ['©'],
            'A' => ['Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ',
                            'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Ä', 'Å', 'Ā',
                            'Ą', 'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ', 'Ἄ', 'Ἅ', 'Ἆ',
                            'Ἇ', 'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ',
                            'Ᾱ', 'Ὰ', 'Ά', 'ᾼ', 'А', ],
            'B' => ['Б', 'Β'],
            'C' => ['Ç', 'Ć', 'Č', 'Ĉ', 'Ċ'],
            'D' => ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ'],
            'E' => ['É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ',
                            'Ệ', 'Ë', 'Ē', 'Ę', 'Ě', 'Ĕ', 'Ė', 'Ε', 'Έ', 'Ἐ',
                            'Ἑ', 'Ἒ', 'Ἓ', 'Ἔ', 'Ἕ', 'Έ', 'Ὲ', 'Е', 'Ё', 'Э',
                            'Є', 'Ə', ],
            'F' => ['Ф', 'Φ'],
            'G' => ['Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ'],
            'H' => ['Η', 'Ή'],
            'I' => ['Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Î', 'Ï', 'Ī', 'Ĭ', 'Į',
                            'İ', 'Ι', 'Ί', 'Ϊ', 'Ἰ', 'Ἱ', 'Ἳ', 'Ἴ', 'Ἵ', 'Ἶ',
                            'Ἷ', 'Ῐ', 'Ῑ', 'Ὶ', 'Ί', 'И', 'І', 'Ї', ],
            'K' => ['К', 'Κ'],
            'L' => ['Ĺ', 'Ł', 'Л', 'Λ', 'Ļ'],
            'M' => ['М', 'Μ'],
            'N' => ['Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν'],
            'O' => ['Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ',
                            'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ö', 'Ø', 'Ō',
                            'Ő', 'Ŏ', 'Ο', 'Ό', 'Ὀ', 'Ὁ', 'Ὂ', 'Ὃ', 'Ὄ', 'Ὅ',
                            'Ὸ', 'Ό', 'О', 'Θ', 'Ө', ],
            'P' => ['П', 'Π'],
            'R' => ['Ř', 'Ŕ', 'Р', 'Ρ'],
            'S' => ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ'],
            'T' => ['Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ'],
            'U' => ['Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ',
                            'Ự', 'Û', 'Ü', 'Ū', 'Ů', 'Ű', 'Ŭ', 'Ų', 'У', ],
            'V' => ['В'],
            'W' => ['Ω', 'Ώ'],
            'X' => ['Χ'],
            'Y' => ['Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ',
                            'Ы', 'Й', 'Υ', 'Ϋ', ],
            'Z' => ['Ź', 'Ž', 'Ż', 'З', 'Ζ'],
            'AE' => ['Æ'],
            'CH' => ['Ч'],
            'DJ' => ['Ђ'],
            'DZ' => ['Џ'],
            'KH' => ['Х'],
            'LJ' => ['Љ'],
            'NJ' => ['Њ'],
            'PS' => ['Ψ'],
            'SH' => ['Ш'],
            'SHCH' => ['Щ'],
            'SS' => ['ẞ'],
            'TH' => ['Þ'],
            'TS' => ['Ц'],
            'YA' => ['Я'],
            'YU' => ['Ю'],
            'ZH' => ['Ж'],
            ' ' => ["\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81",
                            "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84",
                            "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87",
                            "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A",
                            "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80", ],
        ];

        return $this->chars;
    }
}
