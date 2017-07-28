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


function carte(){
	 include 'carte.php';
	 include 'moteur_recherche.php';

}
add_shortcode('cartographie', 'carte');






 ?>
