<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://mwale.me
 * @since             1.0.0
 * @package           Nchito_Maps_Redirection
 *
 * @wordpress-plugin
 * Plugin Name:       Nchito Maps Redirection
 * Plugin URI:        https://mwale.me
 * Description:       Generates short strings and uses those to create redirects that replace lengthy Google Maps URLs.
 * Version:           1.0.0
 * Author:            Mwale Kalenga
 * Author URI:        https://mwale.me
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nchito-maps-redirection
 * Domain Path:       /languages
 */

declare(strict_types=1);
namespace MwaleMe\Nchito_Maps_Redirection;

require_once 'vendor/autoload.php';
require_once 'includes/util/helpers.php';
$dotenv = \Dotenv\Dotenv::createImmutable( __DIR__ );
$dotenv->safeLoad();

$config = array(
	'username' => $_ENV['username'],
	'password' => $_ENV['password'],
	'url'      => $_ENV['url'],
);

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-nchito-maps-redirection.php';

/**
 * Begins execution of the plugin.
 *
 * @param string $username The WordPress account's username.
 * @param string $password The App password used for authentication via non-interactive systems.
 * @param string $url The url of the site running the Redirection plugin.
 * @return void
 */
function run_nchito_maps_redirection( $username, $password, $url ) {
	$plugin = new Nchito_Maps_Redirection( $username, $password, $url );
}

run_nchito_maps_redirection( ...$config );
