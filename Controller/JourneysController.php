<?php
/**
 * Rdexapi
 *
 * @copyright     Copyright (c) Impronta48 (http://impront48.it)
 * @link          http://impronta48.it
 * @package       app.Controller
 * @since         
 * @license       
 */

App::uses('AppController', 'Controller');
App::uses('HttpSocket', 'Network/Http');

/**
 * RDEX API
 * 
 * Provides an implementation of a REST Api for the CarPooling Aggregator following RDEX/FEDUCO standard
 *
 * 
 * @package       app.Controller
 * @link http://
 */
 
class JourneysController extends AppController {

/**
 * 
 *
 * @var array
 */
	public $uses = array('Provider', 'SearchResult', 'WidgetTracking', 'SearchRequest');

/**
 * 
 *
 * @var array
 */
	public $components = array('Paginator', 'RequestHandler');
	
/**
 * 
 * 
 */
	public function beforeFilter() {
		
		parent::beforeFilter();
    }	
    
    // temporanea, da rimuovere
    public function stats() {}
    
/**
 * 
 */
	public function embed() {
	}	


/**
 * versione web service: l'aggregatore esegue le ricerche e restituisce i risultati a mo' di provider
 * contattato dal router che connetterà le tratte di car sharing trovate con le altre tratte di altri tipi
 * di servizio (bus, ecc...)
 * L'interfaccia è la medesima del provider RDEX 
 * TODO: per esigenze di test non ci sono signature e gestione expiry (da copiare dal provider)
 */
	public function search_ws() {
		
		$sess_id = $this->Session->read('sess_id');
		if(empty($sess_id)) {
			$sess_id = uniqid("", true);
			$this->Session->write('sess_id', $sess_id);
		}
		
		//$this->RequestHandler->setContent('json', 'application/json');
		
		$q = isset($this->request->query['p']) ? $this->request->query['p'] : array();
		
		// validate query parameters
		if( !isset($q['from']) || !isset($q['from']['latitude']) || !isset($q['from']['longitude']) ) {
			$this->_throwBadRequestException('From: missing latitude and/or longitude');
		}
		if( !isset($q['to']) || !isset($q['to']['latitude']) || !isset($q['to']['longitude']) ) {
			$this->_throwBadRequestException('To: missing latitude and/or longitude');
		}
		
		if( isset($q['outward']['mindate']) ) { 
			if( strtotime($q['outward']['mindate']) == -1 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $q['outward']['mindate'])) {
				// Invalid (date must be in Y-m-d format). Set default
				$this->_throwBadRequestException('Invalid outward mindate (required format: Y-m-d)');
			}
		}
		
		if( isset($q['outward']['maxdate']) ) { 
			if( strtotime($q['outward']['maxdate']) == -1 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $q['outward']['maxdate'])) {
				// Invalid (date must be in Y-m-d format). Set default
				$this->_throwBadRequestException('Invalid outward maxdate (required format: Y-m-d)');
 			}
		}
		
		if( isset($q['outward']['mindate']) && isset($q['outward']['maxdate']) ) { 
			if( strtotime($q['outward']['maxdate']) < strtotime($q['outward']['mindate']) ) {
				$this->_throwBadRequestException('Outward maxdate must be larger than or equal to Outward mindate');
			} 
		}

		// gestione radius (parametro fuori specifica ma necessario)
		if( isset($q['radius']) ) {
			if( !is_int($q['radius']) ) $q['radius'] = 10; // default value
		} 
		
		// gestisco il mintime (parametro fuori specifica ma necessario, come su blablacar è un intero tra 1 e 24)
		if( isset($q['outward']['mintime']) ) { 
			if( !is_int($q['outward']['mintime']) ) $q['outward']['mintime'] = 1;
		}
		
		// gestisco il maxtime (parametro fuori specifica ma necessario, come su blablacar è un intero tra 1 e 24)
		if( isset($q['outward']['maxtime']) ) { 
			if( !is_int($q['outward']['maxtime']) ) $q['outward']['maxtime'] = 24;
		}
		
		// 2016-10-27 - log della richiesta all'aggregatore
		$this->SearchRequest->save(array(
			'from_lat' => 			$q['from']['latitude'],
			'from_lon' => 			$q['from']['longitude'],
			'from_fulladdress' => 	isset($q['from']['fulladdress']) ? $q['from']['fulladdress'] : '',
			'to_lat' => 			$q['to']['latitude'],
			'to_lon' => 			$q['to']['longitude'],
			'to_fulladdress' => 	isset($q['to']['fulladdress']) ? $q['to']['fulladdress'] : '',
			'from_date' => 			empty($q['outward']['mindate']) ? '' : date('Y-m-d', strtotime($q['outward']['mindate'])),
			'to_date' => 			empty($q['outward']['maxdate']) ? '' : date('Y-m-d', strtotime($q['outward']['maxdate'])),
			'ip' => 				$this->request->clientIp(),
			'user_agent' =>			$this->request->header('User-Agent'),
			'type' => 				'rest',
			'session_id' =>			$sess_id
		));
		
		// Important: clear any previous search result in session
		$this->SearchResult->clearExpired();
		$this->SearchResult->clearSession( $sess_id/*$this->Session->id()*/ );
		
		set_time_limit(120); // TODO: potrebbe essere necessario un timeout maggiore se i provider sono molti dato che li contatto sequenzialmente
		
		$providers = $this->Provider->find('all', array('fields' => array('id', 'name'), 'order' => array('id DESC')));
		foreach($providers as $provider) {
			try {
				$num_pages = $this->search_provider($provider['Provider']['id'], $q);
				if($num_pages > 1) { // risultato paginato (blablacar)
					for($j=2;$j<=$num_pages;$j++) {
						$q['page'] = $j;
						$this->search_provider($provider['Provider']['id'], $q);
					}
				}
			} 
			catch (Exception $e) {
				// devo intercettare eventuali eccezioni generate da search_provider ma non faccio nulla 
				// (semplicemente la chiamata restituirà risultato vuoto per quel provider)
			}
		}
		
		// fetch results from db
		$result = $this->SearchResult->find('all', array(
			'conditions' => array('SearchResult.session_id' => $sess_id/*$this->Session->id()*/),
			'order' => array(
				'SearchResult.outward_mindate', 
				'SearchResult.outward_maxdate', 
				'SearchResult.return_mindate', 
				'SearchResult.return_maxdate', 
				'SearchResult.cost_fixed', 
				'SearchResult.cost_variable'
			),
			'recursive' => -1
		));
		
		// convert result to rdex standard (NOTA: diverso dal codice del provider perchè cambia la tabella)
		$res = array();
		foreach($result as $j) {
		
			$j = $j['SearchResult'];
			
			unset($j["session_id"]);
			unset($j["provider_id"]);
			unset($j["created"]);
			
			$j['from']['address'] = $j['from_address'];
			unset($j['from_address']);
			$j['from']['city'] = $j['from_city'];
			unset($j['from_city']);
			$j['from']['latitude'] = $j['from_latitude'];
			unset($j['from_latitude']);
			$j['from']['longitude'] = $j['from_longitude'];
			unset($j['from_longitude']);
			$j['from']['country'] = '';
			$j['from']['postalcode'] = '';
			
			$j['to']['address'] = $j['to_address'];
			unset($j['to_address']);
			$j['to']['city'] = $j['to_city'];
			unset($j['to_city']);
			$j['to']['latitude'] = $j['to_latitude'];
			unset($j['to_latitude']);
			$j['to']['longitude'] = $j['to_longitude'];
			unset($j['to_longitude']);
			$j['to']['country'] = '';
			$j['to']['postalcode'] = '';
			
			$j['number_of_waypoints'] = 2; // waypoints non gestibili (blablacar ...) metto 2 (partenza e destinazione)
			
			// driver
			$j['driver']['uuid'] = $j['driver_id'];
			unset($j['driver_id']);
			$j['driver']['alias'] = $j['driver_alias'];
			unset($j['driver_alias']);
			$j['driver']['image'] = $j['driver_image'];
			unset($j['driver_image']);	
			$j['driver']['seats'] = $j['driver_seats'];
			unset($j['driver_seats']);
			$j['driver']['state'] = $j['driver_state'];
			unset($j['driver_state']);		
			// waypoints
			$j['waypoints'] = array();
			// waypoints non gestiti da blablacar
			/*for($i=1;$i<sizeof($journey['Waypoint'])-1;$i++) {
				unset($journey['Waypoint']['id']);
				unset($journey['Waypoint']['journey_id']);
				$j['waypoints'][] = $journey['Waypoint'][$i];
			}*/
			// cost
			$j['cost']['fixed'] = $j['cost_fixed'];
			unset($j['cost_fixed']);
			$j['cost']['variable'] = $j['cost_variable'];
			unset($j['cost_variable']);
			// vehicle
			$j['vehicle']['image'] = $j['vehicle_image'];
			unset($j['vehicle_image']);
			$j['vehicle']['color'] = $j['vehicle_color'];
			unset($j['vehicle_color']);
			$j['vehicle']['model'] = $j['vehicle_model'];
			unset($j['vehicle_model']);
			// days
			$days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
			$long_days = array('mon' => 'monday', 'tue' => 'tuesday', 'wed' => 'wednesday', 'thu' => 'thursday', 'fri' => 'friday', 'sat' => 'saturday', 'sun' => 'sunday');
			foreach($days as $d) {
				$j['days'][$long_days[$d]] = $j[$d];
				if(empty($j['days'][$long_days[$d]])) $j['days'][$long_days[$d]] = 0;
				unset($j[$d]);
			}
			// others
			$l1 = array('outward', 'return');
			$l2 = array('mindate', 'maxdate');
			$l3 = array('mintime', 'maxtime');
			foreach($l1 as $k1) {
				foreach($l2 as $k2) {
					$j[$k1][$k2] = $j[$k1.'_'.$k2];
					unset($j[$k1.'_'.$k2]);
				}
				foreach($days as $d) {
					foreach($l3 as $k3) {
						$j[$k1][$long_days[$d]][$k3] = $j[$k1.'_'.$d.'_'.$k3];
						unset($j[$k1.'_'.$d.'_'.$k3]); 
					}
				}
			}
			
			$res[] = $j; 
		}
			
		$this->set('res', $res);
        $this->set('_serialize', 'res');
		
	}

	
