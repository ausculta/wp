<?php 
function bootstrapwp_enqueuefiles() {
    $tempdiruri = get_template_directory_uri();
    wp_register_style('bootstrap', $tempdiruri . '/css/bootstrap.min.css' );
	// wp_register_style('endeavour', $tempdiruri . '/css/endeavour.css' );
	// $deps = array('bootstrap', 'endeavour');
	$deps = array('bootstrap');
    wp_enqueue_style( 'bootstrapwp-style', get_stylesheet_uri(), $deps ); 
    // wp_enqueue_script("jquery");
	wp_register_script( 'custom-script', $tempdiruri . '/js/bootstrap.bundle.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'custom-script' );
}

function bootstrapwp_wpsetup() {
    add_theme_support( 'title-tag' );
	require_once get_template_directory() . '/class-wp-bootstrap-navwalker.php';
	// require_once $tempdiruri . '/class-wp-bootstrap-navwalker.php';
}

function bootstrapwp_registermenus() {
    register_nav_menus( array(
                            'menuheader' => __('Header Menu'),
                            'menumain' => __('Main Menu')
                        )
    );
}

/*
 * Redirect user after successful login.
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */
function bootstrapwp_loginredirect( $redirect_to, $request, $user ) {
    //is there a user to check?
    // if ( isset( $user->roles ) && is_array( $user->roles ) ) {
    //     //check for admins
    //     if ( in_array( 'administrator', $user->roles ) ) {
    //         // redirect them to the default place
    //         return $redirect_to;
    //     } else {
    //         // return home_url();
    //         return site_url();
    //     }
    // } else {
    //     // return $redirect_to;
    //     return site_url();
    // }
    return site_url();
}

function bootstrapwp_logoutredirect( $redirect_to, $request, $user ) {
    return site_url();
}

function bootstrapwp_removeadminbar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

/* Disable WordPress Admin Bar for all users but admins. */
// show_admin_bar(false);

add_filter( 'login_redirect', 'bootstrapwp_loginredirect', 10, 3 );
add_filter( 'logout_redirect', 'bootstrapwp_logoutredirect', 10, 3 );
add_action( 'wp_enqueue_scripts', 'bootstrapwp_enqueuefiles' );
add_action('after_setup_theme', 'bootstrapwp_removeadminbar');
add_action( 'after_setup_theme', 'bootstrapwp_wpsetup' );
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