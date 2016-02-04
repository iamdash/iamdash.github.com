<?php 

require_once 'image_resizer.php';

class ImageCacher{

	protected $image_url;
	protected $image_ext;
	protected $cache_dir;
	protected $cache_dir_root;
	protected $cache_time;

	function __construct($image_url, $cache_dir){
		$this->image_url 		= $image_url;
		$this->cache_dir 		= 'cache'.DIRECTORY_SEPARATOR.$cache_dir;
		$this->cache_dir_root           = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$this->cache_dir;
		$this->image_url 		= $image_url;
		$this->file_ext 		= $this->_getImageExtension($image_url);
		$this->cache_time 		= 24 * 60 * 60;
		//d($this);
	}

	/**
	*	@name getImage
	*	@desc Get gets the url of the image by way of checking if the file exists
	*			If the file we are looking for is not yet cached, we cache it
	*			to the server by calling $this::cacheImage.
	*	@param string Path to the image
	**/

	public function getImage(){
		$file_name 			= md5($this->image_url).'.'.$this->file_ext;
		$cached_file_root 	= $this->cache_dir_root.DIRECTORY_SEPARATOR.$file_name;
		$cached_file 		= $this->cache_dir.DIRECTORY_SEPARATOR.$file_name;
		if($this->_checkCachedFileExists($cached_file_root)){
			return $cached_file;
		} else {
			$this->_cacheImage();
			return $this->cache_dir.DIRECTORY_SEPARATOR.md5($this->image_url).'.'.$this->file_ext;
		}
	}

	/**
	*	@name _getImageExtension
	*	@desc Get the file extension from the file url
	*	@param string Url of the image
	*	@return string Extension of the image
	**/

	private function _getImageExtension($image_url){
		$file_name 			= explode('.',basename($image_url));
		return $file_name[count($file_name)-1];
	}


	/**
	*	@name _getImageExtension
	*	@desc Get the file extension from the file url
	*	@param string Url of the image
	*	@return string Extension of the image
	**/

	private function _cacheImage(){
		if(!is_dir($this->cache_dir_root)){
			mkdir($this->cache_dir_root);
		}
	    $data = file_get_contents($this->image_url);
	    return file_put_contents($this->cache_dir_root .DIRECTORY_SEPARATOR. md5($this->image_url).'.'.$this->file_ext,$data); //go ahead and cache the file

	}	

	/**
	*	@name _checkCachedFileExists
	*	@desc Check if the file is already cached
	*	@param string Path of the image relative to server root, NOT doc root
	*	@return boolean True if image is already cached
	**/
	private function _checkCachedFileExists($cached_file){
		if(is_dir($this->cache_dir_root) && is_writable($this->cache_dir_root) && file_exists($cached_file) && time() - $this->cache_time < filemtime($cached_file)){
			return true;
		}else{
			return false;
		}
	}

	private function makeCacheDirs(){

	}


}

