<?php echo $this->Html->script('stats', array('inline' => false));?>

<label>Server base URL</label>
		<br/>
		<input type="text" id="server" name="server" value="http://impronta48.it/opticities/aggregator" />
		<br/>
		<span><i>(es. http://mioserver.it - senza '/' finale)</i></span>
		<br/>
		<br/>
		
		<label>Providers</label>
		<br/>
		<input type="text" id="providers" name="providers" value="2,3" />
		<br/>
		<span><i>(specificare gli id dei provider separati da virgola)</i></span>
		<br/>
		<br/>
		
		<label>Partenze</label>
		<br/>
		<textarea id="from" rows="10" cols="50">
Milano,45.464161,9.190336
Varese,45.816667,8.833333
		</textarea>
		<br/>
		<span><i>(specificare un punto di partenza per ogni riga inserendo separati da virgola nome localita', latitudine, longitudine)</i></span>
		<br/>
		<br/>
	
		
		<button id="process">Ottieni numero tratte</button>
	
		<span id="progress" style="display:none"></span>
	
		<br/>
		<br/>
