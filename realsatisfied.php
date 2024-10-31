<?php

/* 
* +--------------------------------------------------------------------------+
* | Copyright (c) 2011-2013 RealSatisfied                                    |
* +--------------------------------------------------------------------------+
* | This program is free software; you can redistribute it and/or modify     |
* | it under the terms of the GNU General Public License as published by     |
* | the Free Software Foundation; either version 2 of the License, or        |
* | (at your option) any later version.                                      |
* |                                                                          |
* | This program is distributed in the hope that it will be useful,          |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
* | GNU General Public License for more details.                             |
* |                                                                          |
* | You should have received a copy of the GNU General Public License        |
* | along with this program; if not, write to the Free Software              |
* | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA |
* +--------------------------------------------------------------------------+
*/

/*
Plugin Name: RealSatisfied Widget
Plugin URI: http://wordpress.org/extend/plugins/realsatisfied-widget/
Description: Sidebar widget that displays RealSatisfied Testimonials and ratings data based on an Agent or Office Profile Page RSS feed.
Version: 1.9.7
Author: RealSatisfied (www.realsatisfied.com)
Author URI: http://www.realsatisfied.com/
License: GPLv2 or later .
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class Real_Satisfied_Testimonials_Widget extends WP_Widget {

	// constructor
	function __construct(){
	//function Real_Satisfied_Testimonials_Widget() {
		
		$widget_ops = array( 'classname' => 'rsw_widget', 'description' => __( 'Sidebar widget that displays RealSatisfied Testimonials and ratings data based on an Agent or Office Profile Page RSS feed.', 'Real_Satisfied_Testimonials_Widget' ) );
		parent::__construct(false, $name = __('RealSatisfied Widget', 'Real_Satisfied_Testimonials_Widget'), $widget_ops );

	}

	// widget form creation
	function form( $instance ) {	
		
		// check values
		if ( $instance ) {
			// if values exist, set them
			$rsw_title 					= esc_attr($instance['title']);
			$rsw_real_satisfied_id 		= esc_attr($instance['real_satisfied_id']);
			$rsw_r_title 				= esc_attr($instance['r_title']);
			$rsw_display_arrows 		= esc_attr($instance['display_arrows']);
			$rsw_speed 					= esc_attr($instance['speed']);
			$rsw_animation_type 		= esc_attr($instance['animation_type']);
			$rsw_mode 					= esc_attr($instance['mode']);
			$rsw_auto_animate 			= esc_attr($instance['auto_animate']);
			$rsw_display_ratings 		= esc_attr($instance['display_ratings']);
			$rsw_display_photo 			= esc_attr($instance['display_photo']);
			//$rsw_navigation_dots 		= esc_attr($instance['navigation_dots']);
			$rsw_show_rs_verified_img 	= esc_attr($instance['show_rs_verified_img']);
			$rsw_show_dates 			= esc_attr($instance['show_dates']);
			$rsw_officekey 				= esc_attr($instance['officekey']);
		} else {
			// set default vaules
			$rsw_title 					= 'My Recent Testimonials';
			$rsw_real_satisfied_id 		= '';
			$rsw_r_title 				= 'My Client Ratings';
			$rsw_display_arrows 		= TRUE;
			$rsw_speed 					= 7;
			$rsw_animation_type 		= 'slide';
			$rsw_mode 					= 'Agent';
			$rsw_auto_animate 			= TRUE;
			$rsw_display_ratings 		= TRUE;
			$rsw_display_photo 			= TRUE;
			//$rsw_navigation_dots 		= FALSE;
			$rsw_show_rs_verified_img 	= TRUE;
			$rsw_show_dates 			= TRUE;
			$rsw_officekey 				= 'NO_KEY';
		}

		// html for the widget form in the admin area
		?>
		<h3>Settings</h3>
		<p>
			<em>Select the feed you would like to display</em>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('mode'); ?>"><?php _e('Display Mode:'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('mode'); ?>" name="<?php echo $this->get_field_name('mode'); ?>">
				<option value="Agent" <?php echo ( $rsw_mode == 'Agent' ) ? ' selected="selected"' : '' ; ?>>Agent</option>
				<option value="Office" <?php echo ( $rsw_mode == 'Office' ) ? ' selected="selected"' : '' ; ?>>Office</option>
			</select>
		</p>

		<h3>Testimonials</h3>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Testimonials Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($rsw_title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('real_satisfied_id'); ?>"><?php _e('Vanity Key:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('real_satisfied_id'); ?>" name="<?php echo $this->get_field_name('real_satisfied_id'); ?>" type="text" value="<?php echo esc_attr($rsw_real_satisfied_id); ?>" />
			<?php
			// Fetch remote rss feed and test if office key is valid
			// NOTE: feed is cached for twelve hours
			if ( $rsw_real_satisfied_id != '' ) {
				
				// Get a SimplePie feed object from the specified feed source
				if($rsw_mode == "Agent") {
					$rsw_rss = fetch_feed( 'http://rss.realsatisfied.com/rss/agent/' . $rsw_real_satisfied_id );
				} else {
					$rsw_rss = fetch_feed( 'http://rss.realsatisfied.com/rss/office/' . $rsw_real_satisfied_id );
				}

				if ( !is_wp_error( $rsw_rss ) ) { // Checks that the object is created correctly 

					// Set the officekey from the http://rss.realsatisfied.com/ns/realsatisfied/ namespace
					$rsw_rss_items = $rsw_rss->get_channel_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'officekey' );
					$rsw_officekey = $rsw_rss_items[0]['data'];

				} else {

					// Not a vaild Agent Feed (Vanity Key)
					echo '<p style="color:red;"><strong>Error:</strong></p>';
					echo $rsw_rss->get_error_message();
					$rsw_officekey = "NO_KEY";

				}
			
			} else {

				echo '<p style="color:red;"><strong>Vanity Key required</strong></p>';
				$rsw_officekey = "NO_KEY";

			}

			?>
			<input id="<?php echo $this->get_field_id('officekey'); ?>" name="<?php echo $this->get_field_name('officekey'); ?>" type="hidden" value="<?php echo esc_attr($rsw_officekey); ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox"  id="<?php echo $this->get_field_id('auto_animate'); ?>" name="<?php echo $this->get_field_name('auto_animate'); ?>"<?php if ($rsw_auto_animate) { ?> checked="checked"<?php } ?> />
			<label for="<?php echo $this->get_field_id('auto_animate'); ?>">Animate automatically</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('speed'); ?>"><?php _e('Hold each slide for:'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('speed'); ?>" name="<?php echo $this->get_field_name('speed'); ?>">
				<?php
				for($i=2; $i<16; $i++) {
					echo '<option value="' . $i . '"' . ( $i == $instance['speed'] ? ' selected="selected"' : '' ) . '>' . $i . " seconds</option>\n";
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('animation_type'); ?>"><?php _e('Animation Type:'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('animation_type'); ?>" name="<?php echo $this->get_field_name('animation_type'); ?>">
				<option value="slide" <?php echo ( $rsw_animation_type == 'slide' ) ? ' selected="selected"' : '' ; ?>>Slide</option>
				<option value="fade" <?php echo ( $rsw_animation_type == 'fade' ) ? ' selected="selected"' : '' ; ?>>Fade</option>
			</select>
		</p>
		<p>
			<input class="checkbox" type="checkbox"  id="<?php echo $this->get_field_id('display_arrows'); ?>" name="<?php echo $this->get_field_name('display_arrows'); ?>"<?php if ($rsw_display_arrows) { ?> checked="checked"<?php } ?> />
			<label for="<?php echo $this->get_field_id('display_arrows'); ?>">Display side arrows</label>
		</p>
		<p>
			<input class="checkbox" type="checkbox"  id="<?php echo $this->get_field_id('show_dates'); ?>" name="<?php echo $this->get_field_name('show_dates'); ?>"<?php if ($rsw_show_dates) { ?> checked="checked"<?php } ?> />
			<label for="<?php echo $this->get_field_id('show_dates'); ?>">Display dates with testimonials</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('display_photo'); ?>"><?php _e('Display Agent Photo (Office Mode Only):'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('display_photo'); ?>" name="<?php echo $this->get_field_name('display_photo'); ?>">
				<option value="both" <?php echo ( $rsw_display_photo == 'both' ) ? ' selected="selected"' : '' ; ?>>On Both</option>
				<option value="page" <?php echo ( $rsw_display_photo == 'page' ) ? ' selected="selected"' : '' ; ?>>On Page</option>
				<option value="popup" <?php echo ( $rsw_display_photo == 'popup' ) ? ' selected="selected"' : '' ; ?>>On Pop-up</option>
				<option value="none" <?php echo ( $rsw_display_photo == 'none' ) ? ' selected="selected"' : '' ; ?>>None</option>
			</select>
		</p>
		<p>
			<input class="checkbox" type="checkbox"  id="<?php echo $this->get_field_id('show_rs_verified_img'); ?>" name="<?php echo $this->get_field_name('show_rs_verified_img'); ?>"<?php if ($rsw_show_rs_verified_img) { ?> checked="checked"<?php } ?> />
			<label for="<?php echo $this->get_field_id('show_rs_verified_img'); ?>">Display RealSatisfied Verified Shield</label>
		</p>
		
		<h3>Ratings</h3>
		<p>
			<em>Only displayed in Agent mode.</em>
		</p>
		<p>
			<input class="checkbox" type="checkbox"  id="<?php echo $this->get_field_id('display_ratings'); ?>" name="<?php echo $this->get_field_name('display_ratings'); ?>"<?php if ($rsw_display_ratings) { ?> checked="checked"<?php } ?> /> 
			<label for="<?php echo $this->get_field_id('display_ratings'); ?>">Display Client Ratings?</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('r_title'); ?>"><?php _e('Client Ratings Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('r_title'); ?>" name="<?php echo $this->get_field_name('r_title'); ?>" type="text" value="<?php echo esc_attr($rsw_r_title); ?>" />
		</p>
		<p>
			<?php rsw_clear_cache_button(); ?>
		</p>
		<?php
		if ($instance['officekey'] == "NO_KEY" || (!$instance['officekey'])){
			echo '<p style="color:red;"><strong>Please save settings again.</strong></p>';
		}



	}

	// widget update
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['r_title'] = strip_tags($new_instance['r_title']);
		$instance['real_satisfied_id'] = strip_tags($new_instance['real_satisfied_id']);
		$instance['officekey'] = strip_tags($new_instance['officekey']);
		$instance['speed'] = $new_instance['speed'];
		$instance['display_ratings'] = $new_instance['display_ratings'];
		$instance['display_photo'] = $new_instance['display_photo'];
		$instance['mode'] = $new_instance['mode'];
		$instance['auto_animate'] = $new_instance['auto_animate'];
		$instance['display_arrows'] = $new_instance['display_arrows'];
		$instance['animation_type'] = $new_instance['animation_type'];
		//$instance['navigation_dots'] = $new_instance['navigation_dots'];
		$instance['show_rs_verified_img'] = $new_instance['show_rs_verified_img'];
		$instance['show_dates'] = $new_instance['show_dates'];
		return $instance;

	}

	// widget display
	function widget( $args, $instance ) {
		
		extract( $args );
		
		if ( $instance['real_satisfied_id'] ) {
			
			if ( $instance['mode'] == "Agent" ) {
				
				$rsw_rss_feed_source = fetch_feed( 'http://rss.realsatisfied.com/rss/agent/' . $instance['real_satisfied_id'] );

			} else {
				
				$rsw_rss_feed_source = fetch_feed( 'http://rss.realsatisfied.com/rss/office/' . $instance['real_satisfied_id'] );
			
			}

			if ( !is_wp_error( $rsw_rss_feed_source ) ) { // Checks that the object is created correctly 

				// Figure out how many total items there are, but limit it to 50. 
				$rsw_maxitems = $rsw_rss_feed_source->get_item_quantity( 50 ); 

				// Build an array of all the items, starting with element 0 (first element).
				$rsw_rss_items = $rsw_rss_feed_source->get_items( 0, $rsw_maxitems );

				// Load channel data into an array
				
				$rsw_channel_array = array();
				$rsw_channel_array['title'] = $rsw_rss_feed_source->get_title();
				$rsw_channel_array['link'] = $rsw_rss_feed_source->get_permalink();
				$rsw_channel_array['description'] = $rsw_rss_feed_source->get_description();
				$rsw_channel_array['language'] = $rsw_rss_feed_source->get_language();
				$rsw_channel_array['copyright'] = $rsw_rss_feed_source->get_copyright();
				$tmp = $rsw_rss_feed_source->get_channel_tags( '', 'pubDate' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
				$rsw_channel_array['pubDate'] = $tmp;
				$tmp = $rsw_rss_feed_source->get_channel_tags( '', 'docs' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
				$rsw_channel_array['docs'] = $tmp;
				$tmp = $rsw_rss_feed_source->get_channel_tags( '', 'generator' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
				$rsw_channel_array['generator'] = $tmp;
				$tmp = $rsw_rss_feed_source->get_channel_tags( '', 'ttl' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
				$rsw_channel_array['ttl'] = $tmp;

				// Load testimonials into an array
				$rsw_testimonials_array = array();
				foreach ( $rsw_rss_items as $rsw_rss_item ) {
					$rsw_testimonial_array = array();
					$rsw_testimonial_array['title'] = $rsw_rss_item->get_title();
					$tmp = $rsw_rss_feed_source->get_channel_tags( '', 'pubDate' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
					$rsw_testimonial_array['pubDate'] = $tmp;
					$rsw_testimonial_array['description'] = $rsw_rss_item->get_description();
					if ( $instance['mode'] == "Office" ) {
						$tmp = $rsw_rss_item->get_item_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'display_name' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
						$rsw_testimonial_array['display_name'] = $tmp;
						$tmp = $rsw_rss_item->get_item_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'avatar' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
						$rsw_testimonial_array['avatar'] = $tmp;
						$rsw_testimonial_array['link'] = $rsw_rss_item->get_link();
					}
					$rsw_testimonials_array[] = $rsw_testimonial_array;

				}
				if ($instance['mode'] == "Office"){
					// randomise the order of the testimonals
					shuffle($rsw_testimonials_array);
				}

				// Check responseCount values
				$tmp = $rsw_rss_feed_source->get_channel_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'responseCount' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
				$rsw_response_count = $tmp;

				if ( !$rsw_response_count ) {
					
					//AGENT TURNED OFF SCORES
					echo "<!-- ratings_off -->";

				} elseif ( $rsw_response_count == 0 ) {

					//AGENT HAS NOT BEEN REVIEWED YET
					echo "<!-- no_results -->";

				} else {
					echo "<!-- responses : $rsw_response_count -->";
					//AGENT HAS RATINGS
				
				}

				// Display the testimonials
				if ( count( $rsw_testimonials_array ) == 0 ) {

						//AGENT HAS NO TESTIMONIALS
						echo "<!-- no_testimonials -->";

				} else {

					echo $before_widget;

					if ( ( $rsw_response_count ) && ( $rsw_response_count > 0 ) && ( $instance['display_ratings'] == true ) && ( $instance['mode'] == "Agent" ) ) {

						echo $before_title . $instance['r_title'] . $after_title;
						echo '<div itemscope itemtype="http://data-vocabulary.org/Review-aggregate" class="rsw-ratings"><ul>';
						$tmp = $rsw_rss_feed_source->get_channel_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'avatar' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
						echo '<meta itemprop="photo" content="' . $tmp .'" />';
						$tmp = $rsw_rss_feed_source->get_channel_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'overall_satisfaction' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
						echo '<li><span>Satisfaction:</span><span itemprop="rating" itemscope itemtype="http://data-vocabulary.org/Rating"><span itemprop="average">' . $tmp. '</span>%<meta itemprop="best" content="100" /></span></li>';
						$tmp = $rsw_rss_feed_source->get_channel_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'recommendation_rating' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
						echo '<li><span>Recommendation:</span><span>' . $tmp. '%</span></li>';
						$tmp = $rsw_rss_feed_source->get_channel_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'performance_rating' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
						echo '<li><span>Performance:</span><span>' . $tmp. '%</span></li></ul>';
						$tmp = $rsw_rss_feed_source->get_channel_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'display_name' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
						echo '<p class="ratings-summary"><span itemprop="itemreviewed">' . $tmp . '</span> has been rated by <span itemprop="votes">' . $rsw_response_count . '</span> clients.</p>';
						echo '</div>';
						$tmp = $rsw_rss_feed_source->get_channel_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'display_name' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
						$verified_copy = '<div class="rs-verified">Rating & Testimonial data verified by <a href=" ' . $rsw_rss_feed_source->get_permalink() . '" target="_blank" class="rs-verified" title="View the Agent Profile for ' . $tmp . '">RealSatisfied</a></div>';
						$tmp = $rsw_rss_feed_source->get_channel_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'display_name' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
						$rs_badge = '<p class="rsw-badge"><a href="' . $rsw_rss_feed_source->get_permalink() . '" target="_blank" title="View the Agent Profile for ' . $tmp . '"><img src="' . plugins_url() . '/realsatisfied-widget/images/RealSatisfied-Trust-Seal-80pix.png" height="80" border="0" class="rs-badge"/></a></p>';
					
					} else {
						
						if ( $instance['mode'] == "Agent" ) {

							$tmp = $rsw_rss_feed_source->get_channel_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'display_name' ); $tmp = $tmp[0]; $tmp = $tmp['data'];
							$verified_copy = '<div class="rs-verified">Testimonial data verified by <a href=" ' . $rsw_rss_feed_source->get_permalink() . '" target="_blank" class="rs-verified" title="View the Agent Profile for ' . $tmp . '">RealSatisfied</a></div>';
							$rs_badge = '<p class="rsw-badge"><a href="' . $rsw_rss_feed_source->get_permalink() . '" target="_blank" title="View the Agent Profile for ' . $tmp . '"><img src="' . plugins_url() . '/realsatisfied-widget/images/RealSatisfied-Trust-Seal-80pix.png" height="80" border="0" class="rs-badge"/></a></p>';
						
						} else {	
						
							$verified_copy = '<div class="rs-verified">Testimonial data verified by <a href=" ' . $rsw_rss_feed_source->get_permalink() . '" target="_blank" class="rs-verified" title="Read more about RealSatisfied ratings">RealSatisfied</a></div>';
						
						}

					}

					$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'] );

					if ( !empty( $title ) ) {
						echo $before_title . $title . $after_title;
					}

					if ( $instance['mode'] == "Office" && ( $instance['display_photo'] == "both" || $instance['display_photo'] == "page" ) ) {
						echo '<div class="rsw-flexslider office container">';
					} else {
						echo '<div class="rsw-flexslider agent container">';
					}

					echo '<ul class="rs-slides">';

					$count = 0;
					for ( $x=0; $x < count( $rsw_testimonials_array ); $x++ ) {
						$count++;
						$rsw_testimonial_item = $rsw_testimonials_array[$x];
						?>
						<li>
							<?php if ( $instance['mode'] == "Office" && ( $instance['display_photo'] == "both" || $instance['display_photo'] == "page" ) ) { ?>
							<a href="#TB_inline?height=500&amp;width=600&amp;inlineId=rs-popup-<?php echo $count; ?>" title="Client Testimonial for <?php echo nl2br($rsw_testimonial_item["display_name"]);?>" class="thickbox">
								<img src="<?php echo $rsw_testimonial_item["avatar"]; ?>" class="rs-agent-photo"/>
							</a>
							<?php } ?>								
							<a href="#TB_inline?height=500&amp;width=600&amp;inlineId=rs-popup-<?php echo $count; ?>" title="Client Testimonial for <?php echo nl2br($rsw_testimonial_item["display_name"]);?>" class="thickbox">
								<p class="rs-disp-testimonial">&ldquo;<?php echo $rsw_testimonial_item["description"]; ?>&rdquo;</p>
							</a>
							<p class="rs-disp-byline">
								<a href="#TB_inline?height=500&amp;width=600&amp;inlineId=rs-popup-<?php echo $count; ?>" title="Client Testimonial for <?php if ( $instance['mode'] == "Office" ) { echo nl2br( $rsw_testimonial_item["display_name"] ); } else { $tmp = $rsw_rss_feed_source->get_channel_tags( 'http://rss.realsatisfied.com/ns/realsatisfied/', 'display_name' ); $tmp = $tmp[0]; echo nl2br( $tmp['data'] ); } ?>" class="thickbox">Read More</a> from <br /><strong><?php echo $rsw_testimonial_item["title"]; ?></strong>
							</p>
						</li>
						<div id="rs-popup-<?php echo $count; ?>" class="rs-popup-container" style="display:none">
							<?php if ( $instance['mode'] == "Office" ) { ?>
							<h4 class="rs-tst-header"><?php if ( $instance['display_photo'] == "both" || $instance['display_photo'] == "popup" ) { ?><img src="<?php echo $rsw_testimonial_item["avatar"]; ?>" class="rs-avatar" /><?php } ?>Client Testimonial for <?php echo nl2br( $rsw_testimonial_item["display_name"] ); ?></h4>
							<?php } ?>
							<p class="rs-testimonial">&ldquo;<?php echo nl2br( $rsw_testimonial_item["description"] ); ?>&rdquo;</p>
							<?php if ( $instance['show_dates'] ) {
								echo '<p class="rs-byline">' . $rsw_testimonial_item["title"] . '<br />' . date( 'F j\, Y', strtotime( $rsw_testimonial_item["pubDate"] ) ) . '</p>';
							} else {
								echo '<p class="rs-byline">' . $rsw_testimonial_item["title"] . '</p>';
							}
							if ( $instance['mode'] == "Office" ) {
								echo '<p class="rs-verified">Testimonial data verified by <a href=" ' . $rsw_rss_feed_source->get_permalink() . '" target="_blank" class="rs-verified" title="Read more about RealSatisfied ratings">RealSatisfied</a><br />View the profile for <a href=" ' . $rsw_testimonial_item["link"] . '" target="_blank" class="rs-verified" title="View the Profile for ' . $rsw_testimonial_item["display_name"] . '">' . $rsw_testimonial_item["display_name"] . '</a></p>';
							} else {
								echo $verified_copy;
							}
							if ( $instance['show_rs_verified_img'] ) {
								if ( $instance['mode'] == "Office" ) {
									$rs_badge = '<p class="rsw-badge"><a href="' .$rsw_testimonial_item["link"] . '" target="_blank" title="Read more about RealSatisfied ratings"><img src="' . plugins_url() . '/realsatisfied-widget/images/RealSatisfied-Trust-Seal-80pix.png" height="80" border="0" class="rs-badge"/></a></p>';
								}
								echo $rs_badge;
							} ?>
						</div>
						<?php
					}

					echo '</ul></div>';
					echo $verified_copy;
					echo $after_widget;

					?>
					<script type="text/javascript">
					jQuery(document).ready(function($){
							jQuery('.rsw-flexslider').flexslider({
							namespace: 'rsw-',
							smoothHeight: false,
			                touch: true,
							selector: '.rs-slides > li',
							slideshowSpeed: <?php echo ($instance['speed'] * 1000); ?>,
							animation: '<?php echo ($instance['animation_type']); ?>',
						    slideshow: <?php echo ($instance['auto_animate']) ? 'true' : 'false'; ?>,
						    animationLoop: true,
						    pauseOnHover: true,
						    directionNav: <?php echo ($instance['display_arrows']) ? 'true' : 'false'; ?>,
						    controlNav: false
					    });
					});
					</script>
					<?php

				}

			} else {

				// Unable to retrieve feed or cached content
				echo $before_widget;
				echo $before_title . 'RealSatisfied Plugin' . $after_title;
				echo '<p>Cannot read feed. Please verify the vanity key is correct.</p>';
				echo $rsw_rss_feed_source->get_error_message();
				echo $after_widget;

			}

		} else {
			echo $before_widget;
			echo $before_title . 'RealSatisfied Plugin' . $after_title;
			echo '<p>no Vanity Key set</p>';
			echo $after_widget;
		}
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("Real_Satisfied_Testimonials_Widget");'));

// Function to limit the number of words displayed in a string
function limit_words($string, $word_limit)
{
    $words = explode(" ",$string);
    return implode(" ",array_splice($words,0,$word_limit));
}

// Add thickbox support
function init_theme_method() {
   add_thickbox();
}
add_action('init', 'init_theme_method');

// Enqueue custom styles and scripts
function rsw_enqueue_styles_and_scripts() {

    wp_enqueue_script( 'flexslider', plugins_url( '/flexslider/jquery.flexslider.js', __FILE__ ), array( 'jquery' ) );
    wp_enqueue_style( 'flexslider-css', plugins_url( '/flexslider/flexslider.css', __FILE__ ) );

    wp_enqueue_style( 'rsw-styles', plugins_url( '/realsatisfied.css', __FILE__ ) );

}
add_action( 'wp_enqueue_scripts', 'rsw_enqueue_styles_and_scripts' );

// Add a button to the admin section to clear the feed cache
function rsw_clear_cache_button() {
	echo '<a href="#ajaxthing" class="myajax button-secondary">Clear the Testimonial Cache</a>';
}

// Clear the feed cache
function rsw_clear_feed_cache_javascript() {
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {

    $('.myajax').click(function(){
        var data = {
            action: 'rsw_clear_feed_cache'
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ajaxurl, data, function(response) {
			alert(response);
		});
	});

});
</script>
<?php
}
add_action('admin_head', 'rsw_clear_feed_cache_javascript');

// Clear the feed cache
function rsw_clear_feed_cache_callback() {
	global $wpdb; // this is how you get access to the database

	add_filter( ‘wp_feed_cache_transient_lifetime’, create_function( '$a', 'return 1;' ) );
	$rsw_rss = fetch_feed( 'http://rss.realsatisfied.com/rss/agent/' . $rsw_real_satisfied_id );
	add_filter( ‘wp_feed_cache_transient_lifetime’, create_function( '$a', 'return 43200;' ) );
	
	$response = 'Cache has been cleared.';
	echo $response;

	exit(); // this is required to return a proper result & exit is faster than die();
}
add_action('wp_ajax_rsw_clear_feed_cache', 'rsw_clear_feed_cache_callback');