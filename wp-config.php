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
define( 'AUTH_KEY',         '>`=b^nW1v`!n[H/uZgkfpr=!I(gaEXrF-qY` aXs{cUE03r2{C^4hp]~:RhR:nA_' );
define( 'SECURE_AUTH_KEY',  'm3vd|,1ez(w&{fPn1S^;(4HB~k)^|qQ)HmH+?%8l1%^nG!p6VH][C4XPnUf8=tQ/' );
define( 'LOGGED_IN_KEY',    '2H]wrAuzt?L,l7wlD[1pRG?n$IN`v6WD2YhSb57ux_)*Nc_@Er IK*-ITQns4A6S' );
define( 'NONCE_KEY',        '^*5G/iVNlo)CdzRkUbC6+H~Z:) :nYws[.KZHk8BV xFNV Swe[C!DzrAFt!_87B' );
define( 'AUTH_SALT',        '?,>l)yX9JKX3MYuCVfCp8K]8I5z0f[y8_97py4LURAw]zrA}T0us( yS7-TjBm~,' );
define( 'SECURE_AUTH_SALT', '!LDdOu:+u,V2WBJ#L }QPAP7Sg:Ik.Vz]~EYC94/BG35<} G8i2h:5SW?|Fo%J$8' );
define( 'LOGGED_IN_SALT',   'XW:xH}SdV+]4We]*}v6#bfq]IBXki7y=QO8[V1MuM0HgF:_SacflEB,f`9_F9oSN' );
define( 'NONCE_SALT',       'r%_]<mSyexV4q/m:J(`@~iO]Hu@G`:B:W@]I-FE:0[tf|H]s^S _Hs:@#up< NNc' );

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
