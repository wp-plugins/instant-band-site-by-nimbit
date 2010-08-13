<?php
/*
 Plugin Name: Instant Band Site by Nimbit
 Plugin URI: http://wordpress.org/extend/plugins/instant-band-site-by-nimbit/
 Description: With four easy steps this plugin will set you up with a complete site for your music.  Once You are done you will have combined the smartest website software with the best music business platform.
 Version: 0.1.6
 Author: Nimbit
 Author URI:
 License: GPL2
 
 Copyright 2010 Sarah Sprague of Nimbit (email : sarah@nimbit.com)
 This file is part of Instant Band Sites by Nimbit.
 
 Instant Band Site by Nimbit is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 (at your option) any later version.

 Instant Band Site by Nimbit is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Instant Band Site by Nimbit.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
 IMPORTANT: 
 IF YOU WANT TO EDIT YOUR NIMBIT CONTENT TOOLS YOU CANNOT DO IT HERE
 YOU MUST SIGN INTO YOUR NIMBIT ACCOUNT AND EDIT THE CONTENT IN YOUR DASHBOARD
*/

/* function : if the user is an admin then set up Nimbit admin menu
*/
if ( is_admin() ){
	//Call the html code 
	add_action('admin_menu', 'nimbit_admin_menu');

	// Call the Plugin Interface Page Code
	add_action('admin_menu', 'nimbit_admin_menu');

	//create Nimbit admin menu
	function nimbit_admin_menu() {
		add_menu_page('Nimbit', 'Nimbit', 'administrator', 'nimbit-admin', 'nimbit_plugin_page', plugins_url($path = '/instant-band-site-by-nimbit').'/images/whitelogo.jpg');
		//add_submenu_page( 'nimbit-admin', 'nimbit-dash', 'Nimbit Dash' , 'administrator', 'nimbit-dash', 'nimbit_dash');
	}
}


add_action('wp_dashboard_setup', 'nimbit_wp_dashboard_setup');

/**
 * add Dashboard Widget via function wp_add_dashboard_widget()
 */
function nimbit_wp_dashboard_setup() {
	wp_add_dashboard_widget( 'nimbit_wp_dashboard', __( 'Nimbit Plugin' ), 'nimbit_wp_dashboard' );
}

/**
 * Content of Dashboard-Widget
 */
function nimbit_wp_dashboard() {
	require('nimbit-dash.php');
}


/* input : title and content of page you would like to create
   output : none
   function : this function is called once the user checks which
   pages they would like to create.  It checks to make sure that
   Nimbit hasn't already created the same page for the user first
*/
function create_given_page($pagetitle, $pagecontent){
	//check if they already have this Nimbit page
	$arguments = array();
	$arguments['meta_key']= 'pagetype';
	$arguments['meta_value']= 'nimbitplug';
	$results = get_pages($arguments);
	$check = 0;
	if(!empty($results)){
		foreach($results as $res){
			$meta_value = get_post_meta($res->ID, 'pagename', true);
			if($meta_value == $pagetitle){
				$check = 1;
			}
		}
	}
	if ($check==0){ //if this page doesn't already exist then create page 
		$warning = "<!--IMPORTANT: Do not edit this content tag.-->";
		//create page object
		$given_page = array();
		$given_page['comment_status'] = 'open';
		$given_page['post_content'] = $warning.''.$pagecontent;
		$given_page['post_status'] = 'publish';
		$given_page['post_title'] = $pagetitle;
		$given_page['post_type'] = 'page';
		//insert page
		$page_id = wp_insert_post($given_page);
		//create custom field in order to be able to check later
		add_post_meta($page_id, 'pagetype', 'nimbitplug');
		add_post_meta($page_id, 'pagename', $pagetitle);
	}
}

/* function : is called when plugin is activated and creates
   username and artist options so can be remembered inbetween 
   sessions by the DB
*/
function nimbit_install() {
	//Creates new database field
	add_option('nimbit_pages', 'nopages', 'This is the Nimbit.', 'yes');
	add_option('nimbit_page', '', 'This is the Nimbit.', 'yes');
	add_option("nimbit_username", 'username', 'This is the Nimbit plugin panel data.', 'yes');
	add_option('nimbit_artist', 'yourartist', 'This is the Nimbit.', 'yes');
	add_option('nimbit_delete', 'not', 'this is nimbit', 'yes');
}
//call nimbit_install when plugin activated
register_activation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php','nimbit_install');

