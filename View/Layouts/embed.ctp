<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
	<head>
		<title><?php echo __("Sistema Piemonte - Registrazione");?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=yes" name="viewport">
        <meta name="author" content="CSI-Piemonte">
        <meta name="description" content="Sistema Piemonte il portale per cittadini ed imprese">
        <meta name="keywords" content="piemonte, servizi, cittadini, imprese, sistema, amministrazione">        
        <link href="<?php echo __("favicon_url");?>" rel="shortcut icon" type="image/vnd.microsoft.icon">
        
        <?php //echo $this->Html->css('bootstrap');?>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" media="screen" />
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet" media="screen" />
        
        <?php echo $this->Html->css('jquery-ui-1');?>
        <?php echo $this->Html->css('skin');?>
        <?php
			$locale = Configure::read('Config.language');
		?>
		<?php if($locale == 'eng'):?>
			<?php echo $this->Html->css('skin.extra.eng');?>
		<?php endif;?>
        
        <?php echo $this->Html->css('jquery-ui-1.10.3.custom.min');?>
		<?php echo $this->Html->css('style');?>
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    
	</head>

	<body class="embedded">

		<div id="portalHeader">
									
			<noscript class="alert_js">
			<p><?php echo __("ATTENZIONE! Il browser in uso non supporta le applicazioni Javascript.<br />
			Per usufruire in maniera completa di alcuni servizi presenti in RuparPiemonte,
			potrebbe essere necessario l&acute;utilizzo dei Javascript.");?></p></noscript>
		
			<!--<div class="row-fluid menupaprivati">
				<ul class="nav menu pull-right">
					<li class="parent">
						<a href="http://www.sistemapiemonte.it/cms/pa">Servizi per la PA</a>
					</li>
				</ul>
				<ul class="nav menu pull-right">
					<li class="active parent">
						<a href="http://www.sistemapiemonte.it/cms/privati">Servizi per privati</a>
					</li>
				</ul>
				<ul class="nav menu menuTopSx pull-left">
					<li>
						<a href="http://www.sistemapiemonte.it/cms/privati/cos-e-sistemapiemonte" class="pipe_dx">Cos'&#232; Sistemapiemonte</a>
					</li>
					<li>
						<a href="http://www.sistemapiemonte.it/cms/privati/cerca-in-privati">Cerca</a>
					</li>
				</ul>
			</div>-->			
			<div class="header">
				<h1><?php echo __("Sistemapiemonte");?></h1>
			</div>	

		</div>

		<!-- riempitivo sx (facoltativo) -->
		<div id="sx">
		</div>

		<!-- riempitivo dx (facoltativo) -->
		<div id="dx">
		</div>

		 <!-- Header servizio-->
		<div class="container-fluid">
			<div class="row">
				<div id="header-servizio" class="col-md-12">
					<h2><?php echo __("Carpooling hub");?></h2>
				</div>
			</div>
		</div>
		<!-- /header servizio-->
	   
   
		<!--RIMOSSO  Fixed navbar -->

		<div class="container" id="content"><!--style="margin-top:70px"-->

			<!--<div class="row pulsantiera">
				<div class="col-md-12">
					<a href="http://dev-www.sistemapiemonte.it/cms/privati/territorio/servizi/781-car-pooling-hub/3135-scarica-widget" class="btn btn-primary pull-right carpooling" id="btn-embed" title="<?php echo __("Copia il widget e inseriscilo nel tuo sito");?>">
						<span class="hidden"><?php echo __("Copia il widget e inseriscilo nel tuo sito");?></span>
					</a>
					
					<div class="modal fade" id="embedCodeModal" tabindex="-1" role="dialog" aria-labelledby="embedCodeModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="myModalLabel"><?php echo __("Copia il widget e inseriscilo nel tuo sito");?></h4>
								</div>
								<div class="modal-body">
									<div id="iframe-code-container">
										<textarea style="width:100%" class="code-container"><iframe height="600" width="400" src="<?php echo $this->Html->url(array('controller' => 'journeys', 'action' => 'search?layout=embed'), array('full' => true));?>"></iframe></textarea>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Chiudi');?></button>
								</div>
							</div>
						</div>
					</div>
					
				</div> 
			</div>-->

			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>		

		</div>
		<!-- /container -->

		<!-- Bootstrap core JavaScript
			================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->

		<?php echo $this->Html->script('ga_002');?>
		<?php echo $this->Html->script('ga');?>
		<?php echo $this->Html->script('jquery');?>
		<?php echo $this->Html->script('jquery-migrate-1');?>
		<?php echo $this->Html->script('jquery-ui');?>
		<?php echo $this->Html->script('bootstrap');?>
		<?php echo $this->Html->script('jquery.ui.touch-punch.min');?>
		<?php echo $scripts_for_layout; ?>
		
		<!-- Footer -->
	<footer class="footer" role="contentinfo">
		<div class="container-fluid">
			<hr /><a name="footer"></a>
			
<!--messe url assoluta ad accessibilità per applicativi che girano su secure perché la cartella CMS non è proxata-->
<div class="custom footerCsi row-fluid">
<div class="compendio">
	<div class="span2 col-sm-2 opticities">
	<a href="<?php echo __('opticities_url');?>"><?php echo $this->Html->image('new/logoopti.png', array('alt' => 'opticities'));?></a></div>
	<div class="span2 col-sm-2 csi_piemonte">
	<a href="<?php echo __("csi_url");?>"><?php echo $this->Html->image('new/logocsi.png', array('alt' => 'csi piemonte'));?></a></div>
	<div class="span2 col-sm-2 il_progetto">
	<a href="<?php echo __("ilprogetto_url");?>" target="_blank"><?php echo __("Il progetto");?></a></div>
	<div class="span2 col-sm-2 privacy">
	<a href="<?php echo __("cookies_policy_url");?>" target="_blank">Cookies <span>policy</span></a></div>
<!-- <div class="span6 col-sm-6 dati_footer">
	Regione Piemonte - Partita Iva 02843860012 - Codice fiscale 80087670016 - <a href="http://www.sistemapiemonte.it/cms/privati/accessibilita" title="Accessibilità">Accessibilit&agrave;</a></div> -->
</div>
</div>

	
		</div>
	</footer>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-7226140-1']);
  _gaq.push (['_gat._anonymizeIp']); 
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>
</html>
