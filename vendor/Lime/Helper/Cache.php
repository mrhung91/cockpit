<?php

namespace Lime\Helper;


class Cache extends \Lime\Helper {

  protected $cachePath = null;


  public function initialize(){
    $this->cachePath = rtrim(sys_get_temp_dir(),"/\\")."/";
  }

  /**
  * ...
  *
  */
  public function setCachePath($path){
    if($path){
      $this->cachePath = rtrim($path, "/\\")."/";
    }
  }

  /**
  * ...
  *
  */
  public function getCachePath(){

    return $this->cachePath;
  }

  /**
  * ...
  *
  */
	public function write($key, &$value, $duration = -1){

    $expire = ($duration==-1) ? -1:(time() + (is_string($duration) ? strtotime($duration):$duration));

    $safe_var = array(
      'expire' => $expire,
      'value' => serialize($value)
    );

    file_put_contents($this->cachePath.md5($key).".cache" , serialize($safe_var));
  }

  /**
  * ...
  *
  */
	public function read($key, $default=null){
    $var = @file_get_contents($this->cachePath.md5($key).".cache");

    if($var===''){
      return $default;
    }else{

      $time = time();
      $var  = unserialize($var);

      if(($var['expire'] < $time) && $var['expire']!=-1){
        return is_callable($default) ? call_user_func($default):$default;
      }

      return unserialize($var['value']);
    }
  }

  /**
  * ...
  *
  */
	public function delete($key){

    $file = $this->cachePath.md5($key).".cache";

    if (file_exists($file)) {
      @unlink($file);
    }

  }

  /**
  * ...
  *
  */
  public function clear(){

    $iterator = new \RecursiveDirectoryIterator($this->cachePath);

    foreach($iterator as $file) {
       if($file->isFile() && substr($file, -6)==".cache") {
          @unlink($this->cachePath.$file->getFilename());
       }
    }
  }
}