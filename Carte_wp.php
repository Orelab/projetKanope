<?php
/*
Plugin Name:      carte Simplon
PluginPlugin URI: http://www.simplon.co
Description:      Un plugin pour afficher les entrepreneurs sur une carte
Version:          0.2
Author:           Simplon Auch - promotion #1
Author URI:       http://www.simplon.co
License:          GPL2
*/




/*
	Appending the CSS and JS needed scripts

	https://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_scripts
*/

function simplon_enqueue_style(){
	wp_enqueue_style( 'spl-leaflet', 'https://unpkg.com/leaflet@1.1.0/dist/leaflet.css' );
	wp_enqueue_style( 'spl-cartestyle', plugins_url('carte/assets/cartestyle.css') );
}

function simplon_enqueue_script(){
	wp_enqueue_script( 'spl-leaflet', 'https://unpkg.com/leaflet@1.1.0/dist/leaflet.js' );
	wp_enqueue_script( 'spl-carto', plugins_url('carte/assets/carto.js') );
}

add_action( 'wp_enqueue_scripts', 'simplon_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'simplon_enqueue_script' );






/*
	A function to geocode the addresses

	Please, note that when an address is geocoded (aka : converted in
	latitude and longitude values), theses values should be stored in the database.

	We first try to geocode with Nominatim. If it fails, we then use Google.

	We add some imprecision in the coordinates, to allow to discern the points
	when users have the same address (see Innoparc)
*/

define("NOMINATIM", "http://nominatim.openstreetmap.org/search?format=json&q=");
define("GOOGLE", "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=");

function geocode( $address, $name='', $service=NOMINATIM ){

	$precision = 15;

	file_put_contents('nominatim.log', "$name ($address)", FILE_APPEND);

	switch($service){

		case NOMINATIM :
			$res = json_decode(file_get_contents( NOMINATIM . urlencode($address) ));

			if( isset($res[0]->lat) ){
				file_put_contents('nominatim.log', " FOUND WITH NOMINATIM\n", FILE_APPEND);
				return [
					'lat' => $res[0]->lat + (rand(-$precision,$precision)/100000),
					'lon' => $res[0]->lon + (rand(-$precision,$precision)/100000)
				];
			}
			file_put_contents('nominatim.log', " NOT FOUND WITH NOMINATIM\n", FILE_APPEND);
			break;

		case GOOGLE :
			$res = json_decode(file_get_contents( GOOGLE . urlencode($address) ));

			if( isset($res->results[0]->geometry->location->lat) ){
				file_put_contents('nominatim.log', " FOUND WITH GOOGLE\n", FILE_APPEND);
				return [
					'lat' => $res->results[0]->geometry->location->lat 
							+ (rand(-$precision,$precision)/100000),
					'lon' => $res->results[0]->geometry->location->lng
							+ (rand(-$precision,$precision)/100000)
				];
			}
			file_put_contents('nominatim.log', " NOT FOUND WITH GOOGLE\n", FILE_APPEND);
			break;
	}
	return false;
}




/*
	Search engine
	The search engine looks for POST parameters
	and generate the corresponding SQL request.
*/
function gen_sql_request(){
	global $wpdb;

	$table = $wpdb->prefix . "usermeta";

	$sql = "
	  SELECT t1.* 
	  FROM $table t1
	";

	if( isset($_REQUEST['activite']) && $_REQUEST['activite']!='')
	{
		$sql .="
		    JOIN $table t2 ON (
		  		t1.user_id = t2.user_id
		  		AND t2.meta_key = 'user_metiers'
		  		AND t2.meta_value LIKE '%".$_REQUEST['activite']."%'
		    )
		";
	}

	if( isset($_REQUEST['ville']) && $_REQUEST['ville']!='')
	{
		$sql .="
		    JOIN $table t4 ON (
		  		t1.user_id = t4.user_id
		  		AND t4.meta_key = 'user_ville'
		  		AND t4.meta_value LIKE '%".$_REQUEST['ville']."%'
		  	)
		";
	}

	if( isset($_REQUEST['codepostal']) && $_REQUEST['codepostal']!='')
	{
		$sql .="
		    JOIN $table t5 ON (
		  		t1.user_id = t5.user_id
		  		AND t5.meta_key = 'user_codepostal'
		  		AND t5.meta_value = '".$_REQUEST['codepostal']."'
		  	)
		";
	}

	if( isset($_REQUEST['departement']) && $_REQUEST['departement']!='')
	{
		$sql .="
		    JOIN $table t3 ON (
		  		t1.user_id = t3.user_id
		  		AND t3.meta_key = 'user_codepostal'
		  		AND t3.meta_value LIKE '".$_REQUEST['departement']."%'
		  	)
		";
	}

	$sql .= "
		WHERE t1.meta_key IN ('first_name', 'last_name', 'description', 
	    'user_adresse01', 'user_adresse02', 'user_codepostal', 'user_ville',
	    'phone_number', 'user_type', 'user_offre', 'user_clientele', 'user_references', 
	    'profilepicture', 'user_logo', 'gender', 'user_url',
	    'facebook', 'twitter', 'user_email', 'user_metiers',
	    'lat', 'lon'
	  )
	";

	$sql .= " ORDER BY t1.user_id;";

	return $sql;
}




/*
	Get the job list
	We retrieve the list of all used jobs from usermeta
*/
function get_job_list(){
	global $wpdb;

	$table = $wpdb->prefix . "usermeta";

	$data = $wpdb->get_results("
		SELECT *
		FROM $table
		WHERE meta_key = 'user_metiers';
	");

	$jobs=[];

	foreach($data as $d)
	{
		$jobs = array_merge( $jobs, unserialize($d->meta_value) );
	}

	$jobs = array_values(array_unique($jobs));

	sort($jobs);

	return $jobs;
}



/*
	The main program
*/

function simplon_add_carto(){
	global $wpdb;

	$sql = gen_sql_request();

	$results = $wpdb->get_results($sql);


	/*
	  Formatting properly the array of data
	*/

	$db = array();

	foreach( $results as $r ){
	  if( ! array_key_exists($r->user_id, $db) ){
	    $db[ $r->user_id ] = [];
	  }
	  $db[$r->user_id][$r->meta_key] = $r->meta_value;
	}



	/*
		Adding user ID
	*/

	foreach( $db as $uid => &$user ){
		$user['ID'] = $uid;
	}	


	/*
		Converting the field 'user_metiers' 
		from php/serialize to js/json
	*/

	foreach( $db as $uid => &$user ){
		if( isset($user['user_metiers']) ){
			$user['user_metiers'] = json_encode( unserialize($user['user_metiers']) );
		}
	}	


	/*
	  For each user who entered an address but have no lat/lon,
	  we use Google to retrieve lat/lon and save it to the database.
	*/

	foreach( $db as $uid => &$user ){

	  if( ! isset($user['lat']) or ! isset($user['lon']) ){

	  	$name = $user['first_name'].' '.$user['last_name'];

	  	$addr = (array_key_exists('user_adresse01',$user) ? $user['user_adresse01'].' ' : '')
	  		. (array_key_exists('user_adresse02',$user) ? $user['user_adresse02'].' ' : '')
	  		. (array_key_exists('user_codepostal',$user) ? $user['user_codepostal'].' ' : '')
	  		. (array_key_exists('user_ville',$user) ? $user['user_ville'] : '');

		if( trim($addr) != '' ){

			$coords = geocode($addr, $name, NOMINATIM);

			if( ! $coords ){
				$coords = geocode($addr, $name, GOOGLE);
			}

			if( ! $coords ){
				// Neither NOMINATIM, nor GOOGLE found the address coords
				// So we bypass the database recording step...
				break;
			}

			$wpdb->insert( $wpdb->prefix . "usermeta", array(
				'user_id'   => $uid,
				'meta_key'  => 'lat',
				'meta_value'=> $coords['lat']
			));
			$user['lat'] = $coords['lat'];

			$wpdb->insert( $wpdb->prefix . "usermeta", array(
				'user_id'   => $uid,
				'meta_key'  => 'lon',
				'meta_value'=> $coords['lon']
			));
			$user['lon'] = $coords['lon'];

	    }
	  }
	}


	/*
	  Generating the HTML
	*/

	$listaddressjson = json_encode( array_values($db) );

	$joblist = get_job_list();

	require 'assets/html.php';
}

add_shortcode('cartographie', 'simplon_add_carto');







 

