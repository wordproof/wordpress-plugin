<?php
/**
 * Plugin Name: WordProof Timestamp
 * Plugin URI:  https://wordproof.io/wordproof-timestamp-plugin/
 * Description: Timestamp your WordPress content into the blockchain. Instant and without fees. For EOSIO, EOS, Ethereum &amp; Telos.
 * Version:     2.9.13
 * Author:      WordProof
 * Author URI:  https://wordproof.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 **/

namespace WordProofTimestamp;

if (!defined('WPINC')) {
    die();
}

// Load Composer
if (is_readable(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';

    try {
    	if (file_exists(__DIR__ . '/.env')) {
		    $dotenv = \WordProofTimestamp\Vendor\Dotenv\Dotenv::createImmutable( __DIR__ );
		    $dotenv->load();
	    }
    } catch(\Exception $e) {}

}

define('WORDPROOF_VERSION', '2.9.13');
define('WORDPROOF_SLUG', 'wordproof');
define('WORDPROOF_ROOT_FILE', __FILE__);
define('WORDPROOF_BASENAME', plugin_basename(WORDPROOF_ROOT_FILE));
define('WORDPROOF_DIR', plugin_dir_path(WORDPROOF_ROOT_FILE));
define('WORDPROOF_DIR_INC', WORDPROOF_DIR . 'includes');
define('WORDPROOF_DIR_ASSETS', WORDPROOF_DIR . 'assets');
define('WORDPROOF_DIR_JS', WORDPROOF_DIR_ASSETS . '/js');
define('WORDPROOF_DIR_CSS', WORDPROOF_DIR_ASSETS . '/css');
define('WORDPROOF_URI', plugin_dir_url(WORDPROOF_ROOT_FILE));
define('WORDPROOF_URI_ASSETS', WORDPROOF_URI . 'assets');
define('WORDPROOF_URI_JS', WORDPROOF_URI_ASSETS . '/js');
define('WORDPROOF_URI_CSS', WORDPROOF_URI_ASSETS . '/css');
define('WORDPROOF_URI_IMAGES', WORDPROOF_URI_ASSETS . '/images');
define('WORDPROOF_WSFY_ENDPOINT_ITEM', 'sites/$siteId/items/');
define('WORDPROOF_WSFY_ENDPOINT_RETRY_WEBHOOK', 'sites/$siteId/items/$postId/callback/retry');
define('WORDPROOF_WSFY_ENDPOINT_GET_ARTICLES', 'sites/$siteId/items/$postId');
define('WORDPROOF_WSFY_ENDPOINT_TOKEN_VALIDATE', 'sites/$siteId/validate/token');

define('WORDPROOF_WSFY_CRON_HOOK', 'wsfy_save_post_on_cron');

define('WORDPROOF_WSFY_API_IP', ['167.71.143.38']);
define('WORDPROOF_MY_URI', wordproof_get_env('WORDPROOF_ENDPOINT', 'https://my.wordproof.com/'));
define('WORDPROOF_API_URI', WORDPROOF_MY_URI . 'api/');

define('WORDPROOF_REST_NAMESPACE', 'wordproof-timestamp/v1');
define('WORDPROOF_REST_TIMESTAMP_ENDPOINT', 'posts');

// Web Standards
define('ARTICLE_TIMESTAMP', 'ArticleTimestamp');
define('MEDIA_OBJECT_TIMESTAMP', 'MediaObjectTimestamp');
define('PRODUCT_TIMESTAMP', 'ProductTimestamp');
define('CURRENT_TIMESTAMP_STANDARD_VERSION', '0.2.0');

// Load plugin
spl_autoload_register(function ($class = '') {
    if (!strstr($class, 'WordProofTimestamp')) {
        return;
    }
    $result = str_replace('WordProofTimestamp\\', '', $class);
    $result = str_replace('\\', '/', $result);
    require $result . '.php';
});

//Setup Plugin
require_once WORDPROOF_DIR_INC . '/core.php';
\WordProofTimestamp\Core\init();

/**
 * Return environment value, or default if false
 * @param $key
 * @param $default
 * @return mixed
 */

function wordproof_get_env($key, $default)
{
    return ( isset($_ENV[$key]) ) ? $_ENV[$key] : $default;
}

