<?php

class TmobLabs_Tappz_Helper_Memcache extends Mage_Core_Helper_Abstract
{
 
    private $_host = "127.0.0.1";
    private $_port = "11211";
    private $_prefix = "_" ;
    private $_compress = MEMCACHE_COMPRESSED;
    protected $memcache;

    public function __construct() {
        $this->memcache = new Memcache;
         $this->memcache->connect($this->_host,  $this->_port);
    }
    public function store( $key , $data , $expire =  3600 ) {
                $this->memcache->set(md5($key) , $data , $this->_compress , $expire);

    }
    public function get($key ){
        
         $cache = $this->memcache->get(md5($key));
        if($cache) return $cache;
        return false;
    }
    public function __destruct() {
        $this->memcache->close();
    }
}