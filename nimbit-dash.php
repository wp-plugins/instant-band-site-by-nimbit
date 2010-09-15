<?php require_once 'common.php';?>

<p><strong>The Nimbit Site Generator Plugin can set you up with a site for your music in 4 easy steps.  Once You are done you will have combined the smartest website software with the best music business platform.</strong></p>
		<p><strong style="color: green;">Your Pages</strong></p>
		<?php	
		$arguments = array();
		$arguments['meta_key']= 'pagetype';
		$arguments['meta_value']= 'nimbitplug';
		$results = get_pages($arguments);
		$count_pages = 0;
		if(!empty($results)){
		$edit_content = array('Music'=>'http://'.dashboard_host().'/dashboard/main/store_appearance.php','Gigs'=>'http://'.dashboard_host().'/dashboard/main/events.php',
		'Photos'=>'','Bio'=>'http://'.dashboard_host().'/dashboard/main/basicinfo.php','News'=>'http://'.dashboard_host().'/dashboard/main/news.php');
			print('<table>');
			foreach($results as $res){
				$page_id = $res->ID;
				$meta_value = get_post_meta($page_id, 'pagename', true);
				$count++;
				if($meta_value != 'Photos'){
					?><tr><td width="15"></td><td><?php print($res->post_title);?><a href="<?php print($edit_content[$res->post_title]); ?>">  edit content</a></td></tr><?php
				}else{
					?><tr><td width="15"></td><td><?php print($res->post_title);?><a href="upload.php">  add media</a></td></tr><?php
				}
			}
			print('</table>');
		}else{
			?><p>You have not set up any pages with your Nimbit content yet.  Click <a href='admin.php?page=nimbit-admin'>here</a> to get started.<?php
		}
		?>
		<p><strong>NOTE:</strong> "edit content" links take you to your Nimbit account to update information. You cannot edit your Nimbit content from within Wordpress.</p>
		<table>
		<tr>
		<td width="570"></td>
		<td><a href="http://www.nimbit.com" class="button-primary" style="background: #00CC00; outline: #00CC00;">Log into Nimbit</a></td>
		</tr>
		</table>