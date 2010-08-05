<div class="wrap ngg-wrap"> 
	<div id="dashboard-widgets-wrap" class="ngg-overview"> 
		<div id="dashboard-widgets" class="metabox-holder"> 
			<div id="post-body"> 
				<div id="dashboard-widgets-main-content"> 
					<div class="postbox-container" style="width:49%;"> 
						<div id="left-sortables" class="meta-box-sortables">
							<div id="dashboard_right_now" class="postbox " > 
								<div class="handlediv" title="Click to toggle"><br /></div>
								<h3 class='hndle'><span>Your Site</span></h3> 
								<div class="inside"> 
								<table>
									<tr>
										<td><strong>Step 1:</strong>  Your Nimbit Username is <strong><?php print(get_option('nimbit_username')); ?></strong>.<p></td>
									</tr>
									<tr>
										<td><strong>Step 2:</strong>  Your Nimbit Artist is: <strong><?php print(get_option('nimbit_artist')); ?></strong>.</p></td>
									</tr>
									<tr>
										<td><strong>Step 3:</strong>  Select which pages you would like to create:</p></td>
									<tr>
									<tr>
									<td><strong>NOTE: "edit content" links take you to your Nimbit account to update information.</strong></td>
									</tr>
									</table>
									<table><?php
									
									//used to keep track of which pages should be set as checked in check box
									$checked = array('Store'=>'','Events'=>'','Photos'=>'','Bio'=>'','News'=>'');
									//used to keep track of ids of pages created
									$id = array('Store'=>'','Events'=>'','Photos'=>'','Bio'=>'','News'=>'');
									
									
									if(get_option('nimbit_pages')=='somepages'){
									//create the pages that were checked
										$pages = get_option('nimbit_page');
										foreach($pages as $page){
											$this_content = nimbit_get_content($page);
											create_given_page($page, $this_content);
											
										}
									
										//insert the pages that exist into the $checked and $id array
										$arguments = array();
										$arguments['meta_key']= 'pagetype';
										$arguments['meta_value']= 'nimbitplug';
										$results = get_pages($arguments);
										if(!empty($results)){
											foreach($results as $res){
												$page_id = $res->ID;
												$meta_value = get_post_meta($page_id, 'pagename', true);
												$checked[$meta_value]='checked';
												$id[$meta_value]=$page_id;
											}
										}
										print('<p style="color: #FF0000;">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<strong>Your pages have been created.</strong></p>');
										update_option('nimbit_pages', 'nopages');
									}
									
									//delete page with given id
									if(isset($_GET['del'])){
										wp_delete_post($_GET['del'], $force_delete = false);
									}
									
									
									if(get_option('nimbit_pages')=='nopages'){
									$arguments = array();
									$arguments['meta_key']= 'pagetype';
									$arguments['meta_value']= 'nimbitplug';
									$results = get_pages($arguments);
									if(!empty($results)){
										foreach($results as $res){
											$page_id = $res->ID;
											$meta_value = get_post_meta($page_id, 'pagename', true);
											$checked[$meta_value]='checked';
											$id[$meta_value]=$page_id;
										}
									}
									
									}
									
								//below are the check boxes for each page
								//if the page already exists it is checked and disabled and a link to edit and delete show up next to it
								//the edit links take you to the Nimbit dashboard where you can edit that specific content tag
								//the delete links delete that specific page
								?><tr><td><form method="post" action="options.php"><?php wp_nonce_field('update-options');
								?><p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="checkbox" name="nimbit_page[]" checked value="Store"<?php if($checked['Store']=='checked'){print('disabled');} ?>/>  Store Front (Skin Store, Pro Accounts Only)<?php if($checked['Store']=='checked'){print('  <a href="http://members.nimbit.com/dashboard/main/store_appearance.php">edit content</a>   <a href="admin.php?page=nimbit-admin&del='.$id['Store'].'">delete</a>');}?></p>
								<p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="checkbox" name="nimbit_page[]" checked value="Events"<?php if($checked['Events']=='checked'){print('disabled');} ?>/>  Events<?php if($checked['Events']=='checked'){print('  <a href="http://members.nimbit.com/dashboard/main/events.php">edit content</a>   <a href="admin.php?page=nimbit-admin&del='.$id['Events'].'"> delete</a>');}?></p>
								<p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="checkbox" name="nimbit_page[]" checked value="Photos"<?php if($checked['Photos']=='checked'){print('disabled');} ?>/>  Photos<?php if($checked['Photos']=='checked'){print('  <a href="admin.php?page=nimbit-admin&del='.$id['Photos'].'">delete</a>');}?></p>
								<p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="checkbox" name="nimbit_page[]" checked value="Bio"<?php if($checked['Bio']=='checked'){print('disabled');} ?>/>  Bio<?php if($checked['Bio']=='checked'){print('  <a href="http://members.nimbit.com/dashboard/main/basicinfo.php">edit content</a>   <a href="admin.php?page=nimbit-admin&del='.$id['Bio'].'">delete</a>');}?></p>
								<p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="checkbox" name="nimbit_page[]" checked value="News"<?php if($checked['News']=='checked'){print('disabled');} ?>/>  News (Pro Accounts Only)<?php if($checked['News']=='checked'){print('  <a href="http://members.nimbit.com/dashboard/main/news.php">edit content</a>   <a href="admin.php?page=nimbit-admin&del='.$id['News'].'">delete</a>');}?></p>
								<input type="hidden" name="action" value="update" />
								<input type="hidden" name="nimbit_pages" value="somepages" />
								<input type="hidden" name="page_options" value="nimbit_pages,nimbit_page" />
								</td></tr></table><table>
								<tr><td width="170"><p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input class="button-primary" type="submit" value="<?php _e('Generate Pages') ?>" /></p>
								</td><td></form>
								<!--clicking reset resets the nimbit username and artist in the DB and deletes all Nimbit pages-->
								<form action='admin.php?page=nimbit-admin' method='post'>
								<input type="hidden" name="reset" value="true" />
								<input type="submit" class="button-primary" style="background: #FF0000; outline: #FF0000;" value="Delete All Pages" />
								</form>
								</td></tr>
								</table>

								<table>
								<tr>
									<td><strong>Step 4: </strong>  The Following Nimbit Widgets Were Installed:</p></td>
								</tr>
								<tr>
									<td><ul>
										<li>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <strong>Streampad Music Player</strong> Widget</li>
										<li>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <strong>Streampad Music Player</strong> Widget</li>
										<li>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <strong>Promotion Code</strong> Widget (Indie and Pro Accounts only) <a href="http://members.nimbit.com/dashboard/main/promo_offers.php">edit content</a> </li>
										<li>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <strong>Standard Email Form</strong> Widget</li>
										<li>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <strong>Next Gig</strong> Widget <a href="http://members.nimbit.com/dashboard/main/events.php">edit content</a>  </li>
										<li>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <strong>Facebook Like</strong> Widget </li>
										<li><p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <a class="button-primary" href="widgets.php">Put Widgets into Sidebar</a></p></li></ul>
									</td>
								</tr>
								</table>
								<table>
								<tr height="50"></tr>
								<tr>
								<td width="40"></td>
								<td><a href="http://www.nimbit.com" class="button" style="background: #00CC00; outline: #00CC00; color: white; font-weight: bold;">Log into Nimbit</a></td>
								</tr>
								<tr><td></td><td><strong>Note: </strong>You can only edit your Nimbit content in your Nimbit dashboard.</td></tr>
								<tr height="10"></tr>
								</table>
								
							</div> 
							</div> 
						</div>						
					</div>
					<div class="postbox-container" style="width:29%;"> 
							<div id="dashboard_right_now" class="postbox" > 
								<div class="handlediv" title="Click to toggle"><br /></div>
								<h3 class='hndle'><span>Like This Plugin?</span></h3> 
								<div class="inside"> 
									<p>If you like this plugin please rate it <a href="http://wordpress.org/extend/plugins/instant-band-site-by-nimbit/">here</a>!!</p>
								</div> 
						</div>						
					</div>
					<div class="postbox-container" style="width:29%;"> 
							<div id="dashboard_right_now" class="postbox" > 
								<div class="handlediv" title="Click to toggle"><br /></div>
								<h3 class='hndle'><span>Feedback</span></h3> 
								<div class="inside"> 
									<p>Fill out this short feedback survey <a href="http://content.nimbit.com/machform/view.php?id=25" title="Instant Band Site Feedback Form">here</a> 
									 and let us know any problems you ran into or any ideas you have for future features you would love
									 for us to add to the plugin!</p>
								</div> 
						</div>						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>