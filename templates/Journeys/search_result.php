<?php use Cake\I18n\Time;
use Cake\Utility\Hash;

if(!empty($res->count() > 0)):?>
<div class="row">
	<div class="col-md-3 pulsantiera pull-right">
		<a href="<?php echo __('scarica_widget_url');?>" class="btn btn-primary pull-right carpooling" id="btn-embed" title="<?php echo __("Copia il widget e inseriscilo nel tuo sito");?>">
			<span class="hidden"><?php echo __("Copia il widget e inseriscilo nel tuo sito");?></span>
		</a>
	</div>
	<div class="col-md-9 tit_ric">
		<div class="col-md-12 form-group">
			<h4><?php
				if( sizeof($res) > 1 ) {
					echo $this->Paginator->counter(__('Trovati {0} passaggi per la tratta da {1} a {2}', array('{{count}}', $this->stripNazione($criteria['from']['fulladdress']), $this->stripNazione($criteria['to']['fulladdress']))));
				}
				else {
					echo $this->Paginator->counter(__('Trovato 1 passaggio per la tratta da {0} a {1}', array('{{count}}', $this->stripNazione($criteria['from']['fulladdress']), $this->stripNazione($criteria['to']['fulladdress']))));
				}
				if( !empty($criteria['outward']['mindate']) || !empty($criteria['outward']['maxdate']) ) echo '<br/><br/>';
				if( !empty($criteria['outward']['mindate']) ) echo __('a partire dal {0}', array(date('d-m-Y', strtotime($criteria['outward']['mindate'])))).' ';
				if( !empty($criteria['outward']['maxdate']) ) echo __('fino al {0}', array(date('d-m-Y', strtotime($criteria['outward']['maxdate']))));
			?></h4>
		</div>
		<div class="col-md-12 form-group">
			<?php if($layout == 'embed'):?>
				<?php echo $this->Html->link(__('Nuova ricerca'), array('controller' => 'journeys', 'action' => 'search', '?' => array('layout' => $layout)), array('class' => ''));?>
			<?php else:?>
				<?php echo $this->Html->link(__('Nuova ricerca'), array('controller' => 'journeys', 'action' => 'search', '?' => array('layout' => $layout)), array('class' => ''));?>
			<?php endif;?>
		</div>
	</div>
</div>

<?php else:?>

<div class="row">
	<div class="col-md-3 pulsantiera pull-right">
		<a href="<?php echo __('scarica_widget_url');?>" class="btn btn-primary pull-right carpooling" id="btn-embed" title="<?php echo __("Copia il widget e inseriscilo nel tuo sito");?>">
			<span class="hidden"><?php echo __("Copia il widget e inseriscilo nel tuo sito");?></span>
		</a>
	</div>
	<div class="col-md-9 tit_ric">
		<div class="col-md-12 form-group">
			<h4><?php
				echo __('Nessun risultato trovato per la tratta da {0} a {1}', array($this->stripNazione($criteria['from']['fulladdress']), $this->stripNazione($criteria['to']['fulladdress'])));
				if( !empty($criteria['outward']['mindate']) || !empty($criteria['outward']['maxdate']) ) echo '<br/><br/>';
				if( !empty($criteria['outward']['mindate']) ) echo __('a partire dal {0}', array(date('d-m-Y', strtotime($criteria['outward']['mindate'])))).' ';
				if( !empty($criteria['outward']['maxdate']) ) echo __('fino al {0}', array(date('d-m-Y', strtotime($criteria['outward']['maxdate']))));
			?></h4>
		</div>
		<div class="col-md-12 form-group">
			<?php if($layout == 'embed'):?>
				<?php echo $this->Html->link(__('Nuova ricerca'), array('controller' => 'journeys', 'action' => 'search', '?' => array('layout' => $layout)), array('class' => ''));?>
			<?php else:?>
				<?php echo $this->Html->link(__('Nuova ricerca'), array('controller' => 'journeys', 'action' => 'search', '?' => array('layout' => $layout)), array('class' => ''));?>
			<?php endif;?>
		</div>
	</div>
</div>

<?php endif;?>