/* function : is called when plugin is deactivated and deletes the
   username and artist options that were created by plugin
   on activation
*/
function nimbit_remove() {
	//Deletes the database field 
	delete_option('nimbit_username');
	delete_option('nimbit_artist');
	delete_option('nimbit_pages');
	delete_option('nimbit_delete');
	delete_option('nimbit_page');
	
}
//call nimbit_remove when plugin deactivated
register_deactivation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', 'nimbit_remove' );

/* function : create main plugin page
*/

function nimbit_plugin_page() {
	print('<div class="wrap">');
	print('<h2>Nimbit Options Page</h2>');

	//called when a user wants to change Nimbit username and delete all Nimbit pages to start over
	if(isset($_POST['reset'])){
		update_username_option();
		$arguments = array();
		$arguments['meta_key']= 'pagetype';
		$arguments['meta_value']= 'nimbitplug';
		$results = get_pages($arguments);
		foreach ($results as $page){
			wp_delete_post($page->ID, $force_delete = false);
		}
	}

	if(isset($_POST['change'])){ //clear nimbit username
		update_username_option();
	}
	
	//if a nimbit username hasn't been entered yet 
	//then display text box for user to enter it
	if(get_option('nimbit_username')=='username'){?><div id="poststuff"> 
	<p><strong>Once You are done you will have combined the smartest website software with the best music business platform.</strong></p>
	<div class="postbox"> 
		    <h3 class="hndle"><span>Create Your Site Now</span></h3> 
		    <div class="inside">
		<form method="post" action="options.php"><?php wp_nonce_field('update-options'); 
		?><table>
		<tr>
			<td width="100" scope="row"><strong>Step 1</strong></td>
		</tr>
		<tr>
			<td width="200">Enter Your Nimbit Username:</td>
			<td width="200"><input name="nimbit_username" type="text" id="nimbit_username" value="<?php echo get_option('nimbit_username'); ?>" /></td>

			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="nimbit_username" />
			<p>
			<td width="300">
			<input class='button-primary' type="submit" value="<?php _e('Continue') ?>" />
			</td>
			<td>
			Sign up for a free Nimbit account <a href="http://www.nimbit.com/plans-pricing/?wpdash">here</a>.
			</td>
		</tr>
		</table>
		</p>
		</form>
		</div>
	</div><?php
	}else{//otherwise give them the option to change their username and select which artist they would like
	if(get_option('nimbit_artist')=='yourartist'){//if haven't chosen artist yet
		print('<p><strong>Once You are done you will have combined the smartest website software with the best music business platform.</strong></p><div id="poststuff"> 
	<div class="postbox"> 
		    <h3 class="hndle"><span>Create Your Site Now</span></h3> 
		    <div class="inside"><table><tr><td width="100"><strong>Step 1</strong></td></tr><tr><td width="300">Your Nimbit username is ');
?><strong><?php print(get_option('nimbit_username')); ?></strong>.</td>
		<form action='admin.php?page=nimbit-admin' method='post'>
		<input type="hidden" name='change' value='true' />
		<td><input class="button-primary" type="submit" value="change username" /></td>
		</tr>
		</form>
		</table><?php
		//finds artist names associated with Nimbit username
		$bandname = get_option('nimbit_username');
		$jsonurl = 'http://members.nimbit.com/nrp/foldername.php?partner=myspace&username='.$bandname.'';
		$json = file_get_contents($jsonurl);
		$jsonresult = json_decode($json, true);
		$count = 0;
		$artistname = array();
		$dirname = array();
		foreach ($jsonresult as $key => $value){
			$artistname[$count] = $value['artist_name'];
			$dirname[$count] = $value['dirname'];
			$count++;
		}
		if($count > 0){ //if there are artists associated with this username then create drop down menu
		?><form method="post" action="options.php"><?php
		wp_nonce_field('update-options'); ?><table>
			<tr>
				<td width="100"><strong>Step 2</strong></td>
			</tr>
			<tr>
				<td width="100" scope="row">Select Your Artist:</td>
				<td width="200"><select name="nimbit_artist" id="nimbit_username"><?php
				foreach($artistname as $a => $artist){
					?><option value="<?php print($dirname[$a]);?>"><?php print($artist); ?></option><?php
				}
				?></select></td>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="nimbit_artist" />
				<td><input class="button-primary" type="submit" value="<?php _e('continue') ?>" /></td></tr>
				</p>
				</tr>
				
				</table>
			</form>
		</div>
	</div>
</div><?php
		}else{ //if not inform user there are no artists
			print('</div></div></div><div id="poststuff"> 
	<div class="postbox"> 
		    <h3 class="hndle"><span>Sorry</span></h3> 
		    <div class="inside"><p id="alert_no_artist" ><strong>There are no activated artists associated with this username.</strong></p><p> You can either sign into your Nimbit dashboard to activate these accounts <a href="http://members.nimbit.com/dashboard/">here</a> or change the username above.</p></div></div></div>');
		}
		
	}else{//once the user has selected an artist and clicks continue they are brought to the main page where they can select what pages they want to create
		require('admin-page.php');
			}
		}
	}
	

/* resets nimbit_username option and nimbit_artist option
*/
function update_username_option(){
	update_option('nimbit_username' , 'username');
	update_option('nimbit_artist', 'yourartist');
	update_option('nimbit_pages', 'nopages');
}


/* input: the title of the page you are creating
   output: the content tag of the page you are creating
   function: generates content tags for given artist
   called upon activation
   IMPORTANT: CHANGING THESE CONTENT TAGS WILL NOT CHANGE YOUR NIMBIT CONTENT, IT WILL BREAK THEM
   IF YOU WOULD LIKE TO CHANGE THESE TAGS LOGIN TO YOUR NIMBIT DASHBOARD
*/
function nimbit_get_content($pagetitle){
	$bandname = get_option('nimbit_artist');
	$photo = '<script src="http://members.nimbit.com/tags/javascript/artists/'.$bandname.'/photo/original/"></script>';
	$bio = '<p><script src="http://members.nimbit.com/tags/javascript/artists/'.$bandname.'/profile"></script></p>';
	$calendar = '<script src="http://members.nimbit.com/tags/javascript/artists/'.$bandname.'/calendar?nrt_mode=true&previous=-2&direction=1"></script>';
	$news = '<script src="http://members.nimbit.com/tags/javascript/artists/'.$bandname.'/news"></script>';
	$skin = '<div id="embed_parent">
 <script src="http://www.nimbitmusic.com/nrp/includes/javascript/flashembed.js"></script>
 <script>expandedStore(\'embed_parent\', \'http://www.nimbitmusic.com/nes/'.$bandname.'/stores/ES\', 600, 600);</script>
 <noscript>
  <embed src=\'http://www.nimbitmusic.com/nes/'.$bandname.'/stores/ES\'
         pluginspage=\'http://www.macromedia.com/go/getflashplayer\' wmode=\'transparent\'
         allowScriptAccess=\'always\' type=\'application/x-shockwave-flash\' width=\'600\' height=\'600\'></embed>
 </noscript>
</div>';
	$content = array();
	$content['Store'] = $skin;
	$content['Events'] = $calendar;
	$content['Photos'] = '';
	$content['Bio'] = $photo.$bio;
	$content['News'] = $news;

	$requested_content = $content[$pagetitle];
	return $requested_content;
}

//Nimbit Subscribe Widget
//error_reporting(E_ALL);
add_action("widgets_init", array('nimbit_subscribe', 'register'));
register_activation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_subscribe', 'activate'));
register_deactivation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_subscribe', 'deactivate'));
class nimbit_subscribe {
	function activate(){
		$data = array( 'title' => 'Email Signup');
		if ( ! get_option('nimbit_subscribe')){
			add_option('nimbit_subscribe' , $data);
		}else {
			update_option('nimbit_subscribe' , $data);
		}
	}
	function deactivate(){
		delete_option('nimbit_subscribe');
	}
	
	function control(){
	$data = get_option('nimbit_subscribe');
	?><p>Title: <input name="nimbit_subscribe_title" type="text" value="<?php print $data['title']; ?>" /></p><?php
		if (isset($_POST['nimbit_subscribe_title'])){
			$data['title'] = attribute_escape($_POST['nimbit_subscribe_title']);
			update_option('nimbit_subscribe', $data);
		}
	}
	
	function widget($args){
		$data = get_option('nimbit_subscribe');
		echo $args['before_widget'];
		echo $args['before_title'] . $data['title'] . $args['after_title'];
		$data = get_option('nimbit_artist');
		echo '<p>Sign up for our email list:</p>';
		echo '<script src="http://members.nimbit.com/tags/javascript/artists/'.$data.'/subscribe/"></script>';
		echo $args['after_widget'];
	}
	function register(){
		register_sidebar_widget('Nimbit Email Signup', array('nimbit_subscribe', 'widget'));
		register_widget_control('Nimbit Email Signup', array('nimbit_subscribe', 'control'));
	}
}

//Nimbit Next Gig Widget
add_action("widgets_init", array('nimbit_nextgig', 'register'));
register_activation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_nextgig', 'activate'));
register_deactivation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_nextgig', 'deactivate'));
class nimbit_nextgig {
	function activate(){
		$data = array( 'title' => 'Next Gig');
		if ( ! get_option('nimbit_nextgig')){
			add_option('nimbit_nextgig' , $data);
		}else {
			update_option('nimbit_nextgig' , $data);
		}
	}
	function deactivate(){
		delete_option('nimbit_nextgig');
	}
	function control(){
	$data = get_option('nimbit_nextgig');
	?><p>Title: <input name="nimbit_nextgig_title" type="text" value="<?php print $data['title']; ?>" /></p><?php
		if (isset($_POST['nimbit_nextgig_title'])){
			$data['title'] = attribute_escape($_POST['nimbit_nextgig_title']);
			update_option('nimbit_nextgig', $data);
		}
	}
	function widget($args){
		$data = get_option('nimbit_nextgig');
		echo $args['before_widget'];
		echo $args['before_title'] . $data['title'] . $args['after_title'];
		$data = get_option('nimbit_artist');
		echo '<script src="http://members.nimbit.com/tags/javascript/artists/'.$data.'/calendar?nrt_mode=true&previous=-2&upcoming=-1"></script>';
		echo $args['after_widget'];
	}
	function register(){
		register_sidebar_widget('Nimbit Next Gig', array('nimbit_nextgig', 'widget'));
		register_widget_control('Nimbit Next Gig', array('nimbit_nextgig', 'control'));
	}
}
//promo widget
add_action("widgets_init", array('nimbit_promo', 'register'));
register_activation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_promo', 'activate'));
register_deactivation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_promo', 'deactivate'));
class nimbit_promo {
	function activate(){
		$data = array( 'title' => 'Promo Code');
		if ( ! get_option('nimbit_promo')){
			add_option('nimbit_promo' , $data);
		}else {
			update_option('nimbit_promo' , $data);
		}
	}
	function deactivate(){
		delete_option('nimbit_promo');
	}
	function control(){
	$data = get_option('nimbit_promo');
	?><p>Title: <input name="nimbit_promo_title" type="text" value="<?php print $data['title']; ?>" /></p><?php
		if (isset($_POST['nimbit_promo_title'])){
			$data['title'] = attribute_escape($_POST['nimbit_promo_title']);
			update_option('nimbit_promo', $data);
		}
	}
	function widget($args){
	$data = get_option('nimbit_promo');
		echo $args['before_widget'];
		echo $args['before_title'] . $data['title'] . $args['after_title'];
		$data = get_option('nimbit_artist');
		echo 'Enter your promotional code here:';
		echo '<form method="post" action="http://www.nimbitmusic.com/nrp/controllers/download_card.php">
				<input type="hidden" name="dirname" value="'.$data.'"/>
				<input type="text" size="20" name="code" />
				<input type="submit" value="Redeem Code" />
			</form>';
		echo $args['after_widget'];
	}
	function register(){
		register_sidebar_widget('Nimbit Promo Code', array('nimbit_promo', 'widget'));
		register_widget_control('Nimbit Promo Code', array('nimbit_promo', 'control'));
	}
}
//facebook widget
add_action("widgets_init", array('nimbit_fb', 'register'));
register_activation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_fb', 'activate'));
register_deactivation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_fb', 'deactivate'));
class nimbit_fb {
	function activate(){
		$data = array( 'option1' => 'Default value');
		if ( ! get_option('nimbit_fb')){
			add_option('nimbit_fb' , $data);
		}else {
			update_option('nimbit_fb' , $data);
		}
	}
	function deactivate(){
		delete_option('nimbit_fb');
	}
	function control(){
	}
	function widget($args){
		echo $args['before_widget'];
		echo $args['before_title'] . '' . $args['after_title'];
		$data = get_option('nimbit_artist');
		echo '<iframe src="http://www.facebook.com/widgets/like.php?href='.get_bloginfo('wpurl').'"
        scrolling="no" frameborder="0"
        style="border:none; width:400px; height:30px"></iframe>
			</form>';
		echo $args['after_widget'];
	}
	function register(){
		register_sidebar_widget('Nimbit Facebook', array('nimbit_fb', 'widget'));
		register_widget_control('Nimbit Facebook', array('nimbit_fb', 'control'));
	}
}
//player widget
add_action("widgets_init", array('nimbit_player', 'register'));
register_activation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_player', 'activate'));
register_deactivation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_player', 'deactivate'));
class nimbit_player {
	function activate(){
		$data = array( 'option1' => 'yes');
		if( !get_option('nimbit_player')){
			add_option('nimbit_player' , $data);
		}else{
		update_option('nimbit_player' , $data);
		}	
	}
	function deactivate(){
		delete_option('nimbit_player');
	}
	function control(){
		$data = get_option('nimbit_player');
		$checked = array('yes'=>'','no'=>'');
		if($data['option1']=='yes'){
			$checked['yes']='CHECKED';
			$checked['no']='';
		}else{
			$checked['no']='CHECKED';
			$checked['yes']='';
		}
		?><p>Do you want the player to autoplay?</p></p><label>Yes<input name="nimbit_player_option1" type="radio" value="yes"<?php print $checked['yes']; ?>/></label>
		<label>No<input name="nimbit_player_option1" type="radio" value="no"<?php print $checked['no']; ?>/></label></p><?php
		if (isset($_POST['nimbit_player_option1'])){
			$data['option1'] = attribute_escape($_POST['nimbit_player_option1']);
			update_option('nimbit_player', $data);
		}
	}	
	function widget($args){
		$artist = get_option('nimbit_artist');
		$songs = '';
		$url = 'http://www.nimbitmusic.com/artistdata/'.$artist.'/stores/PS/';
		$xml = simplexml_load_file($url);
		$count = 0;
		$resulttwo = $xml->xpath('//response/RecordCompany/Artist/Catalog/Product/SongTitles/SongTitle/Name');
		$resultthree = $xml->xpath('//response/RecordCompany/Artist/Catalog/Product/SongTitles/SongTitle/SampleFile');
		foreach($resultthree as $r => $result){
			$songs .= '<a style="opacity:0.0;" onclick="return false;" href="http://www.nimbitmusic.com'.$resultthree[$r].'" />'.$resulttwo[$r].'</a>';
		}
	$data = get_option('nimbit_player');
	if($data['option1']=='yes'){
		$player_code='<script type="text/javascript" src="http://o.aolcdn.com/art/merge?f=/_media/sp/sp-player.js&f=/_media/sp/sp-player-other.js&expsec=86400&ver=11&autoplay=true"></script>'.$songs;
	}else{
		$player_code='<script type="text/javascript" src="http://o.aolcdn.com/art/merge?f=/_media/sp/sp-player.js&f=/_media/sp/sp-player-other.js&expsec=86400&ver=11"></script>'.$songs;
	}
	echo $args['before_widget'];
    echo $args['before_title'] . '' . $args['after_title'];
	echo $player_code;
    echo $args['after_widget'];
	}
	function register(){
		register_sidebar_widget('Nimbit Player', array('nimbit_player', 'widget'));
		register_widget_control('Nimbit Player', array('nimbit_player', 'control'));
	}
}
//social networking
add_action("widgets_init", array('nimbit_social', 'register'));
register_activation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_social', 'activate'));
register_deactivation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_social', 'deactivate'));
class nimbit_social {
	function activate(){
		$data = array( 'title'=>'', 'twitter' => 'no', 'twitteruser' => '', 'myspace' => 'no', 'myspaceuser' => '', 'facebook' => 'no', 'facebookuser' => '', 'youtube' => 'no', 'youtubeuser' => '');
		if( !get_option('nimbit_social')){
			add_option('nimbit_social' , $data);
		}else{
		update_option('nimbit_social' , $data);
		}	
	}
	function deactivate(){
		delete_option('nimbit_social');
	}
	function control(){
		$data = get_option('nimbit_social');
		$twitter = array('yes'=>'','no'=>'');
		if($data['twitter']=='yes'){
			$twitter['yes']='CHECKED';
			$twitter['no']='';
		}else{
			$twitter['no']='CHECKED';
			$twitter['yes']='';
		}
		$myspace = array('yes'=>'','no'=>'');
		if($data['myspace']=='yes'){
			$myspace['yes']='CHECKED';
			$myspace['no']='';
		}else{
			$myspace['no']='CHECKED';
			$myspace['yes']='';
		}
		$facebook = array('yes'=>'','no'=>'');
		if($data['facebook']=='yes'){
			$facebook['yes']='CHECKED';
			$facebook['no']='';
		}else{
			$facebook['no']='CHECKED';
			$facebook['yes']='';
		}
		$youtube = array('yes'=>'','no'=>'');
		if($data['youtube']=='yes'){
			$youtube['yes']='CHECKED';
			$youtube['no']='';
		}else{
			$youtube['no']='CHECKED';
			$youtube['yes']='';
		}
		?><p>Title: <input type="text" name="nimbit_social_title" value="<?php print $data['title']; ?>" /></p>
		<p>Which sites do you want to link to?</p><p><strong>Facebook: </strong><label>Yes<input name="nimbit_social_facebook" type="radio" value="yes"<?php print $facebook['yes']; ?>/></label>
		<label>No<input name="nimbit_social_facebook" type="radio" value="no"<?php print $facebook['no']; ?>/></label>http://facebook.com/<input size='10' type='text' name='nimbit_social_facebookuser' value="<?php print $data['facebookuser']; ?>"/></p>
		<p><strong>Twitter: </strong><label>Yes<input name="nimbit_social_twitter" type="radio" value="yes"<?php print $twitter['yes']; ?>/></label>
		<label>No<input name="nimbit_social_twitter" type="radio" value="no"<?php print $twitter['no']; ?>/></label>http://twitter.com/<input size='10' type='text' name='nimbit_social_twitteruser' value="<?php print $data['twitteruser']; ?>"/></p>
		<p><strong>MySpace: </strong><label>Yes<input name="nimbit_social_myspace" type="radio" value="yes"<?php print $myspace['yes']; ?>/></label>
		<label>No<input name="nimbit_social_myspace" type="radio" value="no"<?php print $myspace['no']; ?>/></label>http://myspace.com/<input size='10' type='text' name='nimbit_social_myspaceuser' value="<?php print $data['myspaceuser']; ?>"/></p>
		<p><strong>YouTube: </strong><label>Yes<input name="nimbit_social_youtube" type="radio" value="yes"<?php print $youtube['yes']; ?>/></label>
		<label>No<input name="nimbit_social_youtube" type="radio" value="no"<?php print $youtube['no']; ?>/></label>http://youtube.com/user/<input size='5' type='text' name='nimbit_social_youtubeuser' value="<?php print $data['youtubeuser']; ?>"/></p><?php
		if (isset($_POST['nimbit_social_twitter'])){
			$data['twitter'] = $_POST['nimbit_social_twitter'];
			$data['myspace'] = $_POST['nimbit_social_myspace'];
			$data['facebook'] = $_POST['nimbit_social_facebook'];
			$data['youtube'] = $_POST['nimbit_social_youtube'];
			$data['title'] = attribute_escape($_POST['nimbit_social_title']);
			$data['twitteruser'] = attribute_escape($_POST['nimbit_social_twitteruser']);
			$data['myspaceuser'] = attribute_escape($_POST['nimbit_social_myspaceuser']);
			$data['facebookuser'] = attribute_escape($_POST['nimbit_social_facebookuser']);
			$data['youtubeuser'] = attribute_escape($_POST['nimbit_social_youtubeuser']);
			update_option('nimbit_social', $data);
		}
	}	
	function widget($args){
	$data = get_option('nimbit_social');
	$twitter_code='';
	$myspace_code='';
	$facebook_code='';
	$youtube_code='';
	if($data['twitter']=='yes'){
		$twitter_code='<a href="http://twitter.com/'.$data['twitteruser'].'"><img src="'.plugins_url($path = '/instant-band-site-by-nimbit').'/images/twitter.png" /></a>';
	}
	if($data['myspace']=='yes'){
		$myspace_code='<a href="http://myspace.com/'.$data['myspaceuser'].'"><img src="'.plugins_url($path = '/instant-band-site-by-nimbit').'/images/myspace.png" /></a>';
	}
	if($data['facebook']=='yes'){
		$facebook_code='<a href="http://facebook.com/'.$data['facebookuser'].'"><img src="'.plugins_url($path = '/instant-band-site-by-nimbit').'/images/facebook.png" /></a>';
	}
	if($data['youtube']=='yes'){
		$youtube_code='<a href="http://youtube.com/user/'.$data['youtubeuser'].'"><img src="'.plugins_url($path = '/instant-band-site-by-nimbit').'/images/youtube.png" /></a>';
	}
	echo $args['before_widget'];
    echo $args['before_title'] . $data['title'] . $args['after_title'];
	echo $facebook_code.$twitter_code.$myspace_code.$youtube_code;
    echo $args['after_widget'];
	}
	function register(){
		register_sidebar_widget('Nimbit Social Sites', array('nimbit_social', 'widget'));
		register_widget_control('Nimbit Social Sites', array('nimbit_social', 'control'));
	}
}
?>