/**
 * 
 * NOTA: IN CASO DI PROBLEMI NELLA SCRITTURA DEL SESSION_ID SUI RECORD DELLA RICERCA (null come se la sessione
 * non fosse disponibile) VERIFICARE LOG4PHP: se per qualche motivo non riesce a scrivere potrebbe invalidare la sessione!
 * 
 */
	public function search() {
		
		$sess_id = $this->Session->read('sess_id');
		if(empty($sess_id)) {
			$sess_id = uniqid("", true);
			$this->Session->write('sess_id', $sess_id);
		}
		
		// tracciatura di chi scarica ( = dove viene usato) il widget
		if( $this->layout == 'embed' ) {
			$url = $this->referer();
			// rimuovi eventuale query (per accorciare l'url)
			if( $pos=strpos($url, '?') !== FALSE ) {
				$url = substr($url, 0, $pos);
			}
			
			$trackingRecord = $this->WidgetTracking->find('first', array('conditions' => array(
				'url' => $url
			)));
			if( empty($trackingRecord) ) {
				$trackingRecord = array(
					'WidgetTracking' => array(
						'url' => $url,
						'hits_num' => 1
					)
				);
			}
			else {
				$trackingRecord['WidgetTracking']['hits_num'] += 1;
			}
			
			$this->WidgetTracking->save($trackingRecord);
		}
		// END of tracciatura widget
		
		// Important: clear expired results
		$this->SearchResult->clearExpired();
		$this->Session->write('ricalcolo', FALSE); // important!	
	
		if( !empty($this->request->data) ) { // oppure se si usa get se non è vuota la query
			
			$this->Session->write('search_criteria', $this->request->data); // usato per filtrare successivamente il risultato
			
			// 2016-10-27 - log della richiesta all'aggregatore
			$d = $this->request->data; // shorten ...
			$this->SearchRequest->save(array(
				'from_lat' => 			$d['from']['latitude'],
				'from_lon' => 			$d['from']['longitude'],
				'from_fulladdress' => 	$d['from']['fulladdress'],
				'to_lat' => 			$d['to']['latitude'],
				'to_lon' => 			$d['to']['longitude'],
				'to_fulladdress' => 	$d['to']['fulladdress'],
				'from_date' => 			empty($d['outward']['mindate']) ? '' : date('Y-m-d', strtotime($d['outward']['mindate'])),
				'to_date' => 			empty($d['outward']['maxdate']) ? '' : date('Y-m-d', strtotime($d['outward']['maxdate'])),
				'ip' => 				$this->request->clientIp(),
				'user_agent' =>			$this->request->header('User-Agent'),
				'type' => 				'web',
				'session_id' => 		$sess_id
			));
			
			// 2015-09-09 - ottimizzazione ricerca (x tutti i fornitori):
			// se sto ricalcolando/filtrando un precedente risultato NON faccio un'altra richiesta sui provider
			// ma filtro localmente i risultati ottenuti con la prima ricerca (ipotizzando che nella prima ricerca
			// dove non uso il raggio il numero di risultati sia sufficientemente ampio)
			// NOTA: per ripristinare la ricerca sui fornitori anche in caso di filtraggio/ricalcolo è sufficiente rimuovere
			// il seguente blocco if/else
			if( isset($this->request->data['Journey']['ricalcolo']) ) {
				$this->Session->write('ricalcolo', TRUE);
				$this->redirect( array('action' => 'search_result') );
			}
			else {
				$this->Session->write('ricalcolo', FALSE);
			}
			
			// Important: clear any previous search result in session (new search)
			$this->SearchResult->clearSession( $sess_id );
			
			// verifica che le date siano valide
			if( !empty($this->request->data['outward']['mindate']) && strtotime($this->request->data['outward']['mindate']) === FALSE ) {
				$this->Session->setFlash(__('Data di partenza (min) non valida'), 'flash_error');
			}
			elseif ( !empty($this->request->data['outward']['maxdate']) && strtotime($this->request->data['outward']['maxdate']) === FALSE ) {
				$this->Session->setFlash(__('Data di partenza (max) non valida'), 'flash_error');
			}
			elseif( 
				!empty($this->request->data['outward']['mindate']) && 
				!empty($this->request->data['outward']['maxdate']) && 
				strtotime($this->request->data['outward']['maxdate']) < strtotime($this->request->data['outward']['mindate'])) {
				$this->Session->setFlash(__('La data di partenza massima deve essere maggiore o uguale alla data di partenza minima'), 'flash_error');
			}
			else {
				// get query string
				$q = $this->_searchQueryArrayToString( $this->request->data );
				
				// get providers (ids and names - not as list)
				$providers = $this->Provider->find('all', array('fields' => array('id', 'name'), 'order' => array('id DESC')));
				
				$this->set('q', $q);
				$this->set('providers', json_encode($providers));
			}
		}
		
		// se sto eseguendo una nuova ricerca, ri-popola il form con i valori precedenti
		$search_criteria = $this->Session->read('search_criteria');
		if($search_criteria) {
			$this->request->data = $search_criteria; 
		}
		
		$this->set('layout', $this->layout);
		
	}
  