<div id="search-filters">
	<?php
		// anche se ho già a disposizione gli orari devo necessariamente eseguire di nuovo la ricerca
		// sui provider se filtro perchè i provider non restituiscono i punti intermedi quindi non posso
		// sapere quali risultati vengono restituiti al variare del raggio
		echo $this->Form->create(null, array('id' => 'RicalcolaForm', 'action' => 'search?layout='.$layout));
	?>

	<?php
		echo $this->Form->input('from.latitude', array('type' => 'hidden', 'value' => $criteria['from']['latitude']));
		echo $this->Form->input('from.longitude', array('type' => 'hidden', 'value' => $criteria['from']['longitude']));
		echo $this->Form->input('from.fulladdress', array('type' => 'hidden', 'value' => $criteria['from']['fulladdress']));
		echo $this->Form->input('to.latitude', array('type' => 'hidden', 'value' => $criteria['to']['latitude']));
		echo $this->Form->input('to.longitude', array('type' => 'hidden', 'value' => $criteria['to']['longitude']));
		echo $this->Form->input('to.fulladdress', array('type' => 'hidden', 'value' => $criteria['to']['fulladdress']));
		echo $this->Form->input('outward.mindate', array('type' => 'hidden', 'value' => $criteria['outward']['mindate']));
		echo $this->Form->input('outward.maxdate', array('type' => 'hidden', 'value' => $criteria['outward']['maxdate']));
        echo $this->Form->input('seats.number', array('type' => 'hidden', 'value' => $criteria['seats']['number']));
	?>
	<?php
		$ricalcolo = $this->getRequest()->getSession()->read('ricalcolo');
		$clsCurrRadiusContainer = '';//empty($criteria['radius']) ? 'hide' : '';
	?>
	<label><?php echo __("Raggio (partenza e arrivo)");?> <span id="curr-radius-container" class="<?php echo $clsCurrRadiusContainer;?>">: <span id="curr-radius"></span> km</span></label>
	<div id="radius-slider"></div>
	<?php echo $this->Form->input('radius', array('type' => 'hidden', 'id' => 'radius', 'name' => 'radius', 'value' => $criteria['radius']));?>
	<br>
	<label><?php echo __("Fascia oraria");?>: <span id="curr-fascia-oraria"></span></label>
	<?php echo $this->Form->input('outward.mintime', array('type' => 'hidden', 'id' => 'outwardMintime' ,'value' => $criteria['outward']['mintime']));?>
	<?php echo $this->Form->input('outward.maxtime', array('type' => 'hidden', 'id' => 'outwardMaxtime', 'value' => $criteria['outward']['maxtime']));?>
	<div id="fascia-oraria-slider"></div>
	<br>
	<?php echo $this->Form->input('ricalcolo', array('type' => 'hidden', 'value' => 1));?>
	<?php echo $this->Form->submit(__('Ricalcola'), array('id' => 'submitRicalcolo', 'div' => false, 'class' => 'btn btn-primary pull-right'));?>
	<?php echo $this->Form->end();?>
</div>

<?php if(empty($res)):?>

<br/><br/>

