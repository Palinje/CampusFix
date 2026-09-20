<?php

/**
 * Sanitize a string against XSS by converting special characters to HTML entities.
 *
 * @param string $data The string to sanitize.
 * @return string The sanitized string.
 */
function sanitize_string($data)
{
    if (is_null($data)) {
        return '';
    }
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Sanitize an email address.
 *
 * @param string $data The email address to sanitize.
 * @return string The sanitized email address.
 */
function sanitize_email($data)
{
    if (is_null($data)) {
        return '';
    }
    return filter_var(trim($data), FILTER_SANITIZE_EMAIL);
}

/**
 * Sanitize a URL.
 *
 * @param string $data The URL to sanitize.
 * @return string The sanitized URL.
 */
function sanitize_url($data)
{
    if (is_null($data)) {
        return '';
    }
    return filter_var(trim($data), FILTER_SANITIZE_URL);
}

/**
 * Sanitize an array of data recursively (useful for $_POST or $_GET).
 *
 * @param array $data The array to sanitize.
 * @return array The sanitized array.
 */
function sanitize_array($data)
{
    if (!is_array($data)) {
        return sanitize_string($data);
    }

    $sanitized_array = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $sanitized_array[$key] = sanitize_array($value);
        } else {
            $sanitized_array[$key] = sanitize_string($value);
        }
    }

    return $sanitized_array;
}
