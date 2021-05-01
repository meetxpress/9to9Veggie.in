<?php

namespace GoDaddy\WordPress\MWC\Common\Helpers;

/**
 * An helper to manipulate strings.
 *
 * @since 1.0.0
 */
class StringHelper
{
    /**
     * Gets the portion of a string after a given delimiter.
     *
     * @since 1.0.0
     *
     * @param string $string
     * @param string $delimiter
     * @return string
     */
    public static function after(string $string, string $delimiter) : string
    {
        return array_reverse(explode($delimiter, $string, 2))[0];
    }

    /**
     * Gets the portion of a string after the last occurrence of a given delimiter.
     *
     * @since 1.0.0
     *
     * @param string $string
     * @param string $delimiter
     * @return string
     */
    public static function afterLast(string $string, string $delimiter) : string
    {
        if ($delimiter === substr($string, -1)) {
            return '';
        }

        return strrev(self::before(strrev($string), $delimiter));
    }

    /**
     * Gets the portion of a string before a given delimiter.
     *
     * @since 1.0.0
     *
     * @param string $string
     * @param string $delimiter
     * @return string
     */
    public static function before(string $string, string $delimiter) : string
    {
        return strstr($string, $delimiter, true) ?: $string;
    }

    /**
     * Gets the portion of a string before the last occurrence of a given delimiter.
     *
     * @since 1.0.0
     *
     * @param string $string
     * @param string $delimiter
     * @return string
     */
    public static function beforeLast(string $string, string $delimiter) : string
    {
        if (0 === strpos($string, $delimiter)) {
            return '';
        }

        return strrev(self::after(strrev($string), $delimiter));
    }

    /**
     * Checks if a given string contains any of the other strings or characters passed.
     *
     * @since 1.0.0
     *
     * @param string $string
     * @param string|array $values
     * @return bool
     */
    public static function contains(string $string, $values) : bool
    {
        foreach (ArrayHelper::wrap($values) as $needle) {
            if ($needle === '') {
                continue;
            }

            if (mb_strpos($string, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Makes a string end with a specific character if it does not already.
     *
     * @since 1.0.0
     *
     * @param string $string
     * @param string $cap
     * @return string
     */
    public static function endWith(string $string, string $cap) : string
    {
        return rtrim(trim($string), $cap).$cap;
    }

    /**
     * Replaces the first instance of $search in the given subject.
     *
     * @since 1.0.0
     *
     * @param string $subject string to modify
     * @param string $search value to replace
     * @param string $replace replacement value
     * @return string
     */
    public static function replaceFirst(string $subject, string $search, string $replace) : string
    {
        if ($search === '') {
            return $subject;
        }

        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Sanitizes a string.
     *
     * @since 1.0.0
     *
     * @param string $string string to sanitize
     * @return string sanitized string
     */
    public static function sanitize(string $string) : string
    {
        return sanitize_text_field($string);
    }

    /**
     * Changes a string to snake_case.
     *
     * @NOTE: Non-alpha and non-numeric characters become delimiters
     *
     * @since 1.0.0
     *
     * @param string $string
     * @param string|null $delimiter
     * @return string
     */
    public static function snakeCase(string $string, $delimiter = '_') : string
    {
        $string = trim(preg_replace('#(?=\p{Lu}{1})#u', ' ', $string));
        $string = trim(preg_replace('/[^a-z0-9'.implode('', []).']+/i', ' ', $string));

        return strtolower(str_replace(' ', $delimiter, $string));
    }

    /**
     * Check if string starts with a given string or character.
     *
     * @TODO: When php 8 minimum switch to str_starts_with {JO: 2021-02-22}
     *
     * @since 1.0.0
     *
     * @param string $string
     * @param string $search
     * @return bool
     */
    public static function startsWith(string $string, string $search) : bool
    {
        return substr($string, 0, strlen($search)) === $search;
    }

    /**
     * Adds a trailing slash to a given string if one does not already exist.
     *
     * @since 1.0.0
     *
     * @param string $value
     * @return string
     */
    public static function trailingSlash(string $value) : string
    {
        return self::endWith($value, '/');
    }
}
