<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {
	
	public function url($url = null, $full = false) {
        if (is_array($url) && !array_key_exists('lang', $url)) {
            $url['lang'] = Configure::read('Config.language');
        }
        return parent::url($url, $full);
    }
    
    /**
     * rimuove l'ultimo token (il pezzo di stringa dopo l'ultima virgola) da un indirizzo di google,
     * che dovrebbe sempre corrispondere alla nazione
     */
    public function stripNazione($anAddress) {
		$tokens = explode(",", $anAddress);
		if( sizeof($tokens) > 1 ) {
			unset($tokens[sizeof($tokens)-1]);
		}
		return implode(",", $tokens);
	}
}
