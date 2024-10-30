<?php
/*
Plugin Name: Buienradar.nl
Plugin URI: http://kierownik.nl/wordpress-buienradar-plugin-and-widget
Description: Add the current weather from The Netherlands in your posts, pages or add a widget, this is only interesting for people that live in the Netherlands, Belgium or Germany.
Version: 0.4.2
Author: kierownik
Author URI: http://kierownik.nl
License: GPL2
*/

/*  Copyright 2011  kierownik  ( email : rokven@gmail.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Loading language file...
//load_plugin_textdomain('buienradar');
//Hmm, doesn't work if the plugin file has its own directory.
//Let's make it our way... load_plugin_textdomain() searches only in the wp-content/plugins dir.
//Taken example from sitemap plugin
$currentLocale = get_locale();
if( !empty( $currentLocale ) ) {
	$moFile = dirname( __FILE__ ) . "/lang/" . $currentLocale . ".mo";
	if ( @ file_exists( $moFile ) && is_readable( $moFile ) ) load_textdomain( 'buienradar', $moFile );
}

// options
$buienradar_options = array (
	// options for the admin form
	'opt_name' => 'buienradar',
	'data_field_value' => 'buienradar',

	// widget codes
	'buienradar' => array( 
		'name' => 'buienradar',
		'code' => '<iframe src="http://gratisweerdata.buienradar.nl/buienradar.php?type=256x256" noresize scrolling=no hspace=0 vspace=0 frameborder=0 marginheight=0 marginwidth=0 width=256 height=256></iframe>'
		 ),
	'buienradar_inc_prognose' => array( 
		'name' => 'buienradar_inc_prognose',
		'code' => '<iframe src="http://gratisweerdata.buienradar.nl/buienradar.php" noresize scrolling=no hspace=0 vspace=0 frameborder=0 marginheight=0 marginwidth=0 width=256 height=406></iframe>'
		 ),
	'radar_400_400' => array( 
		'name' => 'radar_400_400',
		'code' => '<a href="http://www.buienradar.nl" target="_blank"><img border="0" src="http://www.buienradar.nl/images.aspx?jaar=-3&soort=sp-loop"></a>'
		 ),
	'radar_256_256' => array( 
		'name' => 'radar_256_256',
		'code' => '<a href="http://www.buienradar.nl" target="_blank"><img border="0" src="http://m.buienradar.nl/"></a>'
		 ),
	'radar_215_155' => array( 
		'name' => 'radar_215_155',
		'code' => '<a href="http://www.buienradar.nl" target="_blank"><img border="0" src="http://www3.buienradar.nl/images.aspx?jaar=-3&soort=sb"></a>'
		 ),
	'radar_120_120' => array( 
		'name' => 'radar_120_120',
		'code' => '<a href="http://www.buienradar.nl" target="_blank"><img border="0" src="http://www3.buienradar.nl/buienradarnl-ani120phone.gif"></a>'
		 ),
	'no_predefined' => array( 
		'name' => 'no_predefined',
		'code' => ''
		 )
 );

// Menu name under settings
function buienradar(  ) {

	add_options_page( 'buienradar options', '<img src="'. plugins_url( '/images/buienradar.png', __FILE__ ) .'" alt="" /> Buienradar.nl', 'manage_options', 'buienradar_options', 'buienradar_options' );
}

add_action( 'admin_menu', 'buienradar' );

// Add link to the plugin-row so you can go directly to the plugins options
function buienadar_plugin_action_links( $links, $file ) {
	if ( $file == plugin_basename( dirname(__FILE__).'/buienradar.php' ) ) {
		$links[] = '<a href="options-general.php?page=buienradar_options">'.__( 'Settings' ).'</a>';
	}

	return $links;
}

add_filter( 'plugin_action_links', 'buienadar_plugin_action_links', 10, 2 );

// Options page
function buienradar_options(  ) {
	global $buienradar_options;

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	// Read in existing option value from database
	$checked_1 = get_option( $buienradar_options['opt_name'] ) == $buienradar_options['buienradar']['name'] ? $checked_1 = 'checked="checked"' : $checked_1 = '';
	$checked_2 = get_option( $buienradar_options['opt_name'] ) == $buienradar_options['buienradar_inc_prognose']['name'] ? $checked_2 = 'checked="checked"' : $checked_2 = '';
	$checked_3 = get_option( $buienradar_options['opt_name'] ) == $buienradar_options['radar_400_400']['name'] ? $checked_3 = 'checked="checked"' : $checked_3 = '';
	$checked_4 = get_option( $buienradar_options['opt_name'] ) == $buienradar_options['radar_256_256']['name'] ? $checked_4 = 'checked="checked"' : $checked_4 = '';
	$checked_5 = get_option( $buienradar_options['opt_name'] ) == $buienradar_options['radar_215_155']['name'] ? $checked_5 = 'checked="checked"' : $checked_5 = '';
	$checked_6 = get_option( $buienradar_options['opt_name'] ) == $buienradar_options['radar_120_120']['name'] ? $checked_6 = 'checked="checked"' : $checked_6 = '';
	$checked_no_defined = get_option( $buienradar_options['opt_name'] ) == $buienradar_options['no_predefined']['name'] ? $checked_no_defined = 'checked="checked"' : $checked_no_defined = '';

?>

<div class="wrap">
<h2>Buienradar</h2>

<form name="form1" method="post" action="options.php">
	<?php wp_nonce_field( 'update-options' ); ?>

	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
	</p>

	<table class="form-table">

<!-- row 1 -->
		<tr valign="top">
			<th scope="row" style="text-align: center;">
				<?php _e( 'Do not use a pre-defined weather chart', 'buienradar' ); ?>
			</th>
			<th scope="row" style="text-align: center;">
			</th>
			<th scope="row" style="text-align: center;">
			</th>
		</tr>

<!-- row 2 -->
		<tr valign="top">
			<td style="text-align: center;">
				<input type="radio" name="<?php echo $buienradar_options['data_field_value']; ?>" value="no_predefined"<?php echo $checked_no_defined ?> />
			</td>
			<td style="text-align: center;">
			</td>
			<td style="text-align: center;">
			</td>
		</tr>

<!-- row 3 -->
		<tr valign="top">
			<th scope="row" style="text-align: center;">
				<?php _e( 'BuienRadar', 'buienradar' ); ?>
			</th>
			<th scope="row" style="text-align: center;">
				<?php _e( 'Buienradar <br /> including current temperature and 5 day forecast', 'buienradar' ); ?>
			</th>
			<th scope="row" style="text-align: center;">
				<?php _e( 'Radar 400 x 400', 'buienradar' ); ?>
			</th>
		</tr>

<!-- row 4 -->
		<tr valign="top">
			<td style="text-align: center;">
				<input type="radio" name="<?php echo $buienradar_options['data_field_value']; ?>" value="<?php echo $buienradar_options['buienradar']['name'] ?>"<?php echo $checked_1 ?>/>
			</td>
			<td style="text-align: center;">
				<input type="radio" name="<?php echo $buienradar_options['data_field_value']; ?>" value="<?php echo $buienradar_options['buienradar_inc_prognose']['name'] ?>"<?php echo $checked_2 ?>/>
			</td>
			<td style="text-align: center;">
				<input type="radio" name="<?php echo $buienradar_options['data_field_value']; ?>" value="<?php echo $buienradar_options['radar_400_400']['name'] ?>"<?php echo $checked_3 ?>/>
			</td>
		</tr>

<!-- row 5 -->
		<tr valign="top" style="text-align: center;">
			<td>
				<?php echo $buienradar_options['buienradar']['code']; ?><br /><?php _e( 'To use this chart use the bottom code:', 'buienradar' ) ?><br /><input style="width: 205px;" type="text" name="in_page_code" value="[buienradar]" />
			</td>
			<td>
				<?php echo $buienradar_options['buienradar_inc_prognose']['code']; ?><br /><?php _e( 'To use this chart use the bottom code:', 'buienradar' ) ?><br /><input style="width: 205px;" type="text" name="in_page_code" value="[buienradar_inc_prognose]" />
			</td>
			<td>
				<?php echo $buienradar_options['radar_400_400']['code']; ?><br /><?php _e( 'To use this chart use the bottom code:', 'buienradar' ) ?><br /><input style="width: 205px;" type="text" name="in_page_code" value="[buienradar_radar_400_400]" />
			</td>
		</tr>

<!-- row 6 -->
		<tr valign="top">
			<th scope="row" style="text-align: center;">
				<?php _e( 'Radar 256 x 256', 'buienradar' ); ?>
			</th>
			<th scope="row" style="text-align: center;">
				<?php _e( 'Radar 215 x 155', 'buienradar' ); ?>
			</th>
			<th scope="row" style="text-align: center;">
				<?php _e( 'Radar 120 x 120', 'buienradar' ); ?>
			</th>
		</tr>

<!-- row 7 -->
		<tr valign="top">
			<td style="text-align: center;">
				<input type="radio" name="<?php echo $buienradar_options['data_field_value']; ?>" value="<?php echo $buienradar_options['radar_256_256']['name'] ?>"<?php echo $checked_4 ?> />
			</td>
			<td style="text-align: center;">
				<input type="radio" name="<?php echo $buienradar_options['data_field_value']; ?>" value="<?php echo $buienradar_options['radar_215_155']['name'] ?>"<?php echo $checked_5 ?> />
			</td>
			<td style="text-align: center;">
				<input type="radio" name="<?php echo $buienradar_options['data_field_value']; ?>" value="<?php echo $buienradar_options['radar_120_120']['name'] ?>"<?php echo $checked_6 ?> />
			</td>
		</tr>

<!-- row 8 -->
		<tr valign="top" style="text-align: center;">
			<td>
				<?php echo $buienradar_options['radar_256_256']['code']; ?><br /><?php _e( 'To use this chart use the bottom code:', 'buienradar' ) ?><br /><input style="width: 205px;" type="text" name="in_page_code" value="[buienradar_radar_256_256]" />
			</td>
			<td>
				<?php echo $buienradar_options['radar_215_155']['code']; ?><br /><?php _e( 'To use this chart use the bottom code:', 'buienradar' ) ?><br /><input style="width: 205px;" type="text" name="in_page_code" value="[buienradar_radar_215_155]" />
			</td>
			<td>
				<?php echo $buienradar_options['radar_120_120']['code']; ?><br /><?php _e( 'To use this chart use the bottom code:', 'buienradar' ) ?><br /><input style="width: 205px;" type="text" name="in_page_code" value="[buienradar_radar_120_120]" />
			</td>
		</tr>

	</table>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="buienradar" />

	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
	</p>

	</form>
</div>

<?php

}

// Add option to add it to the content
function filter_buienradar( $content ) {
	global $buienradar_options;
	 $search  = array (
		'buienradar' => '[buienradar]',
		'buienradar_inc_prognose' => '[buienradar_inc_prognose]',
		'radar_400_400' => '[buienradar_radar_400_400]',
		'radar_256_256' => '[buienradar_radar_256_256]',
		'radar_215_155' => '[buienradar_radar_215_155]',
		'radar_120_120' => '[buienradar_radar_120_120]'
	);
	
	foreach ( $search as $name => $code) {
		$content = str_ireplace( $code, $buienradar_options[$name]['code'], $content );
	}
	return $content;
}
add_filter ( 'the_content', 'filter_buienradar' );

/**
 * Buienradar Widget Class
 */
class buienradar_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function buienradar_widget(  ) {
		$widget_ops = array ( 'classname' => 'BuienRadar', 'description' => __( "With this Widget you can show the current weather in The Netherlands", 'buienradar' ) );
		$this->WP_Widget( 'BuienRadar', 'BuienRadar', $widget_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		global $buienradar_options;
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
			if ( $title ) {
				echo $before_title . $title . $after_title;

				echo '<div style="text-align: center;"><br />';
				echo $buienradar_options[get_option( $buienradar_options['opt_name'] )]['code'];
				echo '</div>';

				echo $after_widget;
			} else {
				echo $before_title . 'BuienRadar' . $after_title;

				echo '<div style="text-align: center;"><br />';
				echo $buienradar_options[get_option( $buienradar_options['opt_name'] )]['code'];
				echo '</div>';

				echo $after_widget;
			}
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 */
	function form( $instance ) {
		$title = esc_attr( $instance['title'] );

?>
			<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
<?php

	}

} // class Buienradar Widget

// register buienradar_widget widget
add_action( 'widgets_init', create_function( '', 'return register_widget( "buienradar_widget" );' ) );

?>