<?php else:?>
<div class="row tit_ric">
	<div class="col-md-6 form-group">
		<p><?php echo $this->Paginator->counter(__('Pagina {0} di {1}, {2} risultati da {3} a {4}', ['{{page}}', '{{pages}}', '{{current}}', '{{start}}', '{{end}}']));?></p>
	</div>
	<div class="col-md-6 form-group ordinamento" style="text-align:right">
		<span><?php echo __('Ordina per');?>&nbsp;&nbsp;&nbsp;</span>
			<div class="btn-group" role="group" aria-label="...">
				<?php
					if(empty($this->request->getParam('named.sort'))) {
						$departureCls = 'btn-primary';
						$returnCls = 'btn-default';
						$priceCls = 'btn-default';
					}
					else {
						$departureCls = 'btn-default';
						$returnCls = 'btn-default';
						$priceCls = 'btn-default';
						switch($this->request->getParam('named.sort')) {
							case 'departure':
								$departureCls = 'btn-primary';
								break;
							case 'return_maxdate':
								$returnCls = 'btn-primary';
								break;
							case 'cost_fixed':
								$priceCls = 'btn-primary';
								break;
						}
					}
				?>
				<div class="btn-group">
					<button type="button" class="btn <?php echo $departureCls;?> dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<?php echo __('Data partenza');?> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
                        <li><?php echo $this->Html->link('<span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span> '.__('crescente'), array('action' => 'search_result', '?' => array('layout' => $layout, 'sort' => 'departure', 'direction' => 'asc')), array('escape' => false));?></li>
                        <li><?php echo $this->Html->link('<span class="glyphicon glyphicon-sort-by-attributes-alt" aria-hidden="true"></span> '.__('decrescente'), array('action' => 'search_result', '?' => array('layout' => $layout, 'sort' => 'departure', 'direction' => 'desc')), array('escape' => false));?></li>
					</ul>
				</div>
				<!--div class="btn-group">
					<button type="button" class="btn <?php echo $returnCls;?> dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<?php echo __('Data ritorno');?> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><?php echo $this->Html->link('<span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span> '.__('crescente'), array('action' => 'search_result', 'sort:return_maxdate', 'direction:asc', '?' => array('layout' => $layout)), array('escape' => false));?></li>
						<li><?php echo $this->Html->link('<span class="glyphicon glyphicon-sort-by-attributes-alt" aria-hidden="true"></span> '.__('decrescente'), array('action' => 'search_result', 'sort:return_maxdate', 'direction:desc', '?' => array('layout' => $layout)), array('escape' => false));?></li>
					</ul>
				</div>-->
				<div class="btn-group">
					<button type="button" class="btn <?php echo $priceCls;?> dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<?php echo __('Prezzo');?> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu pull-right" role="menu">
                        <li><?php echo $this->Html->link('<span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span> '.__('crescente'), array('action' => 'search_result', '?' => array('layout' => $layout, 'sort' => 'cost_fixed', 'direction' => 'asc')), array('escape' => false));?></li>
                        <li><?php echo $this->Html->link('<span class="glyphicon glyphicon-sort-by-attributes-alt" aria-hidden="true"></span> '.__('decrescente'), array('action' => 'search_result', '?' => array('layout' => $layout, 'sort' => 'cost_fixed', 'direction' => 'desc')), array('escape' => false));?></li>
					</ul>
				</div>
			</div>
	</div>
</div>

