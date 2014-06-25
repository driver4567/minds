<?php
/**
 * Minds main class
 */
namespace minds\core;

class multisite extends base{

	protected $domain;	

	public function __construct($domain = NULL){
		global $DOMAIN;

		if(!$DOMAIN && isset($_SERVER['HTTP_HOST']))
			$this->domain = $_SERVER['HTTP_HOST'];
		elseif($DOMAIN)
			$this->domain = $DOMAIN;

		if($this->domain)
			$this->load($this->domain);
	}

	public function load($domain){
		global $CONFIG;
		if(!$row = $this->getCache($domain) ){
			$db = new data\call('domain', $CONFIG->multisite->keyspace, $CONFIG->multisite->servers);
               		$row = $db->getRow($domain);
			$this->saveCache($domain, $row);
		}

		$CONFIG->cassandra->keyspace = unserialize($row['keyspace']);
		$CONFIG->wwwroot = unserialize($row['wwwroot']);
        	$CONFIG->dataroot = unserialize($row['dataroot']);
	}

	public function getCache($domain){
		//check the tmp directory to see if there is a cached config of the site
		$path = "/tmp/nodes/$domain";
		if(file_exists($path)){
			return json_decode(file_get_contents($path), true);
		}
		return false;
	}

	public function saveCache($domain, $data){
		$path = "/tmp/nodes/$domain";
		@mkdir('/tmp/nodes/');
		file_put_contents($path, json_encode($data));
	}

	public function getKeyspace($domain = NULL){
		global $CONFIG;
		$db = new data\call('domain', $CONFIG->multisite->keyspace, $CONFIG->multisite->servers);
		$row = $db->getRow($domain);
		return $keyspace = unserialize($row['keyspace']);
	}
	
}
