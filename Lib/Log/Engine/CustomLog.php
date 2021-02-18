<?php
App::uses('CakeLogInterface', 'Log');
require dirname(__FILE__) . '/log4php/Logger.php';

/**
 * カスタムログクラス log4phpをラップ
 * @author noto
 *
 */
class CustomLog implements CakeLogInterface {


    /**
     * logger
     * @var object
     */
    private $logger;


    /**
     * コンストラクタ
     * @param array $options
     */
    public function __construct($options = array()) {

        //log4php設定ファイル読み込み
        if(isset($options['properties_path'])){
            Logger::configure($options['properties_path']);
        }
        else {
            Logger::configure(APP. 'Config' . '/log4php.properties');
        }

        if(isset($options['name'])){
            $this->logger = Logger::getLogger($options['name']);
        }
        else{
            $this->logger = Logger::getLogger(get_class());
        }
    }


    /**
     * (non-PHPdoc)
     * @see CakeLogInterface::write()
     */
    public function write($type, $message) {

        switch ($type) {

            case 'debug':
                $this->logger->debug($message);
            break;

            case 'notice':
                $this->logger->info($message);
            break;

            case 'info':
                $this->logger->info($message);
            break;

            case 'warning':
                $this->logger->warn($message);
            break;

            case 'error':
                $this->logger->error($message);
            break;

            default:
                $this->logger->error($message);
            break;
        }

    }
}
 