<div id="journeys-container" class="">

	<?php
		$days = array(
			'mon' => __('Lun'),
			'tue' => __('Mar'),
			'wed' => __('Mer'),
			'thu' => __('Gio'),
			'fri' => __('Ven'),
			'sat' => __('Sab'),
			'sun' => __('Dom'));
	?>

	<?php $index=-1;?>
	<?php foreach($res as $j):?>
		<?php $index++;?>
		<?php
			$provider = $j->provider;

		?>

		<div class="row viaggio <?php echo ($index%2 == 1) ? 'viaggio2' : '';?>">

			<div class="col-xs-12 col-md-12 col-lg-1 provider">
				<?php
					if(empty($provider['url_icona'])) {
						echo !empty($j['url']) ? $this->Html->link($provider['name'], $j['url'], array('target' => '_blank')) : $provider['name'];
					}
					else {
						echo !empty($provider['homepage']) ? $this->Html->link( $this->Html->image($provider['url_icona'], array('class' => 'provider-icon')), $provider['homepage'], array('target' => $targetDettagli, 'escape' => false)) : $this->Html->image($provider['url_icona'], array('class' => 'provider-icon'));
					}
				?>
			</div>

			<div class="col-xs-6 col-md-6 col-lg-1">
				<?php
                    $dataPartenza = '';
                    if(!empty($j['outward_mindate'])){
                        $minDate = Time::parse($j['outward_mindate']);
                        $dataPartenza = $minDate->i18nFormat('dd/MM/yyyy ');
                    }

					//$dataPartenza = (!empty($j['outward_mindate']) && strtotime($j['outward_mindate']) > 0) ? date('d/m/Y', strtotime($j['outward_mindate'])) : '';

					$oraPartenza = '';
					$numDays = 0;
					foreach(array_keys($days) as $d) {
						if($j[$d] == 1) $numDays++;
					}
					if($numDays == 1) { // altrimenti è ricorrente, non faccio vedere l'ora perchè si riferisce a più giorni
						foreach(array_keys($days) as $d) {
							if($j[$d] == 1) {
								$oraPartenza = date('H:i', strtotime($j['outward_'.$d.'_mintime']));
								//if(!empty($j['outward_'.$d.'_maxtime'])) $oraPartenza .= '-'.date('H:i', strtotime($j['outward_'.$d.'_maxtime']));
							}
						}
					}

				?>
				<p class="data_viaggio"><?php echo $dataPartenza;?></p> <p class="ora_viaggio"><?php echo $oraPartenza;?></p>
			</div>

			<div class="col-xs-6 col-md-6 col-lg-1">
				<?php
					$j_prezzo = (float)$j['cost_fixed'] + (float)$j['cost_variable'];
				?>
				<?php if($j_prezzo > 0):?>
				<span class="prezzo pull-right">
					<?php echo number_format($j_prezzo, 2, ',', '.') . ' &euro;';?>
				</span>
				<?php endif;?>
			</div>

			<div class="col-xs-12 col-md-12 col-lg-3">
				<p class="pap_viaggio"><span class="pap"><?php echo __("Partenza");?></span> <?php echo $j['from_address'].' '.$j['from_city'];?></p>
			</div>

			<div class="col-xs-12 col-md-12 col-lg-3">
				<p class="pap_viaggio"><span class="pap"><?php echo __("Arrivo");?></span> <?php echo $j['to_address'].' '.$j['to_city'];?></p>
			</div>

			<div class="col-xs-12 col-md-12 col-lg-2">
				<p class="pap_viaggio"><span class="pap"><?php echo __("Posti richiesti");?></span> <?php echo $j['driver_seats'];?></p>
			</div>

			<div class="col-xs-12 col-md-12 col-lg-1 dettagli">
				<p class="scheda_viaggio">
					<?php echo !empty($j['url']) ? $this->Html->link(__('Dettagli').' <i class="icon-chevron-right"></i>', $j['url'], array('class' => 'pull-right scheda', 'target' => $targetDettagli, 'escape' => FALSE)) : '';?>
				</p>
			</div>


		</div>


			<!--<div class="col-xs-12 col-md-2 ricorrente">
				<?php
					if($j['frequency'] == 'regular') {
						$cls = 'si';
						$txt = 's&igrave;';
						$img = 'ICO_ok_mini.png';
					}
					else {
						$cls = 'no';
						$txt = 'no';
						$img = 'ICO_no_mini.png';
					}
				?>
				<p class="pap_viaggio"><span class="pap"><?php echo __("Ricorrente");?></span> <span class="<?php echo $cls;?>"><?php echo $txt;?></span></p>
			</div>-->
			<!--<div class="col-xs-12 col-md-1 ritorno">
				<?php
					if( !empty($j['return_mindate']) || !empty($j['return_maxdate']) ) {
						$cls = 'si';
						$txt = 's&igrave;';
						$img = 'ICO_ok_mini.png';
					}
					else {
						$cls = 'no';
						$txt = 'no';
						$img = 'ICO_no_mini.png';
					}
				?>
				<p class="pap_viaggio"><span class="pap"><?php echo __("Ritorno");?></span> <span class="<?php echo $cls;?>"><?php echo $txt;?></span></p>
			</div>-->


	<?php endforeach;?>

</div>

