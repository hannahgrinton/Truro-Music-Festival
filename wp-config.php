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
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'user' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Nf8BmaluHw7anbd1' );

/** MySQL hostname */
define( 'DB_HOST', 'ec2-35-175-245-35.compute-1.amazonaws.com' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '88`Z;&^l&aAnfid4_Jc))o-j?9T;FgUBKkNj#m3*6Sx@L7`rSXaQs@`6h{lH< (%' );
define( 'SECURE_AUTH_KEY',  'MlWLt]2sk#wE+qk5w66dGl{e>l}eh&hD&%mE7T}|kS>#kRv1gw&j;s$h~}n2:bK?' );
define( 'LOGGED_IN_KEY',    'i:X?hl~>?N(Nj3RNJDN^EbEJCCs8|-uk~9PA(nuJzI:@}t#M=JR3Mq&67TM,9Jhz' );
define( 'NONCE_KEY',        'J6M4{QQpeZ:4KS- K1p^};(2@hixYaQ;ct> wX39Jcha.Ajzx4=i*w$brTG_{|C&' );
define( 'AUTH_SALT',        '#jDh9cI@mUx6ZA$j3bL5u<9~0XCx@AHq=u=P!a73NW_S!7!A&2AqB=l+IZZ?kef8' );
define( 'SECURE_AUTH_SALT', '9HY~D|.<0U0+C*UX_yDvQ1d/eb*CYH{N4W2|WSvX?#Q00<ol6HT_UTB)$^Sk<1(y' );
define( 'LOGGED_IN_SALT',   'L l1p_AaKPz:H6>)lIgN@J4XPo0QNxthcd|CKCR_<yqHFnW0D`PL+60Z~q8rm(8|' );
define( 'NONCE_SALT',       '.qc2<lG,X&&&_||kGh8tr*e2>@dqX 0^-uEIFNzwK+NbAR?ipZobZ:^LG-~F&`E6' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
