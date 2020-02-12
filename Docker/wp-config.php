<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'endvrwpdb1');

/** MySQL database username */
define( 'DB_USER', 'wpendeavour@endvrdb1');

/** MySQL database password */
define( 'DB_PASSWORD', '1#aqkfJAG3V2YqpI!57GmrqS#0JgVhY9@7A12RQs');

/** MySQL hostname */
define( 'DB_HOST', 'endvrdb1.mysql.database.azure.com');

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '77cf2cf1578d74b70601c6b6e9147700ede2a73d');
define( 'SECURE_AUTH_KEY',  'c64787599552ecf91448ecc3b181b13e2f4f3561');
define( 'LOGGED_IN_KEY',    '58b1928ad1bac354bd4394ae00a26e7063de22ed');
define( 'NONCE_KEY',        '7cbe57afa1b51e5e95725534d804af5e0fa52d98');
define( 'AUTH_SALT',        'e4f320170979f6576c99bd80d5ac4967b608e1df');
define( 'SECURE_AUTH_SALT', 'b71afb9c4f17c7355d5ed61071086747153753ec');
define( 'LOGGED_IN_SALT',   '822b289918332f5cefe4b3a1206ec699a1749de2');
define( 'NONCE_SALT',       '9b8ad8e914487a4e4340e969db5d22fa6417f187');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'edvr1_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false);
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', false );
define( 'CONCATENATE_SCRIPTS', true );
define( 'WP_MEMORY_LIMIT', '96M' );
define( 'WP_MAX_MEMORY_LIMIT', '256M' );
define( 'WP_ALLOW_MULTISITE', true );
define( 'WP_CACHE', true);
define( 'WPCACHEHOME', dirname(__FILE__) . '/wp-content/plugins/wp-super-cache/');

// If we're behind a proxy server and using HTTPS, we need to alert Wordpress of that fact
// see also http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

define('WP_HOME', "https://endeavouresu.uk/");
define('WP_SITEURL', "https://endeavouresu.uk/");
define( 'COOKIE_DOMAIN', 'endeavouresu.uk' );

// Force SSL redirect
define('FORCE_SSL', true); 
define('FORCE_SSL_ADMIN', true); 
if ( isset($_SERVER['HTTP_X_ARR_SSL']) ) 
        $_SERVER['HTTPS']='on';

// Force SSL for MySQL connections
define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);
define('MYSQL_SSL_CA_PATH', '/');
define('MYSQL_SSL_CA', '/website/BaltimoreCyberTrustRoot.crt.pem');
define('DB_SSL', true);

// Email setup
define( 'SMTP_HOST', 'smtp.office365.com' ); 
define( 'SMTP_AUTH', true );
define( 'SMTP_PORT', '465' );
define( 'SMTP_SECURE', 'ssl' );
define( 'SMTP_USERNAME', 'website@endeavouresu.uk' );  // Username for SMTP authentication
define( 'SMTP_PASSWORD', 'YO^5cNA6Jc$x36pC2hPMpNK4y705LI7Ud!$n4d9z' );          // Password for SMTP authentication
define( 'SMTP_FROM',     'website@endeavouresu.uk' );  // SMTP From address
define( 'SMTP_FROMNAME', 'Endeavour ESU' );         // SMTP From name

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
