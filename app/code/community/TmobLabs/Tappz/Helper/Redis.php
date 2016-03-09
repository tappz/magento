<?php

class TmobLabs_Tappz_Helper_Redis extends Mage_Core_Helper_Abstract
{
 
    private $_host = "127.0.0.1";
    private $_port = "6379";

    protected $redis;

    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect($this->_host,  $this->_port);
        
    }
    public function store( $key , $data , $expire =  3600 ) {
                $this->redis->setex(md5($key) ,$expire, serialize($data) );
    }
    public function get($key ){
     $cache = unserialize( $this->redis->get(md5($key)));
        if($cache) return $cache;
        return false;
    }
    public function __destruct() {
               $this->redis->close();
    }
}