/**
 * 	called via ajax to search for trips on the given search provider.
 * 
 * 20150508: chiamata anche via ws per la ricerca sul provider (aggiunto il secondo parametro per tale motivo)
 */
	public function search_provider($id, $q_ws=null) {
		
		// 2016-12-02: se la richiesta è solo per le interrogazioni di tipo statistico non memorizzo i risultati a db
		$saveProviderResult = true;
		if( isset($this->request->query['is_stat']) ) $saveProviderResult = false;
		
		$provider = $this->Provider->findById($id);
		if(empty($provider)) {
			 throw new NotFoundException(__('Provider not found'));
		}
		
		if( !isset($this->request->query['p']) ) {
			throw new BadRequestException(__('Missing query'));
		}
		
		// translate the query parameters according to the provider's api
		$func = '_get'.ucfirst(strtolower($provider['Provider']['api'])).'RequestQueryStr';
		if(!method_exists($this, $func)) {
			throw new InternalErrorException('Unavailable method for api type');
		}
		
		$q = $this->$func( isset($q_ws) ? $q_ws : $this->request->query['p'] );

		$num_pages = 1; // init
		$journeysNum = 0;

		// update query string by adding authentication params (string grants) according to the provider
		$func = '_add'.ucfirst(strtolower($provider['Provider']['api'])).'RequestGrantStr';
		if(!method_exists($this, $func)) {
			throw new InternalErrorException('Unavailable method for api type');
		}
		$res = $this->$func($q, $provider);
		
		if($res['success']) { // successfully added grants to the request. Proceed with the search
			
			$q = $res['q'];
			
			// search
			$socket = new HttpSocket(array('timeout' => Configure::read('provider.socket_timeout')));
			
			if( Configure::read('proxy.enabled')  ) { // VARCH  21/10/2014 - a proxy can be configured
				$proxy = Configure::read('proxy.params');
				$proxy['port'] = empty($proxy['port']) ? 3218 : $proxy['port'];
				$socket->configProxy($proxy['host'], $proxy['port']);
			}
			
			// gestione paginazione (blablacar)
			$extra_q = '';
			if( isset($this->request->query['page']) ) {
				$extra_q = '&page='.$this->request->query['page'];
			}
			
			$res = $socket->get($provider['Provider']['url'], $q.$extra_q);
			$journeysNum = $this->_handleSearchRequest($provider, $res, $saveProviderResult);
			
			// verifica se il risultato è paginato (blablacar)
			if( $res->code == 200 ) {
				$res = json_decode($res->body);
				if( isset($res->pager) ) {
					$num_pages = $res->pager->pages;
				}
				else {
					$num_pages = 1;
				}
			}
			else {
				$num_pages = 1;
			}
		}
		else {
			$num_pages = 1;
			//CakeLog::write('error', '[Journeys:search_provider] Ricerca sul provider '.$id.' annullata. Impossibile aggiungere alla richiesta le credenziali di accesso');
		}
		
		if( isset($q_ws) ) {
			return $num_pages;
		}
		else {
			$this->set('res', array('provider' => $id, 'num_pages' => $num_pages, 'num_results' => $journeysNum));
			$this->set('_serialize', 'res');
		}
	}
	
	
	function _handleSearchRequest($provider, $res, $saveProviderResult) {
		$journeysNum = 0;
		if( $res->code == 200 ) {
			$res = json_decode($res->body);
			
			// translate search result according to the provider's api
			$func = '_translate'.ucfirst(strtolower($provider['Provider']['api'])).'SearchResult';
			if(!method_exists($this, $func)) {
				throw new InternalErrorException('Unavailable method for api type');
			}
			$rdexFoundJourneys = $this->$func($res);
			// convert rdex journeys to a one-dim array to be saved on db as search result
			$searchResults = array();
			foreach($rdexFoundJourneys as $j) $searchResults[] = $this->_rdexJourneyToSearchResult($j, $provider['Provider']['id']);
			if(!empty($searchResults)) {
				if($saveProviderResult) { // altrimenti è una richiesta fatta esclusivamente per le statistiche (non mi interessa salvarla)
					$this->SearchResult->saveAll($searchResults);
				}
				$journeysNum = sizeof($searchResults);
			}
		}
		else {
			//CakeLog::write('warning', '[Journeys:search_provider] Ricerca sul provider '.$provider['Provider']['id'].' fallita '.$res->code.' '.$res->reasonPhrase. ' '.serialize($res->headers));
		}
		return $journeysNum;
	}
	
	function _rdexJourneyToSearchResult($j, $provider_id) {
		
		$sess_id = $this->Session->read('sess_id');
		
		$r = array();
		$r['operator'] = $j['operator'];
		$r['logo_supplier'] = $j['logo_supplier'];
		$r['origin'] = $j['origin'];
		$r['url'] = $j['url'];
		$r['driver_id'] = $j['driver']['uuid'];
		$r['driver_alias'] = $j['driver']['alias'];
		$r['driver_image'] = $j['driver']['image'];
		$r['driver_seats'] = $j['driver']['seats'];
		$r['route'] = $j['route'];
		$r['cost_fixed'] = $j['cost']['fixed'];
		$r['cost_variable'] = $j['cost']['variable'];
		$r['details'] = $j['details'];
		$r['vehicle_image'] = $j['vehicle']['image'];
		$r['vehicle_model'] = $j['vehicle']['model'];
		$r['vehicle_color'] = $j['vehicle']['color'];
		$r['frequency'] = $j['frequency'];
		$r['type'] = $j['type'];
		$r['real_time'] = $j['real_time'];
		$r['stopped'] = $j['stopped'];
		$r['stopped'] = $j['stopped'];
		$r['mon'] = $j['days']['monday'];
		$r['tue'] = $j['days']['tuesday'];
		$r['wed'] = $j['days']['wednesday'];
		$r['thu'] = $j['days']['thursday'];
		$r['fri'] = $j['days']['friday'];
		$r['sat'] = $j['days']['saturday'];
		$r['sun'] = $j['days']['sunday'];
		$r['outward_mindate'] = $j['outward']['mindate'];
		$r['outward_maxdate'] = $j['outward']['maxdate'];
		$r['outward_mon_mintime'] = $j['outward']['monday']['mintime'];
		$r['outward_mon_maxtime'] = $j['outward']['monday']['maxtime'];
		$r['outward_tue_mintime'] = $j['outward']['tuesday']['mintime'];
		$r['outward_tue_maxtime'] = $j['outward']['tuesday']['maxtime'];
		$r['outward_wed_mintime'] = $j['outward']['wednesday']['mintime'];
		$r['outward_wed_maxtime'] = $j['outward']['wednesday']['maxtime'];
		$r['outward_thu_mintime'] = $j['outward']['thursday']['mintime'];
		$r['outward_thu_maxtime'] = $j['outward']['thursday']['maxtime'];
		$r['outward_fri_mintime'] = $j['outward']['friday']['mintime'];
		$r['outward_fri_maxtime'] = $j['outward']['friday']['maxtime'];
		$r['outward_sat_mintime'] = $j['outward']['saturday']['mintime'];
		$r['outward_sat_maxtime'] = $j['outward']['saturday']['maxtime'];
		$r['outward_sun_mintime'] = $j['outward']['sunday']['mintime'];
		$r['outward_sun_maxtime'] = $j['outward']['sunday']['maxtime'];
		$r['return_mindate'] = $j['return']['mindate'];
		$r['return_maxdate'] = $j['return']['maxdate'];
		$r['return_mon_mintime'] = $j['return']['monday']['mintime'];
		$r['return_mon_maxtime'] = $j['return']['monday']['maxtime'];
		$r['return_tue_mintime'] = $j['return']['tuesday']['mintime'];
		$r['return_tue_maxtime'] = $j['return']['tuesday']['maxtime'];
		$r['return_wed_mintime'] = $j['return']['wednesday']['mintime'];
		$r['return_wed_maxtime'] = $j['return']['wednesday']['maxtime'];
		$r['return_thu_mintime'] = $j['return']['thursday']['mintime'];
		$r['return_thu_maxtime'] = $j['return']['thursday']['maxtime'];
		$r['return_fri_mintime'] = $j['return']['friday']['mintime'];
		$r['return_fri_maxtime'] = $j['return']['friday']['maxtime'];
		$r['return_sat_mintime'] = $j['return']['saturday']['mintime'];
		$r['return_sat_maxtime'] = $j['return']['saturday']['maxtime'];
		$r['return_sun_mintime'] = $j['return']['sunday']['mintime'];
		$r['return_sun_maxtime'] = $j['return']['sunday']['maxtime'];
		$r['from_city'] = $j['from']['city'];
		$r['from_address'] = $j['from']['address'].' '.$j['from']['postalcode'];
		$r['from_latitude'] = $j['from']['latitude'];
		$r['from_longitude'] = $j['from']['longitude'];
		$r['to_city'] = $j['to']['city'];
		$r['to_address'] = $j['to']['address'].' '.$j['to']['postalcode'];
		$r['to_latitude'] = $j['to']['latitude'];
		$r['to_longitude'] = $j['to']['longitude'];

		$r['session_id'] = $sess_id;
		$r['provider_id'] = $provider_id;
		
		return $r;
	}
	
