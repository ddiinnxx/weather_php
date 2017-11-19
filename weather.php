<?php

	// git clone https://github.com/ddiinnxx/weather_php 
	// more at www.ddiinnxx.com 

	// various defualts. get the right keys
	// check https://developers.google.com/maps/documentation/static-maps/
	$googleapi_key = "GOOGLE_MAPS_KEY";
	// http://api.wunderground.com/weather/api/d/docs
	$wunderground_key = "YOUR_KEY";
	
	// enter location for which you check weather most often. avoids frequent geo-lookup. 
	$def_location = "IA/Cedar_Rapids";
	$p_lat = "41.9650539";
	$p_long = "-91.802762";
	
	// if new latitude and longitude were sent in params, use them instead of the defaults. 
	// the re-locate button send lat long of your current location and call this php file again
	if (isset($_GET["lat"]) && isset($_GET["long"])) {
		$p_lat = $_GET["lat"];
		$p_long = $_GET["long"];
		
		$location_url = "http://api.wunderground.com/api/" . $wunderground_key . "/geolookup/q/" . $p_lat . "," . $p_long . ".json";
		// get location for the lat long sent via params
		$location_string = file_get_contents($location_url);

		$parsed_loc_json = json_decode($location_string);
		
		// name of state. eg: NY, MA, etc
		$loc_state = $parsed_loc_json->{'location'}->{'state'};
		
		// name of city. eg: New York, Boston
		$loc_city_raw = $parsed_loc_json->{'location'}->{'city'}; 
		
		// replace blanks with _. This is how the wunderground API understand multiworded cities.
		$loc_city = str_replace(" ", "_", $loc_city_raw); 
		
		$condition_url = "http://api.wunderground.com/api/" . $wunderground_key . "/conditions/q/" . $loc_state . "/" . $loc_city . ".json";
		
		// get weather conditions from wunderground
		$json_string = file_get_contents($condition_url);
	}
	else
	{
		$condition_url = "http://api.wunderground.com/api/" . $wunderground_key . "/conditions/q/" . $def_location . ".json";
		
		// get weather conditions of default location from wunderground
		$json_string = file_get_contents($condition_url);
	
	}

	$parsed_json = json_decode($json_string);

	$co_location = $parsed_json->{'current_observation'}->{'display_location'}->{'full'};
	$co_city = $parsed_json->{'current_observation'}->{'display_location'}->{'city'};
	$co_temp_str = $parsed_json->{'current_observation'}->{'temperature_string'};
	$co_feels_like = $parsed_json->{'current_observation'}->{'feelslike_string'};
	$co_wind_str = $parsed_json->{'current_observation'}->{'wind_string'};
	$co_rel_humidity = $parsed_json->{'current_observation'}->{'relative_humidity'};
	$co_precip_str = $parsed_json->{'current_observation'}->{'precip_today_string'};

	echo "<html>"; echo "\n"; echo "\n";
	echo "<head>\n"; echo "\n";
	// using a google font
	echo '<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet">'; echo "\n";
	// we have a css style sheet
	echo "<link rel='stylesheet' type='text/css' href='./weather.css'>"; echo "\n";
	echo "<title>${co_feels_like}</title>"; echo "\n";
	// using viewport ot make sure page is displayed fine in mobile too
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">'; echo "\n";
	// for iOS. icon to be used when someone adds the page to their home screen
	echo '<link href="apple-touch-icon.png" rel="apple-touch-icon" />'; echo "\n";
	// get location script using getCurrentPosition function of browser. will pass the lat long as
	// param to this page via URL and reload it using new URL.
	// NOTE: Re-Locate will work only in HTTPS pages
	echo '<script>'; echo "\n";
	echo 'function getLocation() {'; echo "\n";
	echo '		if (navigator.geolocation) {'; echo "\n";
	echo '				navigator.geolocation.getCurrentPosition(showPosition);'; echo "\n";
	echo '		}'; echo "\n";
	echo '}'; echo "\n";
	echo 'function showPosition(position) {'; echo "\n";
	echo '		window.location.href = "index.php?lat=" + position.coords.latitude + "&long=" + position.coords.longitude;'; echo "\n";
	echo '}'; echo "\n";
	echo '</script>'; echo "\n";
	echo "</head>"; echo "\n";
	echo "<body>"; echo "\n";
	echo "<h1>${co_location}</h1>"; echo "\n";
	// NOTE: Re-Locate will work only in HTTPS pages
	echo '<button id="relocate_btn" class="button" onclick="getLocation()">Re-Locate</button>'; echo "\n";
	echo '<script>'; echo "\n";
	// in case we are not on https, better hide the button as it will not work anyway
	echo "	if (location.protocol != 'https:') {"; echo "\n";
	echo '	document.getElementById("relocate_btn").style.display = "none";'; echo "\n";
	echo '}'; echo "\n";
	echo '</script>'; echo "\n";
	echo "<p><b>Now: </b>${co_temp_str}</p>"; echo "\n";
	echo "<p><b>Feels like: </b>${co_feels_like}</p>"; echo "\n";
	// load a static map of the location to give a good idea of where the wind is coming in from
	echo '<img src="https://maps.googleapis.com/maps/api/staticmap?center=' . $p_lat . ',' . $p_long . '&zoom=15&size=300x300&maptype=roadmap&key=' . $googleapi_key . '" />'; echo "\n";
	echo "<p><b>Winds: </b>${co_wind_str}</p>"; echo "\n";
	echo "<p><b>Humidity: </b>${co_rel_humidity}</p>"; echo "\n";
	echo "<p><b>Precipitation: </b>${co_precip_str}</p>"; echo "\n";
	echo "<hr />"; echo "\n";
	// thanking wunderground for their free and useful API
	echo "<p><img src='https://icons.wxug.com/logos/JPG/wundergroundLogo_4c.jpg' alt='Weather Underground' height='50'/></p>"; echo "\n";
	echo "</body>"; echo "\n";
	echo "</html>"; echo "\n";
?>
