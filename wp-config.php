<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ercaspay' );

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
define( 'AUTH_KEY',         'H[LSHV,c2/).2@tR*S2VV]D+?=|560x/4C]}LgVLy*C`{Ol/_Wv  7B>:80(qemw' );
define( 'SECURE_AUTH_KEY',  'H!,N+o`zB9}+U`F<Nuk~1ok]3*3X:k|<Wq$Ui|^Q]mbfIr!/pG`>w3k!GMRw[!BX' );
define( 'LOGGED_IN_KEY',    'k)F=K5Y _)UV*tw-:w(5Ds?htiG0@%^JeY:<9rj 78p|RiUx0XU}$FwN#vMZ+HY8' );
define( 'NONCE_KEY',        'ZZ[1]]&sa[hig-vici+MbuR&Jx0?h$(R0V|uRKJ1k]23z}|>JX#5{*>w$v^wvEw4' );
define( 'AUTH_SALT',        '1s3Z4i-@bKj?lU8/;{w{8*$H;<VU]lO1uV(m% :,2xakS?#&~.6T.w~YCD-vQZ`o' );
define( 'SECURE_AUTH_SALT', 'KJGxs7/x(vA(wwfT7lc;y}3azb%Tv,gRNa5m!ZhQPuYm<_4?P]lqvTC}/%C-3Iwc' );
define( 'LOGGED_IN_SALT',   'q!1c&N^999]7yF;,qM:*XLmLA}lf}M+ud]%; JMTBm%i-jZ|}+F%86nD.aQXbbqQ' );
define( 'NONCE_SALT',       '()Mt]A26_]meTp;9KzBk,ny)iHvyN.l{-Xc$.k7r0#(FjJ[im)jkz?ChK,!zNJ`#' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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