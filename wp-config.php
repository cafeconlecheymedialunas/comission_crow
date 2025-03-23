<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'agentcommission' );


/** Database username */
define( 'DB_USER', 'myagentcom04' );


/** Database password */
define( 'DB_PASSWORD', 'sg09hCsp' );


/** Database hostname */
define( 'DB_HOST', 'localhost' );


/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );


/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '8u+v@rZ =JIpYuHqWsaTrP%m|/z[p0|1wiz}]!KB8PUiJ&w.|{;LX`Wj6b3ldRfZ' );

define( 'SECURE_AUTH_KEY',  '2f(Sdl_7eu-_G& p}-_>NjAW6=;!mqt`i{T}z0iH~4XAPf&Ulca%an~}0Qu-7O#(' );

define( 'LOGGED_IN_KEY',    '5jn$wkKwI>)H<.Vdg;?0sXIp$uKRy(/<oy@g@u:yY(j/]f`.kSJ FW}T`6Sq9Y-&' );

define( 'NONCE_KEY',        'f<1TRHA|,4TvS[AS{(#=8<dboo}v]_qqu!#nCds$}Wqi_*e+$DigE[&X[HlT9);y' );

define( 'AUTH_SALT',        'S&RFOj?h2n)nc9#:&kK78p]P*<EEY=-:(+UI:d`d@l@}$x!}ahZ+Sh%$Llr-n;Wy' );

define( 'SECURE_AUTH_SALT', '.1QoeYapi6@Gb{NCXB e}-Bvbn_>++3 U+7,W6LE{(w=~;O2s]721{x5dYBMoW$y' );

define( 'LOGGED_IN_SALT',   '_KE+C#<y5T$6vf&YJ8#Arj#[my[p={No,HG30V{M1MtCr>&l&/O9a:BIYXoTEC9}' );

define( 'NONCE_SALT',       'utH|;-A|/bS`U]d#x;I^9h)hn3d0*y^W4,~swYv>C^4@3Bz<m_u*ZJxu=5xdVr4*' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
