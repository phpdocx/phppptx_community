<?php
namespace Phppptx\Utilities;
/**
 * PhppptxUtilities
 *
 * @category   Phppptx
 * @package    utilities
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class PhppptxUtilities
{
    /**
     * Generates a UID
     *
     * @return array GUID and RAW GUID
     */
    public static function generateGUID()
    {
        $charid = strtoupper(md5(uniqid((string)rand(), true)));
        $hyphen = chr(45); // '-'
        $uuid = chr(123) // '{'
            . substr($charid, 0, 3) . '14A78' . $hyphen
            . '8E89' . $hyphen
            . '426F' . $hyphen
            . '90D8' . $hyphen
            . '59D98759585D'
            . chr(125); // '}'


        return array('guid' => $uuid, 'rawguid' => str_replace(array('{', '}', '-'), '', $uuid));
    }

    /**
     * Checks if string is UTF8
     *
     * @access public
     * @param string $string_input String to check
     * @static
     * @return boolean
     */
    public static function isUtf8($string_input)
    {
        $string = $string_input;

        $string = preg_replace("#[\x09\x0A\x0D\x20-\x7E]#", "", $string);
        $string = preg_replace("#[\xC2-\xDF][\x80-\xBF]#", "", $string);
        $string = preg_replace("#\xE0[\xA0-\xBF][\x80-\xBF]#", "", $string);
        $string = preg_replace("#[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}#", "", $string);
        $string = preg_replace("#\xED[\x80-\x9F][\x80-\xBF]#", "", $string);
        $string = preg_replace("#\xF0[\x90-\xBF][\x80-\xBF]{2}#", "", $string);
        $string = preg_replace("#[\xF1-\xF3][\x80-\xBF]{3}#", "", $string);
        $string = preg_replace("#\xF4[\x80-\x8F][\x80-\xBF]{2}#", "", $string);

        return ($string == "" ? true : false);
    }

    /**
     * Return a random number
     *
     * @access public
     * @static
     * @param int $min Min value
     * @param int $max Max value
     * @return int
     */
    public static function randomNumber($min, $max)
    {
        return mt_rand($min, $max);
    }

    /**
     * Returns a uniqueid to be used in tags
     *
     * @access public
     * @static
     * @return string
     */
    public static function uniqueId()
    {
        $uniqueid = uniqid('phppptx_');

        return $uniqueid;
    }
}