/**
 * 
 */
	public function search_result() {
		
		$sess_id = $this->Session->read('sess_id');
		
		$search_criteria = $this->Session->read('search_criteria');
		
		if(empty($search_criteria)) {
			// qualcuno ha provato a visitare direttamente questa pagina, redirezionalo alla ricerca
			$this->redirect( array('action' => 'search') );
		}
		
		// fill non mandatory criteria (if missing)
		if( !isset($search_criteria['radius']) ) {
			$search_criteria['radius'] = ''; // se non è specificato non filtro per radius
		}
		if( !isset($search_criteria['outward']['mintime']) || empty($search_criteria['outward']['mintime']) ) {
			$search_criteria['outward']['mintime'] = 0;
		}
		if( !isset($search_criteria['outward']['maxtime']) || empty($search_criteria['outward']['maxtime']) ) {
			$search_criteria['outward']['maxtime'] = 24;
		}
		
		$this->SearchResult->virtualFields = array();
		// campo virtuale per tirare su solo i viaggi successivi al tempo corrente
		$this->SearchResult->virtualFields['departure'] = "UNIX_TIMESTAMP(outward_mindate) + TIME_TO_SEC(COALESCE(outward_mon_mintime,outward_tue_mintime,outward_wed_mintime,outward_thu_mintime,outward_fri_mintime,outward_sat_mintime,outward_sun_mintime,'00:00:00'))";
		
		// blocco per ottimizzazione ricalcolo
		$ricalcolo = $this->Session->read('ricalcolo');
		if($ricalcolo) {
			$conditions = array();
			$conditions['SearchResult.session_id'] = $sess_id;
			// radius
			if( !empty($search_criteria['radius']) ) {
				// setup necessary virtual fields on the fly (because they depend on the coordinates in search criteria)
				$lat_from = $search_criteria['from']['latitude'];
				$lng_from = $search_criteria['from']['longitude'];
				$lat_to = $search_criteria['to']['latitude'];
				$lng_to = $search_criteria['to']['longitude'];
				$this->SearchResult->virtualFields['distance_from'] = '(3959 * ACOS(COS(RADIANS('.$lat_from.'))
									* COS(RADIANS(SearchResult.from_latitude))
									* COS(RADIANS(SearchResult.from_longitude) 
									- RADIANS('.$lng_from.'))
									+ SIN(RADIANS('.$lat_from.'))
									* SIN(RADIANS(SearchResult.from_latitude))))';
				$this->SearchResult->virtualFields['distance_to'] = '(3959*ACOS(COS(RADIANS('.$lat_to.'))
									* COS(RADIANS(SearchResult.to_latitude))
									* COS(RADIANS(SearchResult.to_longitude) 
									- RADIANS('.$lng_to.'))
									+ SIN(RADIANS('.$lat_to.'))
									* SIN(RADIANS(SearchResult.to_latitude))))';
										
				$conditions['distance_from <'] = $search_criteria['radius']*0.621371192; // km to miles
				$conditions['distance_to <'] = $search_criteria['radius']*0.621371192; // km to miles
			}
			
			// mintime
			$mintime = (strlen($search_criteria['outward']['mintime']) == 1 ? '0' : '').$search_criteria['outward']['mintime'].':00:00';
			$conditions[] = array(
				'OR' => array(
					'outward_mon_mintime >=' => $mintime,
					'outward_tue_mintime >=' => $mintime,
					'outward_wed_mintime >=' => $mintime,
					'outward_thu_mintime >=' => $mintime,
					'outward_fri_mintime >=' => $mintime,
					'outward_sat_mintime >=' => $mintime,
					'outward_sun_mintime >=' => $mintime,
				)
			);
			// maxtime
			$maxtime = (strlen($search_criteria['outward']['maxtime']) == 1 ? '0' : '').$search_criteria['outward']['maxtime'].':00:00';
			$conditions[] = array(
				'OR' => array(
					'outward_mon_maxtime <=' => $maxtime,
					'outward_tue_maxtime <=' => $maxtime,
					'outward_wed_maxtime <=' => $maxtime,
					'outward_thu_maxtime <=' => $maxtime,
					'outward_fri_maxtime <=' => $maxtime,
					'outward_sat_maxtime <=' => $maxtime,
					'outward_sun_maxtime <=' => $maxtime,
				)
			);
			
		}
		else {
			$conditions = array('SearchResult.session_id' => $sess_id);
		}
		
		// in ogni caso tiro su solo i risultati successivi al tempo corrente
		$conditions['departure >='] = time();
	
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'limit' => 20,
			'order' => array(
				'SearchResult.departure', 
				'SearchResult.cost_fixed', 
				'SearchResult.cost_variable'
			)
		);
		
		// gestione 'target' del link dettagli
		if( $this->RequestHandler->isMobile() ) {
			$targetDettagli = '_self';
		}
		else {
			$targetDettagli = '_blank';
		}
		
		$data = $this->Paginator->paginate('SearchResult');
		$this->set('res', $data);
		$this->set('criteria', $search_criteria);
		$this->set('layout', $this->layout);
		$this->set('targetDettagli', $targetDettagli);
		$this->set('default_search_radius', Configure::read('default_search_radius'));
		$this->set('max_search_radius', Configure::read('max_search_radius'));
	}
	
