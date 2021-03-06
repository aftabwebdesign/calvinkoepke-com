<?php
/*============================================
=            Begin Functions File            =
============================================*/

/**
 * Define Child Theme Constants
 *
 * @since 1.0.0
 *
 */
define( 'CHILD_THEME_NAME', 'Logic' );
define( 'CHILD_THEME_AUTHOR', 'Calvin Koepke' );
define( 'CHILD_THEME_AUTHOR_URL', 'https://calvinkoepke.com/' );
define( 'CHILD_THEME_URL', 'http://calvinkoepke.com/themes/logic/' );
define( 'CHILD_THEME_VERSION', '1.0.0' );
define( 'TEXT_DOMAIN', 'logic' );

/**
 *
 * Start the engine
 *
 * @since 1.0.0
 *
 */
include_once( get_template_directory() . '/lib/init.php');

/**
 * Set the content width.
 *
 * @since 1.0.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 740;
}

/**
 *
 * Load files in the /assets/ directory
 *
 * @since 1.0.0
 *
 */
add_action( 'wp_enqueue_scripts', 'logic_load_assets' );
function logic_load_assets() {

	// Clean up default WP Scripts.
	wp_deregister_script( 'jquery' );
	wp_deregister_script( 'wp-embed' );

	// Load theme JS.
	wp_enqueue_script( 'logic-fonts', '//use.typekit.net/xoo4gbo.js', array(), null );
	wp_enqueue_script( 'logic-theme-js', get_stylesheet_directory_uri() . '/build/js/theme.min.js', array(), CHILD_THEME_VERSION, true );

	// Load extra CSS.
	if ( is_front_page() ) {
		wp_enqueue_style( 'logic-home-styles', get_stylesheet_directory_uri() . '/build/css/front-page.min.css', array( 'logic' ), CHILD_THEME_VERSION );
	}

	wp_add_inline_script( 'logic-fonts', '(function(d) {
		var config = {
		  kitId: \'xoo4gbo\',
		  scriptTimeout: 3000,
		  async: true,
		  events: false,
		  classes: false
		},
		h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src=\'https://use.typekit.net/\'+config.kitId+\'.js\';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
	})(document);' );

	// Localize menu settings.
	wp_localize_script(
		'logic-theme-js',
		'genesis_responsive_menu',
		logic_get_responsive_menu_settings()
	);

}

/**
 * Set up the responsive menu settings for the theme.
 * @return array Array of settings.
 */
function logic_get_responsive_menu_settings() {

	$settings = array(
		'mainMenu'          => __( 'Menu', TEXT_DOMAIN ),
		'menuIconClass' => 'button',
		'menuClasses'       => array(
			'others' => array(
				'.nav-primary',
			)
		),
	);

	return $settings;

}

/**
 *
 * Add Social Share Buttons
 *
 * @since 1.0.0
 */
add_action( 'genesis_entry_content', 'logic_social_share' );
function logic_social_share() {

	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$url = urlencode( get_the_permalink() );
	$title = urlencode( get_the_title() );

	$facebook = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
	$twitter = 'https://twitter.com/home?status=' . $title . '%3A%20' . $url . '%20via%20%40cjkoepke';

	if ( is_singular( 'post' ) ) {
		printf( '
			<div class="share-it">
				<a href="%s" target="_blank" class="button" alt="Share on Twitter">Share on Twitter</a>
				<a href="%s" target="_blank" alt="Share on Facebook" class="button">Share on Facebook</a>
				<a href="#" class="button button-primary show-popup">Get Updates</a>
			</div>
		', $twitter, $facebook );
	}

}

/**
 * Add search form to header.
 *
 * @since 1.0.0
 */
add_action( 'genesis_header', 'logic_do_search_form', 5 );
function logic_do_search_form() {

	echo '<div id="header-search">';
		get_search_form();
	echo '</div>';

}

/**
 * Add extra elements.
 *
 * @since 1.0.0
 */
add_action( 'genesis_before_footer', 'logic_do_extras' );
function logic_do_extras() {

	echo '<div id="site-overlay"></div>';

}

/**
 * Add member menu.
 * @since 1.0.0
 */
add_filter( 'genesis_nav_items', 'logic_member_menu_items', 10, 2 );
add_filter( 'wp_nav_menu_items', 'logic_member_menu_items', 10, 2 );
function logic_member_menu_items( $menu, $args ) {

	if ( 'member' == $args->theme_location ) {

		$account_svg = '<svg class="svg-icon" viewBox="0 0 20 20">
							<path fill="none" d="M14.023,12.154c1.514-1.192,2.488-3.038,2.488-5.114c0-3.597-2.914-6.512-6.512-6.512
								c-3.597,0-6.512,2.916-6.512,6.512c0,2.076,0.975,3.922,2.489,5.114c-2.714,1.385-4.625,4.117-4.836,7.318h1.186
								c0.229-2.998,2.177-5.512,4.86-6.566c0.853,0.41,1.804,0.646,2.813,0.646c1.01,0,1.961-0.236,2.812-0.646
								c2.684,1.055,4.633,3.568,4.859,6.566h1.188C18.648,16.271,16.736,13.539,14.023,12.154z M10,12.367
								c-2.943,0-5.328-2.385-5.328-5.327c0-2.943,2.385-5.328,5.328-5.328c2.943,0,5.328,2.385,5.328,5.328
								C15.328,9.982,12.943,12.367,10,12.367z"></path>
						</svg>';

		$links = array();
		$string = '';

		if ( ! is_user_logged_in() ) {
			$links[] = '<a href="#login">Login</a>';
		} else {
			$links[] = '<a href="/my-account/" class="has-svg">' . $account_svg . '</a>';
		}

		foreach( $links as $link ) {
			$string .= '<li class="menu-item">' . $link . '</li>';
		}

		return $menu . $string;
	} else {
		return $menu;
	}
}

/**
 * Add widget popup.
 * @since 1.0.0
 */
add_action( 'genesis_after', 'logic_widget_popup', 20 );
function logic_widget_popup() {
	?>
	<div id="popup">
		<?php genesis_widget_area( 'popup' ); ?>
	</div>
	<?php
}

/**
 *
 * Add theme supports
 *
 * @since 1.0.0
 *
 */
add_theme_support( 'genesis-responsive-viewport' ); /* Enable Viewport Meta Tag for Mobile Devices */
add_theme_support( 'html5',  array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) ); /* HTML5 */
add_theme_support( 'genesis-accessibility', array( 'skip-links', 'search-form', 'drop-down-menu', 'headings' ) ); /* Accessibility */
// add_theme_support( 'genesis-after-entry-widget-area' );

/**
 *
 * Apply custom body classes
 *
 * @since 1.0.0
 * @uses /lib/classes.php
 *
 */
include_once( get_stylesheet_directory() . '/lib/classes.php' );

/**
 *
 * Apply Logic defaults (overrides default Genesis settings)
 *
 * @since 1.0.0
 * @uses /lib/defaults.php
 *
 */
include_once( get_stylesheet_directory() . '/lib/genesis-setup.php' );

/**
 *
 * Apply Logic default attributes
 *
 * @since 1.0.0
 * @uses /lib/attributes.php
 *
 */
include_once( get_stylesheet_directory() . '/lib/attributes.php' );

/**
 * Clean up unused scripts/styles.
 * @since 1.0.0
 */
include_once( get_stylesheet_directory() . '/lib/clean.php' );

/**
 * Register new widget areas.
 *
 * @since 1.0.0
 * @uses /lib/widgets.php
 */
include_once( get_stylesheet_directory() . '/lib/widgets.php' );
