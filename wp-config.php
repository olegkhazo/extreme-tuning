<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'extreme_tuning' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define ('WPLANG', 'ru_RU');
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'LQS)Jj,5PZXhO665jakvV_Oy/m5b^5=IwYE7[SILWx&$xKyu(O]+uZkOg3+^Gb}.' );
define( 'SECURE_AUTH_KEY',  'LS}~st6Ng(l$jDnZ4[lZaJ1hdqNAQCP?LIvz9jz1k,$i~s6/hD7S=]:6$eoLV3S$' );
define( 'LOGGED_IN_KEY',    'M G#HZFu;.B~2R*=X%!-SE(=^|N>bAgDw8q-Iwt=gemY!rPMoRyZ4)~~!Xmb(D9-' );
define( 'NONCE_KEY',        'xs`caZtD$WZxS4a|GV^7cdqn^`I6H/8CLsS4O8HcGCo<DJrl ?66w+S!tYbanR1/' );
define( 'AUTH_SALT',        'hpSA>BKF+dH8CX}fDUw?%L83*;I0=w>qao# ,faTS=zI`nuTR(~S[MbF/F={RqIB' );
define( 'SECURE_AUTH_SALT', ')0}2;C1C^|DL+DnQ6M<j,E>yj@d.~V_K~KD!i<NCasL0?A~TWxM?r>m9/yeo>1:I' );
define( 'LOGGED_IN_SALT',   '{0,PDVMl(ORfg-O1c5/5,jKO qY]CF[[~PCsYEIp0cm8An()Jk,oASN]5lx,C/I>' );
define( 'NONCE_SALT',       'wBrROlA)z=AYGB8`GlL4J+h@U6KQ=H(CF?$wi6Ek|7D4t<,Sc=2.G~i/p(HnbSb!' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'extu_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