/**
 * 
 */
	function _getRdexRequestQueryStr($qArr) {
		
		if( isset($qArr['outward']['mindate']) && !empty($qArr['outward']['mindate']) ) {
			// convert to std date format
			//$mindate = explode('/', $qArr['outward']['mindate']);
			//$qArr['outward']['mindate'] = $mindate[2].'-'.$mindate[1].'-'.$mindate[0];
			$qArr['outward']['mindate'] = date('Y-m-d', strtotime($qArr['outward']['mindate']));
		}
		if( isset($qArr['outward']['maxdate']) && !empty($qArr['outward']['maxdate']) ) {
			// convert to std date format
			//$maxdate = explode('/', $qArr['outward']['maxdate']);
			//$qArr['outward']['maxdate'] = $maxdate[2].'-'.$maxdate[1].'-'.$maxdate[0];
			$qArr['outward']['maxdate'] = date('Y-m-d', strtotime($qArr['outward']['maxdate']));
		}
		
		return $this->_searchQueryArrayToString($qArr);
	
	}
	
/**
 * 
 */
	function _getBlablacarRequestQueryStr($qArr) {
		
		$qStr = '';
		$qStr .= 'fc=' . $qArr['from']['latitude'] . '|' . $qArr['from']['longitude'] . '&';
		if( isset($qArr['to']) ) { // 2016-12-02: per determinati utilizzi è previsto che la destinazione non sia obbligatoria
			$qStr .= 'tc=' . $qArr['to']['latitude'] . '|' . $qArr['to']['longitude'] . '&';
		}
		if( isset($qArr['outward']['mindate']) && !empty($qArr['outward']['mindate']) ) {
			
			$qStr .= 'db=' . date('d-m-Y', strtotime($qArr['outward']['mindate'])) . '&';
			
			if( isset($qArr['outward']['maxdate']) && !empty($qArr['outward']['maxdate']) ) {
				// db funziona correttamente come data inizio del periodo di ricerca, non devo fare altro
			}
			else {
				// data fine non valorizzata, db funziona come data "secca" (vengono restituiti solo i viaggi
				// per quella specifica data)
				// per ottenere i viaggi a partire dalla data specificata bisogna comunque specificare de
				// in questo caso setto de=31-12-3999
				$qStr .= 'de=31-12-3999&';
			}
		}
		if( isset($qArr['outward']['maxdate']) && !empty($qArr['outward']['maxdate']) ) {
			$qStr .= 'de=' . date('d-m-Y', strtotime($qArr['outward']['maxdate'])) . '&';
		}
		if( isset($qArr['outward']['mintime']) && !empty($qArr['outward']['mintime']) ) {
			$qStr .= 'hb=' . $qArr['outward']['mintime'] . '&'; // NOTA: solo l'ora in blablacar (formato: 1-24)
		}
		if( isset($qArr['outward']['maxtime']) && !empty($qArr['outward']['mintime']) ) {
			$qStr .= 'he=' . $qArr['outward']['maxtime'] . '&'; // NOTA: solo l'ora in blablacar (formato: 1-24)
		}
		/*if( isset($qArr['radius']) && !empty($qArr['radius']) ) {
			$qStr .= 'radius=' . $qArr['radius'] . '&';
		}*/
		// remove last '&'
		$qStr = substr($qStr, 0, strlen($qStr)-1);
		return $qStr . '&limit=100&seats=1&locale=it_IT&cur=EUR'; // max page size for blablacar (otherwise defaults to 10!)
		
	}
	
