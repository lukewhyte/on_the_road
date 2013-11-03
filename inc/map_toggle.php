<nav id="mapToggle">
	<ul>
		<li class="mapToggleText">view map</li>
		<li><input type="radio" name="toggleMap" id="mapBtn" class="mapBtn"></li>
		<li><input type="radio" name="toggleMap" id="blogBtn" class="blogBtn"></li>
		<li class="mapToggleText">view blog</li>
	</ul>
</nav>
<?php // If "Hide Map" is checked in 'Appearance > On The Road',
      // create a hidden element that tells 'main.js' to keep the map hidden
  $map_display = get_option('theme_options');
  if ($map_display['hide_map']) { ?>
    <div id="noMap" style="display:none;"></div>
<?php } ?>