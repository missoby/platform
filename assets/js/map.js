function initialize() {
		var homeLatlng = new google.maps.LatLng(43.643063,-72.311929);

		var myOptions = {
			zoom: 17,
			center: homeLatlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

		var image = new google.maps.MarkerImage(
			'http://maps.google.com/mapfiles/ms/micons/green-dot.png',
			new google.maps.Size(32, 32),	// size
			new google.maps.Point(0,0),	// origin
			new google.maps.Point(16, 32)	// anchor
		);

		var shadow = new google.maps.MarkerImage(
			'http://maps.google.com/mapfiles/ms/micons/msmarker.shadow.png',
			new google.maps.Size(59, 32),	// size
			new google.maps.Point(0,0),	// origin
			new google.maps.Point(16, 32)	// anchor
		);

		var homeMarker = new google.maps.Marker({
			position: homeLatlng, 
			map: map,
			title: "this is the location",
			icon: image,
			shadow: shadow
		});
	}

	google.maps.event.addDomListener(window, 'load', initialize);