/**
 * 
 */
	function _addRdexRequestGrantStr($qStr, $provider) {
		
		$qStr .= '&timestamp='.time();
		$qStr .= '&apikey='.$provider['Provider']['apikey'];
		
		// sort query params in alphabetical order to properly sign the request
		$tokens = explode('&', $qStr);
		$qArr = array();
		foreach($tokens as $token) {
			$token = explode('=', $token);
			$qArr[ $token[0] ] = $token[1];
		}
		ksort($qArr);
		$qStr = '';
		foreach(array_keys($qArr) as $k) {
			$qStr .= $k . '=' . $qArr[$k] . '&';
		}
		// remove last '&'
		$qStr = substr($qStr, 0, strlen($qStr)-1);
		
		// NOTE: do NOT urlencode the url for signature!
		$qStr .= '&signature='.hash_hmac('sha256', $provider['Provider']['url'] . '?' . $qStr, $provider['Provider']['privatekey']);
		return array(
			'success' => TRUE,
			'q' => $qStr
		);
		
	}

/**
 * 
 */
	function _addBlablacarRequestGrantStr($qStr, $provider) {

		// 2018-05-29: è cambiato il sistema di autenticazione di Blablacar
		return array(
			'success' => true,
			'q' => $qStr . '&key='.$provider['Provider']['apikey'] // riciclo il campo
		);

		// check if the access token must be renewed
		/*$data = unserialize($provider['Provider']['data']);
		
		if(!isset($data['access_token_request_url'])) $data['access_token_request_url'] = 'https://api.blablacar.com/oauth/v2/access_token';
		if(!isset($data['access_token_request_query'])) $data['access_token_request_query'] = 'grant_type=client_credentials';
		
		$renewAccessToken = false;
		if( !isset($data['access_token']) || empty($data['access_token']) ) {
			$renewAccessToken = true;
		}
		elseif( $data['expires_in'] <= time() + 5 ) { // 5s band guard (to account for the delay in the search request)
			$renewAccessToken = true;
		}
		
		if($renewAccessToken) {
			
			$socket = new HttpSocket(array('timeout' => Configure::read('provider.socket_timeout')));
			if( Configure::read('proxy.enabled')  ) { // VARCH  21/10/2014 - a proxy can be configured
				$proxy = Configure::read('proxy.params');
				$proxy['port'] = empty($proxy['port']) ? 3218 : $proxy['port'];
				$socket->configProxy($proxy['host'], $proxy['port']);
			}
			
			$res = $socket->get($data['access_token_request_url'], $data['access_token_request_query'], array(
				'header' => array(
					'Authorization' => 'Basic ' . base64_encode($provider['Provider']['apikey'].':'.$provider['Provider']['privatekey'])
				)
			));
			
			if( $res->code == 200 ) {
				
				$serverData = json_decode($res->body);
				$data['access_token'] = $serverData->access_token;
				$data['expires_in'] = time() + $serverData->expires_in;
			}
			else {
				CakeLog::write('error', '[Journeys:_addBlablacarRequestGrantStr] Richiesta nuovo token di accesso fallita '.$res->code.' '.$res->reasonPhrase);
				$data['access_token'] = ''; // reset to force an update on next request
			}
			
			// update data on db
			$this->Provider->save(array(
				'id' => $provider['Provider']['id'],
				'data' => serialize($data)
			));
		}
		
		return array(
			'success' => !empty($data['access_token']),
			'q' => $qStr . '&access_token='.$data['access_token']
		);*/
	}
	
/**
 * 
 */
	function _translateRdexSearchResult($res) {
		
		if( !is_array($res) ) return array();
		
		$days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
		$long_days = array('mon' => 'monday', 'tue' => 'tuesday', 'wed' => 'wednesday', 'thu' => 'thursday', 'fri' => 'friday', 'sat' => 'saturday', 'sun' => 'sunday');
		
		// the aggregator doesn't care about the result returned from the provider (it is up to the provider
		// to return valid data)
		$journeys = array();
		foreach($res as $r) {
			$j = $this->_objToArr($r);
			$j = $this->_setMissingFields($j);
			
			// importante! se sono vuoti, tutti i campi 'max' devono essere valorizzati con il valore in min
			if( empty($j['outward']['maxdate']) ) {
				$j['outward']['maxdate'] = $j['outward']['mindate'];
			} 
			foreach($days as $d) {
				if( empty($j['outward'][$d]['maxtime']) ) {
					$j['outward'][$d]['maxtime'] = $j['outward'][$d]['mintime'];
				} 
			}
			// importante! setta il giorno in base alla data
			$j['days'][$long_days[strtolower(date('D', strtotime($j['outward']['mindate'])))]] = 1;
				
			$journeys[] = $j;
			
			// NON GESTISCO/CONSIDERO I VIAGGI RICORRENTI PER PROBLEMI DI VISUALIZZAZIONE DEGLI STESSI (SONO/DEVONO ESSERE TUTTI VIAGGI SINGOLI)
			
			
			// 2015-09-09: per gestire in modo semplice i viaggi ricorrenti (che ha solo RDEX) questi
			// vengono 'moltiplicati' e trasformati in viaggi singoli
			/*$numDays = 0;
			foreach($days as $d) {
				if($j['outward'][$d] == 1) $numDays++;
			}*/
			
			/*if($numDays > 1) { // ricorrente
				$original_j = $j;
				for($i=0;$i<7;$i++) {
					$d = $days[$i];
					$j[$d] = NULL;
					$j['outward_'.$d.'_mintime'] = NULL;
					$j['outward_'.$d.'_maxtime'] = NULL;
					$j['return_'.$d.'_mintime'] = NULL;
					$j['return_'.$d.'_maxtime'] = NULL;
				}
				foreach($days as $d) {
					if($original_j[$d] == 1) {
						$cloned_j = $j;
						$cloned_j['outward_'.$d.'_mintime'] = $original_j['outward_'.$d.'_mintime'];
						$cloned_j['outward_'.$d.'_maxtime'] = $original_j['outward_'.$d.'_maxtime'];
						$cloned_j['return_'.$d.'_mintime'] = $original_j['return_'.$d.'_mintime'];
						$cloned_j['return_'.$d.'_maxtime'] = $original_j['return_'.$d.'_maxtime'];
					}
					// inserisci il viaggio per tutti i giorni tra outward_mindate e outward_maxdate
					$startDay = date('N', strtotime($original_j['outward_mindate']))-1;
					$currDate = date('Y-m-d', strtotime($original_j['outward_mindate']) + ($i-$startDay)*24*3600);
					
					// NOTA: NON gestisco le return dates che non mi interessano
					while( strtotime($currDate) <= strtotime($original_j['outward_maxdate']) ) {
						$cloned_j['outward_mindate'] = $currDate;
						$cloned_j['outward_maxdate'] = $currDate;
						
						$journeys[] = $cloned_j;
						
						$currDate = date('Y-m-d', strtotime($currDate)+7*24*3600);
					}
				}
			}
			else { // non ricorrente 
				
			}*/
			
		}
		return $journeys;
	}
	
