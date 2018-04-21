<?php
//Loads configuration from database into global CI config
function load_config()
{
    $CI =& get_instance();

    $CI->load->model('settings/Setting');
    $CI->load->library('auth/tank_auth');

    foreach( $CI->Setting->get_all($CI->tank_auth->get_license_key())->result() as $app_config )
    {
        $CI->config->set_item( $app_config->key, $app_config->value );
    }
    
    
    //Set timezone from config database
    if ( $CI->config->item( 'timezone' ) )
    {
        date_default_timezone_set( $CI->config->item( 'timezone' ) );
    }
    else
    {
        date_default_timezone_set( 'Asia/Hong_Kong' );
    }
	
	//Set dateformat from config database
    if ( $CI->config->item( 'dateformat' ) == '')
    {
		$CI->config->set_item( 'dateformat', 'd/m/Y');
    }
    
	//Set dateformat from config database
    if ( $CI->config->item( 'timeformat' ) == '' )
    {
		$CI->config->set_item( 'timeformat', 'h:i:s a' );
    }
}
?>