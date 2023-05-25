<?php
/**
 * Helper functions
 *
 * This file contains helper functions used throughout the plugin.
 *
 * @link https://github.com/mwalek/nchito-maps-redirection
 *
 * @package    WordPress
 * @subpackage Plugins
 * @since      1.0.0
 */

declare(strict_types=1);
namespace MwaleMe\Nchito_Maps_Redirection;

/**
 * Checks if a given string is present in an array.
 *
 * @param string $needle   The string to check for.
 * @param array  $haystack An array of strings to check.
 *
 * @return bool Returns true if the string is unique, false otherwise.
 */
function is_unique( string $needle, array $haystack ) : bool {
		return in_array( $needle, $haystack, true ) ? false : true;
}