/**
 * 
 */
	function _translateBlablacarSearchResult($res) {
		
		if( !isset($res->trips) || !is_array($res->trips) ) return array();
		
		// translate
		// same as for rdex if a field doesn't exist or is empty we don't care (it is up to the provider to
		// return valid data), aggregator simply translates fields returned (when they are available)
		$journeys = array();
		$blablacarToRdexFieldsMap = array(
			'departure_date' => 'outward|mindate',
			'return_date' => 'return|maxdate',
			'departure_place|city_name' => 'from|city',
			'departure_place|address' => 'from|address',
			'departure_place|latitude' => 'from|latitude',
			'departure_place|longitude' => 'from|longitude',
			'arrival_place|city_name' => 'to|city',
			'arrival_place|address' => 'to|address',
			'arrival_place|latitude' => 'to|latitude',
			'arrival_place|longitude' => 'to|longitude',
			'price|value' => 'cost|fixed', // rdex has cost fixed and variable, using fixed 
			'price|currency' => '', // no field in rdex for currency! Assuming EUR
			'seats_left' => 'driver|seats', // according to the description in Connections -> driver the field (same for journeys) is the number of available seats
			'seats' => '',  // no mapping. There's a persons field in Connections -> passenger which could be used (seats - seats_left) but currently not using connections (what's their purpose?)
			'duration|value' => 'duration', // in seconds
			'duration|unity' => '', // no mapping. Duration in seconds will be converted later
			'distance|value' => 'distance', // in meters
			'distance|unity' => '', // no mapping. Distance in meters will be converted later
			'permanent_id' => 'uuid', 
			'main_permanent_id' => '', // no mapping. Blablacar says just 'an identifier'
			'links|_front' => 'url', 
			'links|_threads' => 'origin',
			'frequency' => 'frequency' // TODO farsi dare una lista dei tipi disponibili e convertirli in base ai tipi di RDEX
		);
		
		foreach($res->trips as $t) {
			$blablaJourney = $this->_objToArr($t);
			$rdexJourney = array();
			foreach( array_keys($blablacarToRdexFieldsMap) as $key ) {
				$fld = explode('|', $key);
				$dstFld = explode('|', $blablacarToRdexFieldsMap[$key]);
				if( empty($dstFld) ) continue; // no mapping for this field
				
				$fldValue = '';
				switch( sizeof($fld) ) {
					case 1:
						$fldValue = isset($blablaJourney[ $fld[0] ]) ? $blablaJourney[ $fld[0] ] : '';
						break;
					case 2:
						$fldValue = isset($blablaJourney[ $fld[0] ][ $fld[1] ]) ? $blablaJourney[ $fld[0] ][ $fld[1] ] : '';
						break;
					case 3:
						$fldValue = isset($blablaJourney[ $fld[0] ][ $fld[1] ][ $fld[2] ]) ? $blablaJourney[ $fld[0] ][ $fld[1] ][ $fld[3] ] : '';
						break;
				}
				
				// write the value
				switch( sizeof($dstFld) ) {
					case 1:
						$rdexJourney[ $dstFld[0] ] = $fldValue;
						break;
					case 2:
						$rdexJourney[ $dstFld[0] ][ $dstFld[1] ] = $fldValue;
						break;
					case 3:
						$rdexJourney[ $dstFld[0] ][ $dstFld[1] ][ $dstFld[2] ] = $fldValue;
						break;
				}
			}
			
			// TODO: convertire duration a seconda della unity (valori di unity?)
			// TODO: convertire distance a seconda della unity (valori di unity?)
			
			// aggiungi i campi aggiuntivi di rdex
			$rdexJourney['operator'] = 'BlaBlaCar';
			$rdexJourney['from']['postalcode'] = '';
			$rdexJourney['from']['country'] = '';
			$rdexJourney['to']['postalcode'] = '';
			$rdexJourney['to']['country'] = '';
			$rdexJourney['cost']['variable'] = 0;
			
			// IMPORTANTE: blablacar restituisce le date (data+ora) nel formato d/m/Y H:i:s. Convertire e spezzare
			// per salvare a db nel formato rdex
			
			// TODO: non avendo info al riguardo considero tutti i risultati di blablacar come journeys punctual e one way
			$days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
			$long_days = array('mon' => 'monday', 'tue' => 'tuesday', 'wed' => 'wednesday', 'thu' => 'thursday', 'fri' => 'friday', 'sat' => 'saturday', 'sun' => 'sunday');
			
			
			// tempi secondo rdex
			if(isset($rdexJourney['outward']['mindate']) && !empty($rdexJourney['outward']['mindate'])) {
				$d = str_replace('/', '-', $rdexJourney['outward']['mindate']);
				$rdexJourney['outward']['mindate'] = date('Y-m-d', strtotime($d));
				$rdexJourney['outward']['maxdate'] = date('Y-m-d', strtotime($d));
				$rdexJourney['outward'][$long_days[strtolower(date('D', strtotime($d)))]]['mintime'] = date('H:i:s', strtotime($d));
				$rdexJourney['outward'][$long_days[strtolower(date('D', strtotime($d)))]]['maxtime'] = date('H:i:s', strtotime($d));
			
				$rdexJourney['days'][$long_days[strtolower(date('D', strtotime($d)))]] = 1; // giorni secondo rdex (giorno dell'outward mindate se punctual)
			}
			if(isset($rdexJourney['return']['maxdate']) && !empty($rdexJourney['return']['maxdate'])) {
				$d = str_replace('/', '-', $rdexJourney['return']['maxdate']);
				$rdexJourney['return']['mindate'] = date('Y-m-d', strtotime($d));
				$rdexJourney['return']['maxdate'] = date('Y-m-d', strtotime($d));
				$rdexJourney['return'][$long_days[strtolower(date('D', strtotime($d)))]]['mintime'] = date('H:i:s', strtotime($d));
				$rdexJourney['return'][$long_days[strtolower(date('D', strtotime($d)))]]['maxtime'] = date('H:i:s', strtotime($d));
			}
			
			$journeys[] = $this->_setMissingFields($rdexJourney);
		}
		return $journeys;
	}
  
