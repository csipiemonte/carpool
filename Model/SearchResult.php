<?php 

class SearchResult extends AppModel{
		
	public $name = 'SearchResult';
	
	public $belongsTo = array(
		'Provider' => array(
			'className' => 'Provider',
			'foreignKey' => 'provider_id',
			'conditions' => '',
			'fields' => ''
		)
	);
	
	/**
	 * remove old search results from db (after an hour they're considered as expired)
	 */
	public function clearExpired() {
		return $this->deleteAll(array('SearchResult.created <' => date('Y-m-d H:i:s', time() - 3600)));
	}
	
	/**
	 * remove existing search results for given session from db
	 */
	public function clearSession($session_id) {
		return $this->deleteAll(array('SearchResult.session_id' => $session_id));
	}


}