<div style="text-align:center">
	<div class="paging">
		<ul class="pagination pager">
		<?php echo $this->Paginator->first('««', array('tag' => 'li'), null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->prev('«', array('tag' => 'li'), null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(array('modulus' => 4, 'tag' => 'li', 'separator' => false, 'currentClass' => 'active'));?>
		<?php echo $this->Paginator->next('»', array('tag' => 'li'), null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->last('»»', array('tag' => 'li'), null, array('class' => 'disabled')); ?>
		</ul>
	</div>
</div>

<?php endif;?>

<?php $this->Html->scriptStart(array('block' => true)); ?>

$(function() {

	var startRadius = <?php echo empty($criteria['radius']) ? $default_search_radius : $criteria['radius'];?>;
	var startMinTime = <?php echo $criteria['outward']['mintime'];?>;
	var startMaxTime = <?php echo $criteria['outward']['maxtime'];?>;
	var maxSearchRadius = <?php echo $max_search_radius;?>;

	$('#RicalcolaForm').submit(function(e){
		if( $('#curr-radius-container').hasClass('hide') ) { // nessun raggio specificato, rimuovo il default value
			$('#radius').val('');
		}
	});

	var radiusSteps = getSliderPseudoLogSteps(5, maxSearchRadius);
	$( "#radius-slider" ).slider({
		orientation: "horizontal",
		range: "min",
		min: 0,
		max: radiusSteps.length-1,
		value: logPositionFromValue(radiusSteps, startRadius),
		change: refreshSearch,
		slide: refreshSearch
	});

	$( "#fascia-oraria-slider" ).slider({
		range: true,
		min: 0,
		max: 24,
		values: [ startMinTime, startMaxTime ],
		slide: function( event, ui ) {
			$( "#curr-fascia-oraria" ).text( ui.values[ 0 ] + "h - " + ui.values[ 1 ] + "h" );
			$('#outwardMintime').val( ui.values[ 0 ] );
			$('#outwardMaxtime').val( ui.values[ 1 ] );
		}
	});

	$(document).ready(function(){
		$('#curr-radius').text( startRadius );
		$( "#curr-fascia-oraria" ).text( $( "#fascia-oraria-slider" ).slider( "values", 0 ) + 'h' + " - " + $( "#fascia-oraria-slider" ).slider( "values", 1 ) + "h" );
	});


	function refreshSearch(event, ui) {
		$('#curr-radius-container').removeClass('hide');
		var radiusPosition = ui.value;
		var radiusValue = steps[radiusPosition];
		$('#curr-radius').text( radiusValue );
		$('#radius').val( radiusValue );
	}

	/**
	 * genera un array contenente gli step dello slider per ottenere uno slider pseudo-logaritmico
	 * (utilizzare un agg. logaritmico "puro" non funziona perchè il valore relativo ad ogni step non sarebbe
	 * regolare) definendo più range:
	 *
	 * (1,10]: step 1
	 * (11,50] : step 5
	 * (51,100]: step 10
	 * (101,inf): step 50
	 *
	 * Es. se lo slider va da 5 a 150 gli step saranno: 5,6,7,8,9,10,15,20,25,30,35,40,45,50,60,70,80,90,100,150
	 *
	 */
	function getSliderPseudoLogSteps(minVal, maxVal) {
		steps = new Array();
		// Range (1,10]
		for(i=minVal;i<=min(10,maxVal);i++) {
			steps.push(i);
		}
		// Range (11,50]
		for(i=15;i<=min(50,maxVal);i=i+5) {
			steps.push(i);
		}
		// Range (51,100]
		for(i=60;i<=min(100,maxVal);i=i+10) {
			steps.push(i);
		}
		// Range (101,inf)
		for(i=150;i<=maxVal;i=i+50) {
			steps.push(i);
		}
		return steps;
	}

	/**
	 * ottiene lo step (o lo step più vicino) per il valore specificato
	 */
	function logPositionFromValue(aSteps, aValue) {
		if(aValue <= aSteps[0]) {
			return 0;
		}
		else if(aValue >= aSteps[aSteps.length-1]) {
			return aSteps.length-1;
		}
		else {
			for(i=0;i<aSteps.length-2;i++) {
				if(aSteps[i] <= aValue && aValue < aSteps[i+1]) {
					return i; // date le condizioni specificate devo restituire il floor
				}
			}
		}
		return -1; // never reached

	}

	/**
	 * used in getSliderPseudoLogSteps
	 */
	function min(a, b) {
		return a < b ? a : b;
	}
});
<?php $this->Html->scriptEnd();
