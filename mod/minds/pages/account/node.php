<?php
    
    $user = elgg_get_logged_in_user_entity();
    
    
$title = elgg_echo("register:node");

if(get_input('access_code') == 'mInDsnOdE'){
	// Display original form for logged in users
	if ($user) {

	    // create the registration url - including switching to https if configured
 	   $register_url = elgg_get_site_url() . 'action/registernode';
 	   $form_params = array(
			'action' => $register_url,
           		'class' => 'elgg-form-account',
   		 );

	    $body_params = array(
        	    'minds_user_guid' => $user->guid,
 	   );
   	 $content .= elgg_view_form('node_loggedin', $form_params, $body_params);
	} else {

   		$register_url = elgg_get_site_url() . 'action/registernewnode';
   		 $form_params = array(
       		     'action' => $register_url,
       		     'class' => 'elgg-form-account',
   		 );

   		 $body_params = array(
   	 	);
    		$content .= elgg_view_form('node', $form_params, $body_params);
	}
} else {
	 $content = '<p>Our hosted node service is only available to selected beta testers for the moment. Please create a channel to keep updated.</p>';

	$buttons = elgg_view('output/url', array('href'=>elgg_get_site_url().'register', 'text'=>elgg_echo('register'), 'class'=>'elgg-button elgg-button-action'));
	$buttons .= '<br/><br/>';
	$buttons .= elgg_view('output/url', array('href'=>elgg_get_site_url().'contact', 'text'=>elgg_echo('Contact'), 'class'=>'elgg-button elgg-button-action'));

}

$title_block = elgg_view_title($title, array('class' => 'elgg-heading-main'));
$header = <<<HTML
<div class="elgg-head clearfix">
        $title_block
</div>
HTML;

    
$body = elgg_view_layout("one_column", array('content' => '<div class="elgg-inner">'. $content . ' <br/> '.  $buttons .'</div>', 'header'=>$header));

echo elgg_view_page($title, $body);