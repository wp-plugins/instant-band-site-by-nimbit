<?php
//featured product widget
add_action("widgets_init", array('nimbit_featured_product', 'register'));
register_activation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_featured_product', 'activate'));
register_deactivation_hook('instant-band-site-by-nimbit/instant-band-site-by-nimbit.php', array('nimbit_featured_product', 'deactivate'));
class nimbit_featured_product {
	function activate(){
		$data = array();
		$cds = array();
		$tickets = array();
		$details = array();
		$data = array('title'=>'Featured Product', 'select'=>'', 'height'=>'', 'url'=>get_bloginfo('wpurl').'/store', 'desc'=>'enter a subtitle here');
		if( !get_option('nimbit_featured_product')){
			add_option('nimbit_featured_product' , $data);
			add_option('nimbit_all_cds', $cds);
			add_option('nimbit_all_tickets', $tickets);
			add_option('nimbit_all_events', $details);
		}else{
			update_option('nimbit_featured_product' , $data);
			update_option('nimbit_all_cds' , $cds);
			update_option('nimbit_all_tickets' , $tickets);
			update_option('nimbit_all_events', $details);
		}
	
	}
	function deactivate(){
		delete_option('nimbit_featured_product');
		delete_option('nimbit_all_cds');
		delete_option('nimbit_all_tickets');
		delete_option('nimbit_all_events');
	}
	function control(){
		$cds = array();
		$tickets = array();
		$artist = get_option('nimbit_artist');
		$url1 = 'http://'.nimbitmusic_host().'/artistdata/';
		$url1 .= $artist;
		$url1 .= '/full_catalog/';
		$xml1 = simplexml_load_file($url1);
		$count = 0;
		$product = $xml1->xpath('//response/RecordCompany/Artist/Catalog/Product');
		$type = $xml1->xpath('//response/RecordCompany/Artist/Catalog/Product/Type');
		$imageid = $xml1->xpath('//response/RecordCompany/Artist/Catalog/Product/ImageId');
		$cd = $xml1->xpath('//response/RecordCompany/Artist/Catalog/Product/ProductName');
		$ticket = $xml1->xpath('//response/RecordCompany/Artist/Catalog/Product/Ticket/EventId');
		foreach($product as $r => $result){
			if($type[$r]=='CD'){
				$id = (string)$imageid[$r];
				if($id == "" | $id == '0'){
					$id = 'http://'.nimbitmusic_host().'/images/placeholder/product_photo.gif';
				}else{
					$id = 'http://'.nimbitmusic_host().'/images/db/large/'.$id.'.jpg';
				}
				$cds[(string)$cd[$r]] = $id;
			}elseif($type[$r]=='Will Call Tickets'){
				$id = (string)$imageid[$r];
				if($id == ""){
					$id = 'http://'.nimbitmusic_host().'/images/placeholder/ticket_photo.jpg';
				}else{
					$id = 'http://'.nimbitmusic_host().'/images/db/large/'.$id.'.jpg';
				}
				$tickets[(string)$ticket[$count]] = $id;
				$count++;
			}
		}
		$url2 = 'http://'.nimbitmusic_host().'/artistdata/'.$artist.'/calendar/';
		$xml2 = simplexml_load_file($url2);
		$count2 = 0;
		$eventid = $xml2->xpath('//EventCalendar/Event/event_id');
		$venue = $xml2->xpath('//EventCalendar/Event/venue');
		$date = $xml2->xpath('//EventCalendar/Event/event_date');
		$details = array();
		$dates = $xml2->xpath('//EventCalendar/Event');
		$test = array();
		$count3 = 0;
		foreach($eventid as $e){
			$test[(string)$e] = array('venue'=> (string)$venue[$count3], 'date'=> (string)$date[$count3]);
			$count3++;
		}

		
		foreach($tickets as $key=>$v){
			foreach($test as $t=>$e){
				if($key == $t){
					$temp = array('venue' => (string)$e['venue'], 'date' => (string)$e['date'], 'image' => $v);
					$details[(string)$t] = $temp;
				}
			}
		
		}
		

		update_option('nimbit_all_cds' , $cds);
		update_option('nimbit_all_tickets' , $tickets);
		update_option('nimbit_all_events', $details);
		
		
		$tickets = get_option('nimbit_all_tickets');
		$details = get_option('nimbit_all_events');
		$cds = get_option('nimbit_all_cds');
		
		$selected = array();
		foreach($tickets as $c=>$d){
			$selected[$c] = '';
		}
		foreach($cds as $c=>$d){
			$selected[$c] = '';
		}

		$data = get_option('nimbit_featured_product');
		?><p>Title: <input type="text" name="nimbit_featured_product_title" value="<?php print $data['title']; ?>" /></p>
		<p>Subtitle: <input type="text" name="nimbit_featured_product_subtitle" value="<?php print $data['desc']; ?>" /></p>
		<?php
		

		if($data['select'] != ""){
			$selected[$data['select']] = 'selected';
		}
		?><p>Choose From Your Nimbit Catalog: <select size="1" name="nimbit_featured_product_select"><?php
												foreach($cds as $c=>$d){
													print('<option '.$selected[$c].' value="'.$c.'">'.$c.'</option>');
												}
		
												foreach($details as $d => $det){
													print '<option '.$selected[$d].' value="'.$d.'">'.$det['venue'].'</option>';
												}
												?></select>
		</p>
		<br />
		<p>Square Image Dimensions: <br /><?php
		
		$selectedsize = array('100'=>'', '150'=>'','200'=>'','250'=>'','300'=>'');
		if($data['height'] != ""){
			$selectedsize[$data['height']] = 'selected';
		}
								?><select name="nimbit_featured_product_height">
													<option <?php print $selectedsize['100']; ?> value="100">100</option>
													<option <?php print $selectedsize['150']; ?> value="150">150</option>
													<option <?php print $selectedsize['200']; ?> value="200">200</option>
													<option <?php print $selectedsize['250']; ?> value="250">250</option>
													<option <?php print $selectedsize['300']; ?> value="300">300</option>
								</select> (px)
		</p>
		<br />
		<p>Storefront URL: <br /> <input size="40" type="text" name="nimbit_featured_product_url" value="<?php print $data['url']; ?>" />
		<span style="font-size: xx-small;">Paste in the URL of your Nimbit storefront</span></p>
		<?php
		if(isset($_POST['nimbit_featured_product_title'])){
			$data['title'] = attribute_escape($_POST['nimbit_featured_product_title']);
			update_option('nimbit_featured_product', $data);
		}if(isset($_POST['nimbit_featured_product_subtitle'])){
			$data['desc'] = (string)attribute_escape($_POST['nimbit_featured_product_subtitle']);
			update_option('nimbit_featured_product', $data);
		}
		if (isset($_POST['nimbit_featured_product_select'])){
			$data['select'] = (string)attribute_escape($_POST['nimbit_featured_product_select']);
			update_option('nimbit_featured_product', $data);
		}if (isset($_POST['nimbit_featured_product_height'])){
			$data['height'] = attribute_escape($_POST['nimbit_featured_product_height']);
			update_option('nimbit_featured_product', $data);
		}if (isset($_POST['nimbit_featured_product_url'])){
			$data['url'] = attribute_escape($_POST['nimbit_featured_product_url']);
			update_option('nimbit_featured_product', $data);
		}	
	}
	function widget($args){
	$artist = get_option('nimbit_artist');
	$data = get_option('nimbit_featured_product');
	$cds = get_option('nimbit_all_cds');
	$details = get_option('nimbit_all_events');
	$tickets = get_option('nimbit_all_tickets');
	echo $args['before_widget'];
    echo $args['before_title'] . $data['title'] . $args['after_title'];
	if($cds[$data['select']]==""){
		echo '<p style="font-size: medium;"><strong>';
		echo $data['desc'];
		echo '</strong></p>';
		echo ("<a href='".$data['url']."' <img src='".$details[$data['select']]['image']."' width=\"".$data['height']."\" height=\"".$data['height']."\" /></a><br />");
		echo '<br />';
		echo '<p style="font-size: medium;"><strong>';
		echo date('M d, Y',strtotime($details[$data['select']]['date']));
		echo '</strong></p>';
	}else{
		echo $data['desc'];
		echo ("<a href='".$data['url']."' <img src=\"".$cds[$data['select']]."\" width=\"".$data['height']."\" height=\"".$data['height']."\" /></a><br />");
	}
    echo $args['after_widget'];
	}
	function register(){
		register_sidebar_widget('Nimbit Featured Product', array('nimbit_featured_product', 'widget'));
		register_widget_control('Nimbit Featured Product', array('nimbit_featured_product', 'control'));
	}
}
?>