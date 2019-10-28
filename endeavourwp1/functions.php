<?php 
function bootstrapwp_enqueuefiles() {
	wp_register_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css' );
	wp_register_style('endeavour', get_template_directory_uri() . '/css/endeavour.css' );
	$deps = array('bootstrap', 'endeavour');
    wp_enqueue_style( 'bootstrapwp-style', get_stylesheet_uri(), $deps ); 
    wp_enqueue_script("jquery");
	wp_register_script( 'custom-script', get_template_directory_uri() . '/js/bootstrap.bundle.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'custom-script' );
}

function bootstrapwp_wpsetup() {
    add_theme_support( 'title-tag' );
}

function bootstrapwp_registermenus() {
    register_nav_menus( array(
                            'menuheader' => __('Header Menu'),
                            'menumain' => __('Main Menu')
                        )
    );
}

function register_navwalker(){
	require_once get_template_directory() . '/class-wp-bootstrap-navwalker.php';
}

function bootstrapwp_menuloginlogout( $items, $args ) {
	if ( $args->theme_location != 'menumain' ) {
		return $items;
	}

	if ( is_user_logged_in() ) {
		$items .= '' . __( 'Log Out' ) . '';
	} else {
		$items .= '' . __( 'Login In' ) . '';
	}

	return $items;
}

function bootstrapwp_registernavwalker(){
	require_once get_template_directory() . '/class-wp-bootstrap-navwalker.php';
}
add_action( 'wp_enqueue_scripts', 'bootstrapwp_enqueuefiles' );
add_action( 'after_setup_theme', 'bootstrapwp_wpsetup' );
add_action( 'after_setup_theme', 'bootstrapwp_registernavwalker' );
add_action( 'init', 'bootstrapwp_registermenus' );
// add_filter( 'wp_nav_menu_items', 'bootstrapwp_menuloginlogout', 199, 2 );

// if (function_exists('register_sidebar'))
// 	register_sidebar(array(
// 		'before_widget' => '',
// 		'after_widget' => '',
// 		'before_title' => '<h3>',
// 		'after_title' => '</h3>',
// 		));

// function bootstrapwp_widgetsinit() {
//     register_sidebar( array(
//         'name'          => 'Footer - Copyright Text',
//         'id'            => 'footer-copyright-text',
//         'before_widget' => '<div class="footer_copyright_text">',
//         'after_widget'  => '</div>',
//         'before_title'  => '<h4>',
//         'after_title'   => '</h4>',
//     ) );
//     register_sidebar( array(
//         'name'          => 'Sidebar - Inset',
//         'id'            => 'sidebar-1',
//         'before_widget' => '<div class="sidebar-module sidebar-module-inset">',
//         'after_widget'  => '</div>',
//         'before_title'  => '<h4>',
//         'after_title'   => '</h4>',
//     ) );
//     register_sidebar( array(
//         'name'          => 'Sidebar - Default',
//         'id'            => 'sidebar-2',
//         'before_widget' => '<div class="sidebar-module">',
//         'after_widget'  => '</div>',
//         'before_title'  => '<h4>',
//         'after_title'   => '</h4>',
//     ) );
// }
// add_action( 'widgets_init', 'bootstrapwp_widgetsinit' );

?>