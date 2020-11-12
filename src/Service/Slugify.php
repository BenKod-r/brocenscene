<?php
/**
 * Created by IntelliJ IDEA.
 * User: Benharrat Khaled
 * Date: 24/08/2020
 * Time: 18:37
 */

namespace App\Service;

/**
 * Class Slugify
 * @package App\Service
 */
class Slugify
{
    /**
     * Generates a slug with a string
     * @param string $input
     * @return string
     */
    public function generate(string $input) :string
    {
        // Conversion of special characters to unicode characters
        // Return a slug without punctuation, without spaces at the beginning and end of the chain,
        // Without successive "-" and in lowercase
        if (!empty($input)) {
            $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $input);
        }
        if (!empty($slug)) {
            $slug = trim($slug);
            $slug = preg_replace("/[[:punct:]]/", "", $slug);
        }
        if (!empty($slug)) {
            $slug = str_replace(" ", "-", $slug);
            $slug = strtolower($slug);
            return $slug;
        }
    }
}
