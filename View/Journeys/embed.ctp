<div class="alert alert-info"><?php echo __("Questa è la versione embedded della pagina di ricerca dell'aggregatore di CarPooling");?></div>

<br><br>

<iframe height="600" width="400" src="<?php echo $this->Html->url(array('controller' => 'journeys', 'action' => 'search', '?' => array('layout'=>'embed')), array('full' => true));?>"></iframe>
