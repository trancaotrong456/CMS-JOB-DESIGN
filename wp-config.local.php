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
define( 'DB_NAME', 'cms_job_design' );
// define( 'DB_NAME', 'wordpress_trancaotrong' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'Z)[?~5mY<aFk,?f[~h5.$)e1eNnLlvTl<^ 5QX&W&Gj6}hg$Z1r~=}w+<.KY^:IZ' );
define( 'SECURE_AUTH_KEY',  '~JL8_kSc*[rkAhzzK~#jf7KM7<#ShOd=}911r[7J~,].AT^G{#w 3LhKHsb|.S4O' );
define( 'LOGGED_IN_KEY',    'i{-g_?5^>I{Vw2;b935lV/#D(g]lJw/!l[|*vz#<Cdi}9|zePG/J$WH$dc+lmT)T' );
define( 'NONCE_KEY',        'z(.-q!Uv;>E<EMv({.HW<gv@>4gxHpS0qB(4 w@r ^~unuy_(kS}^T=VRdfZo.!`' );
define( 'AUTH_SALT',        'uSN7B,0[re;h|jW!Kk8Z!K]VvH2^`` =be?5~u,gjeACz#JCNGgn1 ]<|5xOB!*`' );
define( 'SECURE_AUTH_SALT', 'I^v%{oJ6{teeJ-j,ku&)(Ez:lO<eCl+QM;DV168BYzRW_lU>BTwnuLPokulU >+-' );
define( 'LOGGED_IN_SALT',   'nOXD|Qvuy4)aqz#plWP62Osex|Io #|<dXuiHJQ]kJTUUV[<qYkK%?p>l+WnCyY&' );
define( 'NONCE_SALT',       '6ybP#J=:e&^@{aK) [;Yy,?xoP!:4VN&Y (;rS-a};oik%uM|<[zwl%R]Gjjx3_|' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
