function lonLatAutocomplete(geocoder, prefixID)
{
	$('#'+prefixID+'Fulladdress').autocomplete({
		//This bit uses the geocoder to fetch address values
		source: function(request, response) {
			$.get( "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBMi6h63xLTEhOKXchLbRJVHjU1MIjE__I&address="+request.term+'&components=country:it')
			.done(function(result) {
				if(result.status == 'OK') {
					if (result.results) {
						result = result.results;
						response($.map(result, function(item) {
							return {
								label:  item.formatted_address,
								value: item.formatted_address,
								latitude: item.geometry.location.lat,
								longitude: item.geometry.location.lng
							}
						}));
					} 
				}
				else {
					if( $('#curr-locale').text() == 'ita' )	alert('Località non presente');
					else alert('Location not available');
				}
			})
			.fail(function() {
				alert( "Errore di geolocalizzazione. Si prega di riprovare" );
			})
			.always(function() {
				
			});
			/*geocoder.geocode( { address: request.term, componentRestrictions: {
				country: 'IT'
			}}, function(result, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					response($.map(result, function(item) {
		   				return {
		   					label:  item.formatted_address,
		   					value: item.formatted_address,
		   					latitude: item.geometry.location.lat(), //item.geometry.location.lat,
		   					longitude: item.geometry.location.lng() //item.geometry.location.lng
		  				}
		  			}));
				} else {
					if( $('#curr-locale').text() == 'ita' )	alert('Località non presente');
					else alert('Location not available');
				}
			});*/

		},
		//This bit is executed upon selection of an address
		select: function(event, ui) {
			
			// populate hidden fields
			$('#'+prefixID+'Latitude').val( ui.item.latitude );
			$('#'+prefixID+'Longitude').val( ui.item.longitude );
			
			// remove any existing alert (if we previously tried to search without coordinates set) 
			$('#' + prefixID.toLowerCase() + '-alert-container').html('');
	  	}
	});
}

