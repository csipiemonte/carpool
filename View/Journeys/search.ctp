<noscript>
	<style>
		.opticities { display:none; }
	</style>
</noscript>
<?php
	$l = Configure::read('Config.language');
?>
<span id="curr-locale" style="display:none"><?php echo $l;?></span> <!--usato per js-->
<!--<?php if($l == 'ita'):?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&language=it"></script>
<?php else:?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&language=en"></script>
<?php endif;?>-->

<?php echo $this->Html->script('lonLat.autocomplete-1.1', array('inline' => false));?>  


	<div class="row">
		<div class="col-md-3 pulsantiera pull-right">
			<a href="<?php echo __('scarica_widget_url');?>" class="btn btn-primary pull-right carpooling" id="btn-embed" title="<?php echo __("Copia il widget e inseriscilo nel tuo sito");?>">
				<span class="hidden"><?php echo __("Copia il widget e inseriscilo nel tuo sito");?></span>
			</a>
		</div>
	</div>  
	
	<div class="row">
		<div class="col-md-12">
			<h4 class="cerca_passaggi"><?php echo __('Cerca Passaggi su tutti i fornitori');?></h4>
		</div>
	</div>

	<!-- search in progress -->
	<?php if(isset($q)):?>
	
		<div class="row jumbotron">
			<!--<div>
				<h4><?php echo __('Ricerca passaggi');?></h4>
				<h3><b><span id="provider-name"></span></b></h3>
			</div>-->
			<div class="progress">
				<div id="search-progress" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
					<span class="sr-only"><?php echo __('Ricerca completata al');?> <span id="progress-percentage">0</span>%;</span>
				</div>
			</div>
			
			<div>
				<span><?php echo __('Operazione in corso. Attendere prego');?> <?php echo $this->Html->image('loading.gif');?></span>
			</div>
		</div>
	
		<?php $this->Html->scriptStart(array('inline' => false)); ?>
			
			$(function(){
				
				searchProviderBaseUrl = '<?php echo $this->Html->url(array('action' => 'search_provider'), true);?>';
				q = '<?php echo $q;?>';
				providers = jQuery.parseJSON('<?php echo $providers;?>');
				providersData = new Object(); // used later in the async version
				
				numProviders = providers.length;
				numProcessed = 0;
				
				percentStep = Math.floor( 100/providers.length );
				currPercentage = 0;
				
				// sync version
				
					// loop over providers 
					// NOTE: do NOT use async:false to accomplish this!
					// When using aync:false in fact IE freezes for the whole time (basically
					// it freezes until 100%, it displays for just a moment 100% and redirects)
					// thus must emulate sequential requests using standard async ajax calls
				
					//searchNextProvider();
				
				// END of sync version
				
				
				// async version
					searchProviders();
				// END of async version
			});
			
			function searchProviders() {
				for(i=0;i<providers.length;i++) {
					var currProvider = providers[i];
					$.ajax({
						url: searchProviderBaseUrl + '/' + currProvider['Provider']['id'] + '.json?' + q,
						dataType: 'json'
					}).always(function(data){
						if(data.num_pages > 1) { 
							// paginated result.  Search successive pages in parallel
							providersData[data.provider] = new Object();
							providersData[data.provider].successivePagesToProcess = data.num_pages-1;
							for(j=2;j<=data.num_pages;j++) {
								$.ajax({
									url: searchProviderBaseUrl + '/' + currProvider['Provider']['id'] + '.json?' + q + '&page='+j,
									dataType: 'json'
								}).always(function(data){
									providersData[data.provider].successivePagesToProcess--;
									if( providersData[data.provider].successivePagesToProcess == 0 ) {
										// request completed. Update progress
										updateProgress();
									}
								});
							}
						} 
						else {
							// request completed. Update progress
							updateProgress();
						}
					});
				}
			}
			
			function searchNextProvider() {
				
				var currProvider = providers.pop();
				numProcessed++;
				$('#provider-name').text( /*currProvider['Provider']['name'] +*/ ' (' + (numProcessed) + '/' + numProviders + ')' );
				
				$.ajax({
					url: searchProviderBaseUrl + '/' + currProvider['Provider']['id'] + '.json?' + q,
					dataType: 'json'
				}).
				always(function(){ 
					// request completed. Update progress
					updateProgress();
				});
			}
			
			function updateProgress() {
				numProcessed++;
				currPercentage += percentStep;
				$('#search-progress').attr('aria-valuenow', currPercentage);
				$('#search-progress').attr('style', 'width:' + currPercentage + '%');
				$('#progress-percentage').text(currPercentage);
							
				if( numProcessed == providers.length ) {
					// redirect to search results
					window.location = '<?php echo $this->Html->url(array('action' => 'search_result', '?' => array('layout' => $layout)), true);?>';
				}
			}
			
		<?php $this->Html->scriptEnd(); ?>
	
	<!-- search form -->
	<?php else:?>

		<?php echo $this->Form->create('Journey');?>

		<div class="row">

			<?php 
				// Disabiltato nella prima ricerca sia perchè non supportato da Blablacar sia 
				// perchè in questo modo quando si filtra una precedente tratta non devo rifare
				// la ricerca sui fornitori esterni ma posso filtrare localmente
				// echo $this->Form->input('radius', array('type' => 'hidden', 'value' => 10));
			?>

			<div class="col-md-5 form-group">
			<?php
				echo $this->Form->input('from.latitude', array('type' => 'hidden'));
				echo $this->Form->input('from.longitude', array('type' => 'hidden'));
				echo $this->Form->input('from.fulladdress', array('required' => TRUE, 'label' => __('Da *'), 'placeholder' => __('Indirizzo'), 'class' => 'form-control input-block'));
			?>
				<div id="from-alert-container"></div>
			</div>	

			<div id="switch-end-points-cont" class="col-md-2 form-group inverti">
				<?php $search_criteria = $this->Session->read('search_criteria');?>
				<?php if(true): // 20160128 - richiesta CSI:sempre visibile ($search_criteria):?>
				<a id="switch-end-points" href="#"><span class="hidden"><?php echo __("inverti il percorso");?></span></a>
				<?php endif;?>
			</div>

			<div class="col-md-5 form-group">
			<?php
				echo $this->Form->input('to.latitude', array('type' => 'hidden'));
				echo $this->Form->input('to.longitude', array('type' => 'hidden'));
				echo $this->Form->input('to.fulladdress', array('required' => TRUE, 'label' => __('A *'), 'placeholder' => __('Indirizzo'), 'class' => 'form-control input-block'));
			?>
				<div id="to-alert-container"></div>
			</div>
		</div>

		<br/><br/>

		<div class="row">
            <div class="col-md-3 col-xs-6">                
                <label for="outwardMindate"><?php echo __("Dal");?></label>
				<div class="input text">
					<input type="text" id="outwardMindate" value="" style="background-color: #fff !important; cursor: text !important;" readonly="readonly" class="form-control datepicker" name="data[outward][mindate]">
					<span id="outwardMindate-icon" aria-hidden="true" class="glyphicon glyphicon-calendar form-control-feedback"></span>
				</div>
            </div>
            <div class="col-md-3 col-xs-6">                
                <label for="outwardMaxdate"><?php echo __("Al");?></label>
				<div class="input text">
					<input type="text" id="outwardMaxdate" value="" style="background-color: #fff !important; cursor: text !important;" readonly="readonly" class="form-control datepicker" name="data[outward][maxdate]">
					<span id="outwardMaxdate-icon" aria-hidden="true" class="glyphicon glyphicon-calendar form-control-feedback"></span>
				</div>
            </div>   
            <div class="col-md-6 col-xs-12">
				<?php echo $this->Html->link(__('Cerca'), '#', array('class' => 'btn btn-primary pull-right', 'id' => 'btn-search'));?>
            </div>         
        </div>
	
		<div class="row">
			<div class="col-md-10">
				<br>
				<em><small><?php echo __('* Campo obbligatorio');?></small></em>
			</div>
		</div>
		
		<div class="row">
            <div class="col-md-10">
                <br>
                <em><small><?php echo __("Restringere il periodo di ricerca per ridurre i tempi di risposta");?></small></em>
			</div>
        </div>
		
		<?php echo $this->Form->end();?> 
		<br><br>

		<?php $this->Html->scriptStart(array('inline' => false)); ?>
			$(function(){
				
				var geocoder = null;//new google.maps.Geocoder();
				
				lonLatAutocomplete(geocoder, 'from');
				lonLatAutocomplete(geocoder, 'to');
				
				$('#btn-search').click(function(e){
					e.preventDefault();
					if( $('#fromLatitude').val() == '' ) {
						$('#from-alert-container').html('<div class="alert alert-info alert-dismissible" role="alert"> \
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> \
							<?php echo __('Nessun indirizzo di partenza selezionato');?> \
						</div>');
						return;
					}
					if( $('#toLatitude').val() == '' ) {
						$('#to-alert-container').html('<div class="alert alert-info alert-dismissible" role="alert"> \
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> \
							<?php echo __('Nessun indirizzo di destinazione selezionato');?> \
						</div>');
						return;
					}
					$('#JourneySearchForm').submit();
				});
				
				$('.datepicker').datepicker({
					dateFormat: "dd-mm-yy",
					dayNames: [ "<?php echo __("Domenica");?>", "<?php echo __("Lunedi");?>", "<?php echo __("Martedi");?>", "<?php echo __("Mercoledi");?>", "<?php echo __("Giovedi");?>", "<?php echo __("Venerdi");?>", "<?php echo __("Sabato");?>" ],
					dayNamesMin: [ "<?php echo __("Do");?>", "<?php echo __("Lu");?>", "<?php echo __("Ma");?>", "<?php echo __("Me");?>", "<?php echo __("Gi");?>", "<?php echo __("Ve");?>", "<?php echo __("Sa");?>" ],
					dayNamesShort: [ "<?php echo __("Dom");?>", "<?php echo __("Lun");?>", "<?php echo __("Mar");?>", "<?php echo __("Mer");?>", "<?php echo __("Gio");?>", "<?php echo __("Ven");?>", "<?php echo __("Sab");?>" ],
					monthNames: [ "<?php echo __("Gennaio");?>", "<?php echo __("Febbraio");?>", "<?php echo __("Marzo");?>", "<?php echo __("Aprile");?>", "<?php echo __("Maggio");?>", "<?php echo __("Giugno");?>", "<?php echo __("Luglio");?>", "<?php echo __("Agosto");?>", "<?php echo __("Settembre");?>", "<?php echo __("Ottobre");?>", "<?php echo __("Novembre");?>", "<?php echo __("Dicembre");?>" ],
					monthNamesShort: [ "<?php echo __("Gen");?>", "<?php echo __("Feb");?>", "<?php echo __("Mar");?>", "<?php echo __("Apr");?>", "<?php echo __("Mag");?>", "<?php echo __("Giu");?>", "<?php echo __("Lug");?>", "<?php echo __("Ago");?>", "<?php echo __("Set");?>", "<?php echo __("Ott");?>", "<?php echo __("Nov");?>", "<?php echo __("Dic");?>" ],
					firstDay: 1
				});
				
				$('#outwardMindate-icon').click(function(){
					$('#outwardMindate').datepicker('show');
				});
				
				$('#outwardMaxdate-icon').click(function(){
					$('#outwardMaxdate').datepicker('show');
				});
				
				$('#switch-end-points').click(function(e){
					e.preventDefault();
					
					var tmp = '';
					
					tmp = $('#fromFulladdress').val();
					$('#fromFulladdress').val( $('#toFulladdress').val() );
					$('#toFulladdress').val(tmp);
					
					tmp = $('#fromLatitude').val();
					$('#fromLatitude').val( $('#toLatitude').val() );
					$('#toLatitude').val(tmp);
					
					tmp = $('#fromLongitude').val();
					$('#fromLongitude').val( $('#toLongitude').val() );
					$('#toLongitude').val(tmp);
				});
			});
		<?php $this->Html->scriptEnd(); ?>

	<?php endif;