/**
 * prepare the search query parameters to be sent 
 */
	function _searchQueryArrayToString($qArr) {
		
		$qStr = '';
		$qStr .= 'p[from][latitude]='.$qArr['from']['latitude'].'&';
		$qStr .= 'p[from][longitude]='.$qArr['from']['longitude'].'&';
		if( isset($qArr['to']) ) { // 2016-12-02: per vari usi la destinazione non è più obbligatoria
			$qStr .= 'p[to][latitude]='.$qArr['to']['latitude'].'&';
			$qStr .= 'p[to][longitude]='.$qArr['to']['longitude'].'&';
		}
		
		// TODO: non è parte dello standard Rdex (usato esclusivamente per visualizzazione)
		if( isset($qArr['from']) && isset($qArr['from']['address']) ) { 
			$qStr .= 'p[from][address]='.(isset($qArr['from']['fulladdress']) ? $qArr['from']['fulladdress'] : $qArr['from']['address']).'&';
		}
		if( isset($qArr['to']) && isset($qArr['to']['address']) ) {
			$qStr .= 'p[to][address]='.(isset($qArr['to']['fulladdress']) ? $qArr['to']['fulladdress'] : $qArr['to']['address']).'&'; 
		}
		// END OF parte non standard in rdex
		
		if( isset($qArr['outward']['mindate']) && !empty($qArr['outward']['mindate']) ) {
			$qStr .= 'p[outward][mindate]='.$qArr['outward']['mindate'].'&';
		}
		if( isset($qArr['outward']['mintime']) && !empty($qArr['outward']['mintime']) ) {
			$qStr .= 'p[outward][mintime]='.$qArr['outward']['mintime'].'&';
		}
		if( isset($qArr['outward']['maxdate']) && !empty($qArr['outward']['maxdate']) ) {
			$qStr .= 'p[outward][maxdate]='.$qArr['outward']['maxdate'].'&';
		}
		if( isset($qArr['outward']['maxtime']) && !empty($qArr['outward']['maxtime']) ) {
			$qStr .= 'p[outward][maxtime]='.$qArr['outward']['maxtime'].'&';
		}
		if( isset($qArr['radius']) && !empty($qArr['radius']) ) {
			$qStr .= 'p[radius]='.$qArr['radius'].'&';
		}
		// remove last '&'
		$qStr = substr($qStr, 0, strlen($qStr)-1);
		return $qStr;
	}
	
/**
 * convert object to array recursively
 */
	function _objToArr($obj) {
		/*$arr = (array)$obj;
		foreach( array_keys($arr) as $k ) {
			if( is_object($arr[$k]) ) {
				$arr[$k] = $this->_objToArr($arr[$k]);
			}
		}*/
		$arr = get_object_vars($obj);
		foreach(array_keys($arr) as $k) {
			if( is_object($arr[$k]) ) {
				$arr[$k] = $this->_objToArr($arr[$k]);
			}
		}
		return $arr;
	}
	
/**
 * 
 */
	function _setMissingFields($journeyArr) {
		$fields = array(
			'uuid',
			'operator',
			'origin',
			'logo_supplier',
			'url',
			'driver|uuid',
			'driver|alias',
			'driver|image',
			'driver|seats',
			'driver|state',
			'from|address',
			'from|city',
			'from|postalcode',
			'from|country',
			'from|latitude',
			'from|longitude',
			'to|address',
			'to|city',
			'to|postalcode',
			'to|country',
			'to|latitude',
			'to|longitude',
			'distance',
			'duration',
			'route',
			'number_of_waypoints',
			'cost|fixed',
			'cost|variable',
			'details',
			'vehicle|image',
			'vehicle|model',
			'vehicle|color',
			'frequency',
			'type',
			'real_time',
			'stopped',
			'days|monday',
			'days|tuesday',
			'days|wednesday',
			'days|thursday',
			'days|friday',
			'days|saturday',
			'days|sunday',
			'outward|mindate',
			'outward|maxdate',
			'outward|monday|mintime',
			'outward|monday|maxtime',
			'outward|tuesday|mintime',
			'outward|tuesday|maxtime',
			'outward|wednesday|mintime',
			'outward|wednesday|maxtime',
			'outward|thursday|mintime',
			'outward|thursday|maxtime',
			'outward|friday|mintime',
			'outward|friday|maxtime',
			'outward|saturday|mintime',
			'outward|saturday|maxtime',
			'outward|sunday|mintime',
			'outward|sunday|maxtime',
			'return|mindate',
			'return|maxdate',
			'return|monday|mintime',
			'return|monday|maxtime',
			'return|tuesday|mintime',
			'return|tuesday|maxtime',
			'return|wednesday|mintime',
			'return|wednesday|maxtime',
			'return|thursday|mintime',
			'return|thursday|maxtime',
			'return|friday|mintime',
			'return|friday|maxtime',
			'return|saturday|mintime',
			'return|saturday|maxtime',
			'return|sunday|mintime',
			'return|sunday|maxtime',
		);
		
		foreach($fields as $fld) {
			$fld = explode('|', $fld);
			switch( sizeof($fld) ) {
				case 1:
					if( !isset($journeyArr[ $fld[0] ]) ) $journeyArr[ $fld[0] ] = '';
					break;
				case 2:
					if( !isset($journeyArr[ $fld[0] ][ $fld[1] ]) ) $journeyArr[ $fld[0] ][ $fld[1] ] = '';
					break;
				case 3:
					if( !isset($journeyArr[ $fld[0] ][ $fld[1] ][ $fld[2] ]) ) $journeyArr[ $fld[0] ][ $fld[1] ][ $fld[2] ] = '';
					break;	
			}
		}
		
		return $journeyArr;
	}
	
	function _throwBadRequestException($errorMsg) {
		$exception = new BadRequestException($errorMsg); // setting the error msg here too (useful for displaying it to the user)
		$exception->responseHeader('Warning', $errorMsg);
		throw $exception;
	}
	
}
	

