<?php
/*
Plugin Name: Friday Morning Report
Plugin URI: http://fridaymorningreport.tv
Description: Displays the archive of the Friday Morning Report shows.
Author: Abesh Bhattacharjee (on Oliver Kohl's Code Base)
Version: 1.0
Author URI: http://blog.abesh.net
Wordpress: Version 2.5+
*/


define("FMR_FEED_URL", "http://fridaymorningreport.tv/video_archive.php");

// This gets called at the plugins_loaded action
function widget_fmr_init() {
	
	// Check for the required API functions
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;

	// This saves options and prints the widget's config form.
	function widget_fmr_control() {
		$options = $newoptions = get_option('widget_fmr');
		if ( $_POST['fmr-submit'] ) {
			$newoptions['shownumberitems'] = $_POST['fmr-show-numberitems'];
			$newoptions['showdate'] = isset($_POST['fmr-show-date']);
			$newoptions['showviews'] = isset($_POST['fmr-show-views']);
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_fmr', $options);
		}
		$fmrshowdate = $options['showdate'] ? 'checked="checked"' : '';
		$fmrshowviews = $options['showviews'] ? 'checked="checked"' : '';
	?>
				<div style="text-align:right">
				<label for="fmr-show-numberitems" style="line-height:35px;display:block;">Number of blog posts to show: 
						<select id="fmr-show-numberitems" name="fmr-show-numberitems">
								 <option value="5" <?php selected('5',$options['shownumberitems']); ?>>5</option>
								 <option value="10" <?php selected('10',$options['shownumberitems']); ?>>10</option>
								 <option value="15" <?php selected('15',$options['shownumberitems']); ?>>15</option>
								 <option value="20" <?php selected('20',$options['shownumberitems']); ?>>20</option>
						</select>
				</label>
				<label for="fmr-show-date">Show FMR show date ? <input class="checkbox" type="checkbox" <?php echo $fmrshowdate; ?> id="fmr-show-date" name="fmr-show-date" /></label><br/>
				<label for="fmr-show-views">Show FMR show views ? <input class="checkbox" type="checkbox" <?php echo $fmrshowviews; ?> id="fmr-show-views" name="fmr-show-views" /></label><br/>
				<input type="hidden" name="fmr-submit" id="fmr-submit" value="1" />
				</div>
	<?php
	}

	// This prints the widget
	function widget_fmr($args) {
		extract($args);
		$options = (array) get_option('widget_fmr');
    $linktarget = ' target="_blank"';           
    $title = "Friday Morning Report";

?>
    <?php echo $before_widget; ?>
    <?php echo $before_title . $title . $after_title; ?>
    <div id="fmr-box" style="margin:0;padding:0;border:none;">
<?php
            require_once('simplepie.inc');
            $feed = new SimplePie();
            $feed->set_feed_url(FMR_FEED_URL);
            $feed->enable_cache(false);
            $feed->init();
            $feed->handle_content_type();
          
?>
         
        <strong>Video Archives</strong>
        <ul>
<?php
        
            foreach ($feed->get_items(1, $options['shownumberitems']) as $item) {
        
                    $title = $item->get_title();
                    $link = $item->get_permalink();
                    $pubDate = $item->get_date('jS F o');
                    $views = $item->get_item_tags('', 'views');

?>
        <li>
        <table border="0">
        <tr>
        <td style="vertical-align:middle"><img src="<?php echo $feed->get_favicon(); ?>"></td>
        <td>
        <a href="<?php echo $link; ?>"  title="<?php echo $title; ?>"<?php echo $linktarget; ?>><?php echo $title; ?></a>
<?php

                if ($options['showdate']) { 

?>
            <br /><small><?php echo $pubDate; ?></small>
<?php

                }

                if ($options['showviews']) {

?>
            <br /><small><?php echo $views[0]['data']; ?></small> views.
            </td>
            </tr>
            </table>
            </li>            
<?php

                }
            }
?>

        <li><a href="http://fridaymorningreport.tv/videos.php"<?php echo $linktarget; ?>>More...</a></li>
        </ul></p>
        </div>
		<?php echo $after_widget; ?>
<?php

	}

 
    
	// Tell Dynamic Sidebar about our new widget and its control
	register_sidebar_widget('Friday Morning Report', 'widget_fmr');
	register_widget_control('Friday Morning Report', 'widget_fmr_control', 300, 220);
}

// Delay plugin execution to ensure Dynamic Sidebar has a chance to load first
add_action('plugins_loaded', 'widget_fmr_init');

?>
