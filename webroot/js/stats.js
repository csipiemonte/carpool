var totResults = 0;
var resultsByProvider = new Array();
var q = '';
var baseURL = '';
var providers = '';
var currProvider = '';
var providersData = new Object(); // used later in the async version
var numProcessed = 0;
var numProviders = 0;
var numRequests = 0;

$(function(){

	$('#process').click(function(){
		$(this).css('display', 'none');
		$('#progress').css('display', 'block');
		$('#progress').html('Attendere prego ...');
		searchProviders();
	});

	/**
	 * 
	 */
	function searchProviders() {
		
		providersData = new Object(); // used later in the async version
		numProcessed = 0;
		numProviders = 0;
		numRequests = 0;
		totResults = 0;
		
		providers = $('#providers').val().split(',');
		baseURL = $('#server').val().trim();
		
		var from = $('#from').val().split('\n');
		var punti_partenza = new Array();
		for(var i in from) {
			from[i] = from[i].trim();
			if(from[i] == '') continue;
			var punto_partenza = from[i].split(',');
			if(punto_partenza.length != 3) continue;
			
			punto_partenza[1] = punto_partenza[1].trim();
			punto_partenza[2] = punto_partenza[2].trim();
			
			if( isNaN(punto_partenza[1] || isNaN(punto_partenza[2])) ) continue;
			
			punti_partenza.push({
				location : punto_partenza[0],
				latitude : parseFloat(punto_partenza[1]),
				longitude : parseFloat(punto_partenza[2]),
			});
		}
		
		for(i=0;i<providers.length;i++) {
			if( isNaN(currProvider) ) continue;
			numProviders++;
		}
		
		numRequests = numProviders * punti_partenza.length;

		for(i=0;i<providers.length;i++) {
			currProvider = providers[i];
			if( isNaN(currProvider) ) continue;
			
			for(j=0;j<punti_partenza.length;j++) {
				
				var punto_partenza = punti_partenza[j];
				
				q = 'p[from][latitude]=' + punto_partenza.latitude + '&p[from][longitude]=' + punto_partenza.longitude+ '&is_stat=1';
				
				$.ajax({
					url: baseURL + '/journeys/search_provider/' + currProvider + '.json?' + q,
					dataType: 'json'
				}).always(function(data){
					
					totResults += data.num_results;
					
					if(data.num_pages > 1) { 
						// so già il numero di pagine (quindi il numero potenziale di risultati), mi basta 
						// conoscere il numero esatto di risultati nell'ultima pagina
						if(data.num_pages > 3) { // partono da 1y
							totResults = totResults + (data.num_results * (data.num_pages-2) );
						}
						$.ajax({
							url: baseURL + '/journeys/search_provider/' + data.provider + '.json?' + q + '&page='+data.num_pages,
							dataType: 'json'
						}).always(function(data){
							
							totResults += data.num_results;
							
							updateProgress();
						});
					} 
					else {
						// request completed. Update progress
						updateProgress();
					}
				});
			
			}
			
			
		}
	}
	
	function updateProgress() {
		numProcessed++;
		if( numProcessed == numRequests ) {
			$('#process').css('display', 'block');
			$('#progress').html( 'Risultati: <b>' + totResults + '</b>');
		}
	}
			
}); 
