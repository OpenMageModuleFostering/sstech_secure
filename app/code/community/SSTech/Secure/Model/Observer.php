<?php
class SSTech_Secure_Model_Observer
{
	private $redirect_page = null;
	private $redirect_blank = null;
	private $raw_allow_ip_data = null;
	private $raw_block_ip_data = null;
	
    public function __construct()
	{
    }
	
	/*
		@Comment to check the Frontend configuration
		@author SSTech 	
	*/
    public function apply_seecurefrontend_check_frontend($observer)
	{
			$this->redirect_page = $this->trim_slashes(Mage::getUrl(Mage::getStoreConfig('admin/securefrontend/redirect_page')));
			$this->redirect_blank = Mage::getStoreConfig('admin/securefrontend/redirect_blank');
			$this->raw_allow_ip_data = Mage::getStoreConfig('admin/securefrontend/allow');
			$this->raw_block_ip_data = Mage::getStoreConfig('admin/securefrontend/block');
			$this->apply_ip_check($observer);
    }

    /*
		@Comment to check the Admin configuration
		@author SSTech 	
	*/
	
    public function apply_secureadmin_check_admin($observer)
	{
			$this->redirect_page = $this->trim_slashes(Mage::getUrl(Mage::getStoreConfig('admin/secureadmin/redirect_page')));
			$this->redirect_blank = Mage::getStoreConfig('admin/secureadmin/redirect_blank');
			$this->raw_allow_ip_data = Mage::getStoreConfig('admin/secureadmin/allow');
			$this->raw_block_ip_data = Mage::getStoreConfig('admin/secureadmin/block');
			$this->apply_ip_check($observer);
    }
	
	 /*
		@Comment to restrict and Secure Ip 
		@author SSTech 	
	*/
    public function apply_ip_check($observer)
	{
		$current_ip = $_SERVER['REMOTE_ADDR'];
		$allow = true;
		$allow_ips = null;
		$block_ips = null;
		$current_page = $this->trim_slashes(Mage::helper('core/url')->getCurrentUrl());
		
		if(strlen($this->redirect_page)){$this->trim_slashes(Mage::getUrl('no-route'));}
		
		$allow_ips = explode("\r\n", $this->raw_allow_ip_data);
		$block_ips = explode("\r\n", $this->raw_block_ip_data);
		
		if(trim($this->raw_allow_ip_data)>0){
			$allow = false;
			if($this->find_ip($current_ip,$allow_ips)){
				$allow = true;
			}
		}
		if(trim($this->raw_block_ip_data)>0){
			if($this->find_ip($current_ip,$block_ips)){
				$allow = false;
			}
		}
		if($this->redirect_blank==1 && !$allow){
			exit();
		}
		if($current_page!=$this->redirect_page && !$allow){
			header('Location: '.$this->redirect_page);
			exit();
		}
		return $this;
    }
	
	 /*
		@Comment To Find IP  
		@author SSTech 	
	*/
	private function find_ip($search_ip,$array)
	{
		$found = false;
		if(count($array)>0){
			foreach($array as $ip){
				if(preg_match('/^'.str_replace(array('\*','\?'), array('(.*?)','[0-9]'), preg_quote($ip)).'$/',$search_ip)){
					$found = true;
				}
			}
		}
		return $found;
	}
	
	private function trim_slashes($str)
	{
		$str = trim($str);
		return $str == '/' ? $str : rtrim($str, '/');
	}	
}