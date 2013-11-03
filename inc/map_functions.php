<!-- Gather and organize data for markers and map boundaries -->
<div id="markers">
  <div id="markersObject">
    <?php if ( have_posts() ) :
        $i = 1; while ( have_posts() ) : the_post();
          $coords = get_field('map_tag');
          $icon = get_field('post_type');
          if ($coords && $icon) :
            echo "latlng:"; the_field('map_tag'); 
            echo "name:" . html_entity_decode(get_the_title(),ENT_QUOTES,'UTF-8') . 
            "permalink:" . get_permalink() .
            "icon:" . get_field('post_type') . "*";
          endif;
        $i++; endwhile;
    endif; ?>
  </div>
  <div id="photoIcon"><?php echo get_template_directory_uri() .'/images/icons/photo.png'; ?></div>
  <div id="textIcon"><?php echo get_template_directory_uri() .'/images/icons/text.png'; ?></div>
  <div id="videoIcon"><?php echo get_template_directory_uri() .'/images/icons/video.png'; ?></div>
  <div id="audioIcon"><?php echo get_template_directory_uri() .'/images/icons/audio.png'; ?></div>
  <div id="toggleIcon"><?php echo get_template_directory_uri() .'/images/styles/radio_4.jpg'; ?></div>
  <?php  
    // If Hide Polylines is checked in (Appearance > On The Road),
    // we'll create an element that tells main.js to hide the polylines
    $polyline_display = get_option('theme_options');
    if ($polyline_display['polylines']) { ?>
      <div id="hidePolylines" style="display:none;"></div>
  <?php } ?>
</div>  
