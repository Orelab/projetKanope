<?php
/*
Plugin Name:test
PluginPlugin URI:http://localhost/wp-v1/wordpress/
Description: Un plugin de test
Version:0.1
Author: Moi
Author URI: http://localhost/wp-v1/wordpress/
License: GPL2
*/

// Hook for adding admin menus
add_action('admin_menu', 'mt_add_pages');

// action function for above hook
function mt_add_pages() {
    // Add a new submenu under Settings:
    add_options_page(__('Test Settings','menu-test'), __('Test Settings','menu-test'), 'manage_options', 'testsettings', 'mt_settings_page');

    // Add a new submenu under Tools:
    add_management_page( __('Test Tools','menu-test'), __('Test Tools','menu-test'), 'manage_options', 'testtools', 'mt_tools_page');

    // Add a new top-level menu (ill-advised):
    add_menu_page(__('Carte','menu-test'), __('Carte','menu-test'), 'manage_options', 'mt-top-level-handle', 'mt_toplevel_page' );

    // Add a submenu to the custom top-level menu:
    add_submenu_page('mt-top-level-handle', __('Test Sublevel','menu-test'), __('Test Sublevel','menu-test'), 'manage_options', 'sub-page', 'mt_sublevel_page');

    // Add a second submenu to the custom top-level menu:
    add_submenu_page('mt-top-level-handle', __('Test Sublevel 2','menu-test'), __('Test Sublevel 2','menu-test'), 'manage_options', 'sub-page2', 'mt_sublevel_page2');
}

// mt_settings_page() displays the page content for the Test Settings submenu
function mt_settings_page() {
    echo "<h2>" . __( 'Test Settings', 'menu-test' ) . "</h2>";
}

// mt_tools_page() displays the page content for the Test Tools submenu
function mt_tools_page() {
    echo "<h2>" . __( 'Test Tools', 'menu-test' ) . "</h2>";
}

// mt_toplevel_page() displays the page content for the custom Test Toplevel menu
function mt_toplevel_page() {
    echo "<h2>" . __( 'Menu Carte', 'menu-test' ) . "</h2>";
}

// mt_sublevel_page() displays the page content for the first submenu
// of the custom Test Toplevel menu
function mt_sublevel_page() {
    echo "<h2>" . __( 'Test Sublevel', 'menu-test' ) . "</h2>";
}

// mt_sublevel_page2() displays the page content for the second submenu
// of the custom Test Toplevel menu
function mt_sublevel_page2() {
    echo "<h2>" . __( 'Test Sublevel2', 'menu-test' ) . "</h2>";
}
function carte(){
	 echo"hello word";
}
add_shortcode('test', 'carte');






 ?>
