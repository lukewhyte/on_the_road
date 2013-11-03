/*
 * The functions addMapData() and initialize() create and populate the map appropriately. They're triggered by toggleMap()
 */

function addMapData(map) { // addMapData() is called by initalize()
  
  // between() returns the portion of 'string' between 'start' and 'end'
  function between (string, start, end) {
    start = string.indexOf(start) + start.length;
    if (arguments.length < 3) {
      return string.slice(start);
    } else {
      end = string.indexOf(end, start);
      return string.slice(start, end);
    }
  }
    
  // createMarkerObjects() will take the array defined in createMarkersArray() 
  // and separate its elements' data into key/value pairs for use on the map
  function createMarkerObjects (element) {
    var coords = between(element, "latlng:", "name:"),
        bits = coords.split(/,\s*/),
        markerObjects = {
          latlng: new google.maps.LatLng(parseFloat(bits[0]),parseFloat(bits[1])),
          name: between(element, "name:", "permalink:"),
          permalink: between(element, "permalink:", "icon:"),
          icon: between(element, "icon:")
        };
    return markerObjects;
  }

  // createMarkersArray breaks up the string of data returned from 'map_functions.php' into an array. 
  // Each array element contains the map/marker data for a specific post
  function createMarkersArray () {
    var rawString = $("#markersObject").html(),
        markersArray = rawString.split("*");
    markersArray.pop(); // The last element is empty
    for (i = 0; i < markersArray.length; i++) {
      markersArray[i] = createMarkerObjects(markersArray[i]);
    }
    return markersArray;
  }
  
  // markersArray holds the array created via createMarkersArray()
  var markersArray = createMarkersArray();
      
  // the dynamicData object creates variables (with unique values for each marker) used by each marker on the map. 
  var dynamicData = {
    points: [],   // "points" will hold the marker coordinates
    bounds: new google.maps.LatLngBounds(), // "bounds" defines the edges of the viewable map, eg. the zoom level
    image: "",  // the variable for the marker icons
    markers: "", // variable for the actual markers
    polylines: "" // the polylines are dashed lines running between the markers
  };
  
  // the staticData object is also used by the markers on the map. Unlike dynamicData, however, its properties
  // always contain the same values
  var staticData = {
    lineSymbol: { // "lineSymbol" creates the dashed lines in polylines
      path: 'M 0,-1 0,1',
      strokeOpacity: 1,
      scale: 2.5
    },
    markerIcons: {// Grab the marker icons' images
      photo: document.getElementById("photoIcon").innerHTML,
      text: document.getElementById("textIcon").innerHTML,
      video: document.getElementById("videoIcon").innerHTML,
      audio: document.getElementById("audioIcon").innerHTML
    }
  };

  // this statement will loop through each marker and assign the corresponding values to the dynamicData variables
  for (var i = 0; i < markersArray.length; i++) {
    var current = markersArray[i];
    dynamicData.points[i] = current.latlng;
    dynamicData.bounds.extend(current.latlng);
  
    // Assign the marker icons to their associated post types     
    switch (current.icon) {
      case "Photo":
        dynamicData.image = staticData.markerIcons.photo; 
        break;
      case "Video":
        dynamicData.image = staticData.markerIcons.video; 
        break;
      case "Audio":
        dynamicData.image = staticData.markerIcons.audio; 
        break;
      case "Text":
      default:
        dynamicData.image = staticData.markerIcons.text; 
        break;
    }

    // create the markers
    dynamicData.markers = new google.maps.Marker({
      position: dynamicData.points[i],
      map: map,
      title: current.name,
      icon: dynamicData.image,
      link: current.permalink
    });

    google.maps.event.addListener(dynamicData.markers, 'click', function () {
      window.location.href = this.link;
    });
  }

  dynamicData.points.reverse(); // Blog posts are listed from newest to oldest, but polylines need to move from oldest to newest on map

  // create the polylines
  if ($('#hidePolylines').length === 0) {
    dynamicData.polylines = new google.maps.Polyline({
      path: dynamicData.points,
      strokeColor: '#d00000',
      strokeOpacity: 0,
      icons: [{
        icon: staticData.lineSymbol,
        offset: '0',
        repeat: '15px'
      }]
    });
    dynamicData.polylines.setMap(map);
  }

  // Set the map boundaries, being carefull not to zoom in too far on only one marker
  var edge = dynamicData.bounds;
  if (edge.getNorthEast().equals(edge.getSouthWest())) {
     var extendPoint1 = new google.maps.LatLng(edge.getNorthEast().lat() + 0.01, edge.getNorthEast().lng() + 0.01);
     var extendPoint2 = new google.maps.LatLng(edge.getNorthEast().lat() - 0.01, edge.getNorthEast().lng() - 0.01);
     edge.extend(extendPoint1);
     edge.extend(extendPoint2);
  }
  map.fitBounds(edge);
}

// Create the map
function initialize () {
  var mapOptions = {
    zoom: 0,
    center: new google.maps.LatLng(0,0),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  var map = new google.maps.Map(document.getElementById("map"), mapOptions);
  addMapData(map);
}

/*
 * The rest of these functions set the parameters surrounding the map's size and state
 * in respect to the rest of the page
 */
$(function () {

  // Calculate the height of the map and width of the map
	function mapSize () {
    var $theWindow = $(window),
        windowHeight = $theWindow.height(),
        $map = $('#map'),
        currentClass = $map.attr('class'); 
        
    switch (currentClass) {
      case "searchMap":
        $map.height(300);
        break;
      case "single-post-map":
        $map.css({
          "height": 250,
          "min-height": 250
        });
        break;
      default:
        if ($theWindow.width() < 530) {
          $map.width(100 + "%").height(windowHeight - 118);
        } else {
          $map.height(windowHeight - 249);
        }
    }
  }

  // Set up the map and the radio buttons in respect to the settings in 'Appearance > On The Road'.
  function showOrHideMap () {
    var $mapWrap = $('#mapWrap'),
        toggleIcon = $('#toggleIcon').html();
    
    // Grab the custom radio buttons from jquery.screwdefaultbuttons.js plugin
    $('#mapToggle input').screwDefaultButtons({
      image: 'url("' + toggleIcon + '")',
      width: 28,
      height: 28
    });
    
    // Show or hide the map onLoad and check the appropriate button
    if ($('#noMap').length === 0) {
      $('#mapBtn').screwDefaultButtons("check");
      $mapWrap.show();
      initialize(); // Fire up the map
    } else {
      $('#blogBtn').screwDefaultButtons("check");
    }
    
    $('#mapToggle').show();	// Display the custom radio buttons
    
    // After all has loaded, use event handlers to dynamically load content based on radio buttons
    $(".mapBtn").click(function(){
      if ($mapWrap.css('display') == 'none') {
        $mapWrap.show();
        initialize(); // Fire up the map
        $('#mapSpacer').hide();
        return false;
      }
    });
    $(".blogBtn").click(function(){
      $mapWrap.hide();
      $('#mapSpacer').show();
      return false;
    });
  }
  
  // Execute DOM ready functions
  mapSize();
  showOrHideMap();
});