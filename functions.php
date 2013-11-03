<?php

// ENTER GOOGLE MAPS API KEY BELOW. 
// You can obtain one at: https://code.google.com/apis/console:
    $apiKey = 'YOUR API KEY GOES HERE';

/**
 ** Register website scripts:
 **/
 
// Load jQuery
if ( !is_admin() ) {
   wp_deregister_script('jquery');
   wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"), false);
   wp_enqueue_script('jquery');
}

// Add scripts to footer	
function footer_scripts () {    
    wp_register_script( 'screw_default_buttons', get_template_directory_uri() . '/js/jquery.screwdefaultbuttonsV2.min.js', array( 'jquery' ), 1.0, true );  
    wp_enqueue_script( 'screw_default_buttons' );
    wp_register_script( 'main', get_template_directory_uri() . '/js/main.js', array( 'jquery' ), 1.0, true );  
    wp_enqueue_script( 'main' );
    wp_register_script( 'imgSizer', get_template_directory_uri() . '/js/imgSizer.js', array( 'jquery' ), 1.0, true );  
    wp_enqueue_script( 'imgSizer' );
}  
add_action( 'wp_enqueue_scripts', 'footer_scripts' );

// Add scripts to <head>
function head_scripts () {   
  wp_register_script( 'map_API_key', 'https://maps.googleapis.com/maps/api/js?key=' . $apiKey . '&sensor=true');  
  wp_enqueue_script( 'map_API_key' );
  wp_register_script( 'modernizr', get_template_directory_uri() . '/js/modernizr.custom.51148.js');  
  wp_enqueue_script( 'modernizr' ); 
}  
add_action( 'wp_enqueue_scripts', 'head_scripts', 5 );
	
/**
 ** Customize default settings:
 **/
	
// Add RSS links to <head>
automatic_feed_links();

// Clean up the <head>
function removeHeadLinks() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
  }
  add_action('init', 'removeHeadLinks');
  remove_action('wp_head', 'wp_generator');
  
  // Register NavBar
function register_navBar() {
  register_nav_menu('navBar',__( 'Header Menu' ));
}
add_action( 'init', 'register_navBar' );
  
// Declare sidebar widget zone
  if (function_exists('register_sidebar')) {
    register_sidebar(array(
      'name' => 'Sidebar Widgets',
      'id'   => 'sidebar-widgets',
      'description'   => 'These are widgets for the sidebar.',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<h2>',
      'after_title'   => '</h2>'
    ));
  }
    
  // Remove the named anchor from the "more" links
  function remove_more_link_scroll( $link ) {
  $link = preg_replace( '|#more-[0-9]+|', '', $link );
  return $link;
}
add_filter( 'the_content_more_link', 'remove_more_link_scroll' );
	
/**
 ** Build 'On The Road' customization page under 'Appearance' menu:
 **/
 
// Build the theme menu
function OTR_theme_menu() {

	add_theme_page(
		'On The Road', 			// The title to be displayed in the browser window for this page.
		'On The Road',			// The text to be displayed for this menu item
		'administrator',			// Which type of users can see this menu item
		'OTR_theme_options',	// The unique ID - that is, the slug - for this menu item
		'OTR_theme_display'		// The name of the function to call when rendering this menu's page
	);

} 
add_action( 'admin_menu', 'OTR_theme_menu' );

// Renders a simple page to display for the theme menu defined above.
function OTR_theme_display() {
?>
	<div class="wrap">
	
		<div id="icon-themes" class="icon32"></div>
		<h2>On The Road</h2>
		<?php settings_errors(); ?>
		<h3>Instructions for registering and implementing your Google Maps API key:</h3>
		<ol>
		  <li>Log into your Google account and visit the <a target="_blank" href="https://code.google.com/apis/console">Google APIs Console</a>.</li>
		  <li>Follow the instructions to register your blog, get your key and then copy it to your clipboard.</li>
		  <li>Access your theme editor under Appearance > Editor and open up functions.php.</li>
		  <li>Paste your API key into the specified location at the top of the document. That's it!</li>
		</ol>		
		<form method="post" action="options.php">
			<?php settings_fields( 'theme_options' ); ?>
			<?php do_settings_sections( 'theme_options' ); ?>			
			<?php submit_button(); ?>
		</form>
		
	</div>
<?php
}

function OTR_initialize_theme_options() {
  // Build the section 
	add_settings_section(
		'theme_settings_section',			// ID used to identify this section and with which to register options
		'Theme Options',					// Title to be displayed on the administration page
		'theme_options_callback',	// Callback used to render the description of the section
		'theme_options'		// Page on which to add this section of options
	);
	
	// Next, we'll introduce the fields for toggling the visibility.
	add_settings_field(	
		'polylines',						// ID used to identify the field throughout the theme
		'<strong>Hide Polylines:</strong>',							// The label to the left of the option interface element
		'toggle_polylines_callback',	// The name of the function responsible for rendering the option interface
		'theme_options',	// The page on which this option will be displayed
		'theme_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback.
			'If checked, the dashed lines that connect blog post markers on the map will be hidden.'
		)
	);
	
	add_settings_field(	
		'hide_map',						
		'<strong>Hide Map:</strong>',				
		'toggle_map_callback',	
		'theme_options',					
		'theme_settings_section',			
		array(								
			'If checked, the map will not load automatically on the homepage. It will have to be toggled active.'
		)
	);
	
	// Register the fields with WordPress
	register_setting(
		'theme_options',
		'theme_options'
	);
	
} 
add_action('admin_init', 'OTR_initialize_theme_options');

function theme_options_callback() {
	echo '<p>Here are a couple options for customizing how and when your map will be displayed:</p>';
} 

function toggle_polylines_callback($args) {
	$options = get_option('theme_options');
		
	$html = '<input type="checkbox" id="polylines" name="theme_options[polylines]" value="1"' . checked(1, $options['polylines'], false) . '/>'; 
	$html .= '<label for="polylines"> '  . $args[0] . '</label>'; 	
	echo $html;
	
}

function toggle_map_callback($args) {
	$options = get_option('theme_options');
	
	$html = '<input type="checkbox" id="hide_map" name="theme_options[hide_map]" value="1"' . checked(1, $options['hide_map'], false) . '/>'; 
	$html .= '<label for="hide_map"> '  . $args[0] . '</label>'; 
	echo $html;
	
}

/**
 ** Implement Advanced Custom Fields (http://www.advancedcustomfields.com/), used for setting map options when adding a new post
 **/
 
define( 'ACF_LITE', true );
include_once('advanced-custom-fields/acf.php');

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_locations-post-types',
		'title' => 'Locations & Post Types',
		'fields' => array (
			array (
				'key' => 'field_518c453755e31',
				'label' => 'Tag this blog post to the map',
				'name' => 'map_tag',
				'type' => 'location-field',
				'instructions' => 'Click and drag the marker to the location on the map where you would like this post to be tagged',
				'required' => 1,
				'val' => 'coordinates',
				'center' => '48.856614,2.3522219000000177',
				'zoom' => 16,
				'scrollwheel' => 1,
			),
			array (
				'key' => 'field_5191ac887b0ca',
				'label' => 'Select the post type',
				'name' => 'post_type',
				'type' => 'select',
				'instructions' => 'This will only decide what type of marker appears on the map for your post – posts can still include any media type',
				'required' => 1,
				'multiple' => 0,
				'allow_null' => 0,
				'choices' => array (
					'Text' => 'Text',
					'Photo' => 'Photo',
					'Video' => 'Video',
					'Audio' => 'Audio',
				),
				'default_value' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

?>