<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$cakeDescription = __("Carpooling hub - Servizi Online CSI");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">

<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=3.0, user-scalable=yes">
    <meta name="author" content="CSI-Piemonte">
    <meta name="description" content="Sistema Piemonte il portale per cittadini ed imprese">
    <meta name="keywords" content="piemonte, servizi, cittadini, imprese, sistema, amministrazione">
    <link href="<?php echo __("favicon_url"); ?>" rel="shortcut icon" type="image/vnd.microsoft.icon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        <?= $cakeDescription ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css', ['media' => 'screen']); ?>
    <?= $this->Html->css('//maxcdn.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css', ['media' => 'screen']); ?>

    <?php //$this->Html->css(['normalize.min', 'milligram.min', 'cake']) 
    ?>
    <?php echo $this->Html->css('jquery-ui-1'); ?>
    <?php echo $this->Html->css('skin'); ?>

    <?php echo Configure::read('Config.language') === "eng" ? $this->Html->css('skin.extra.eng') : ''; ?>

    <?php echo $this->Html->css('jquery-ui-1.10.3.custom.min'); ?>
    <?php echo $this->Html->css('style'); ?>

    <?= $this->Html->script('//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js'); ?>
    <?= $this->Html->script('//oss.maxcdn.com/respond/1.4.2/respond.min.js'); ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>

<body>
    <div id="portalHeader">

        <noscript class="alert_js">
            <p><?php echo __("ATTENZIONE! Il browser in uso non supporta le applicazioni Javascript.<br />
			Per usufruire in maniera completa di alcuni servizi presenti in RuparPiemonte,
			potrebbe essere necessario l&acute;utilizzo dei Javascript."); ?></p>
        </noscript>
        <div class="header">

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
                <h2><a href="<?= Router::url('/') ?>"><?php echo __("Carpooling hub"); ?></a></h2>
            </div>
        </div>
    </div>
    <!-- /header servizio-->

    <!--RIMOSSO  Fixed navbar -->
    <div class="container" id="content">
        <!--style="margin-top:70px"-->

        <!--<div class="row pulsantiera">
            <div class="col-md-12">
                <a href="http://dev-www.sistemapiemonte.it/cms/privati/territorio/servizi/781-car-pooling-hub/3135-scarica-widget" class="btn btn-primary pull-right carpooling" id="btn-embed" title="<?php echo __("Copia il widget e inseriscilo nel tuo sito"); ?>">
                    <span class="hidden"><?php echo __("Copia il widget e inseriscilo nel tuo sito"); ?></span>
                </a>

                <div class="modal fade" id="embedCodeModal" tabindex="-1" role="dialog" aria-labelledby="embedCodeModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel"><?php echo __("Copia il widget e inseriscilo nel tuo sito"); ?></h4>
                            </div>
                            <div class="modal-body">
                                <div id="iframe-code-container">
                                    <textarea style="width:100%" class="code-container"><iframe height="600" width="400" src="<?php echo $this->Url->build(array('controller' => 'journeys', 'action' => 'search?layout=embed'), array('fullBase' => true)); ?>"></iframe></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Chiudi'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </div>
    <!-- Bootstrap core JavaScript================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <?php echo $this->Html->script('ga_002'); ?>
    <?php echo $this->Html->script('ga'); ?>
    <?php echo $this->Html->script('jquery'); ?>
    <?php echo $this->Html->script('jquery-migrate-1'); ?>
    <?php echo $this->Html->script('jquery-ui'); ?>
    <?php echo $this->Html->script('bootstrap'); ?>
    <?php echo $this->Html->script('jquery.ui.touch-punch.min'); ?>
    <?= $this->fetch('script') ?>

    <!-- Footer -->
    <footer class="footer" role="contentinfo">
        <div class="container-fluid">
            <hr /><a name="footer"></a>

            <!--messe url assoluta ad accessibilità per applicativi che girano su secure perchè la cartella CMS non è proxata-->
            <div class="custom footerCsi row-fluid">
                <div class="compendio">
                    <div class="span2 col-sm-2 opticities">
                        <a href="<?php echo __('opticities_url'); ?>"><?php echo $this->Html->image('new/logoopti.png', array('alt' => 'opticities')); ?></a>
                    </div>
                    <div class="span2 col-sm-2 csi_piemonte">
                        <a href="<?php echo __("csi_url"); ?>"><?php echo $this->Html->image('new/logocsi.png', array('alt' => 'csi piemonte')); ?></a>
                    </div>
                    <div class="span2 col-sm-2 il_progetto">
                        <a href="<?php echo __("ilprogetto_url"); ?>" target="_blank"><?php echo __("Il progetto"); ?></a>
                    </div>
                    <div class="span2 col-sm-2 il_progetto">
                        <a href="<?= Router::url('/pages/widget')?>">Widget</a>
                    </div>
                    <div class="span2 col-sm-2 privacy">
                        <a href="<?php echo __("cookies_policy_url"); ?>" target="_blank">Cookies <span>policy</span></a>
                    </div>
                    <!-- <div class="span6 col-sm-6 dati_footer">
                        Regione Piemonte - Partita Iva 02843860012 - Codice fiscale 80087670016 - <a href="http://www.sistemapiemonte.it/cms/privati/accessibilita" title="Accessibilit�">Accessibilit&agrave;</a></div> -->
                </div>
            </div>
        </div>
    </footer>
    <?= $this->fetch('scriptBottom') ?>
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-7226140-1']);
        _gaq.push(['_gat._anonymizeIp']);
        _gaq.push(['_trackPageview']);

        (function() {
            var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        })();
    </script>
</body>

</html>