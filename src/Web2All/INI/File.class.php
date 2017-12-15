<?php

/**
 * Web2All INI File class
 * 
 * This class is for managing files in INI format
 * 
 * @todo: implement proper working with ini files without sections
 * 
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2014-2017 Web2All BV
 * @since 2014-01-16
 */
class Web2All_INI_File extends Web2All_Manager_Plugin {
  
  /**
   * assoc array with this files (parsed) data
   *
   * @var array
   */
  protected $inifile_data=array();
  
  /**
   * Should sections be parsed and represented in the inifile_data?
   *
   * @var boolean
   */
  protected $parse_sections=true;
  
  /**
   * Should sections be parsed and represented in the inifile_data?
   *
   * @var boolean
   */
  protected $lineend="\n";
  
  /**
   * The filename (including full path) of last opened ini file
   * 
   * used for saving the same file again
   *
   * @var string
   */
  protected $last_opened_filename;
  
  /**
   * Open and load an ini file
   *
   * @param string $filename  filename including path
   * @return boolean  successfully opened?
   */
  public function open($filename)
  {
    if(is_readable($filename)){
      $this->inifile_data = parse_ini_file($filename, $this->parse_sections);
    }else{
      $this->inifile_data = false;
    }
    if($this->inifile_data===false){
      // failed to load data
      $this->inifile_data=array();
      return false;
    }else{
      // successfully read ini file, so remember filename
      $this->last_opened_filename=$filename;
      return true;
    }
  }
  
  /**
   * Save ini file to the given file
   *
   * @param string $filename  [optional] filename including path, when not given, 
   *                                     write to same file which was opened before.
   * @return boolean  successfully written?
   */
  public function write($filename=null) {
    if(empty($filename)){
      // no filename given, lets see if this file was opened before
      if(!empty($this->last_opened_filename)){
        $filename=$this->last_opened_filename;
      }else{
        // no filename to write to
        return false;
      }
    }
    $string = '';
    foreach(array_keys($this->inifile_data) as $key) {
      $string .= '['.$key."]".$this->lineend;
      $string .= $this->write_get_string($this->inifile_data[$key], '').$this->lineend;
    }
    if(file_put_contents($filename, $string)===false){
      return false;
    }else{
      return true;
    }
  }
  
  /**
   *  write get string [recursive]
   */
  protected function write_get_string($ini, $prefix) {
    $string = '';
    //ksort($ini);
    foreach($ini as $key => $val) {
      if (is_array($val)) {
        $string .= $this->write_get_string($ini[$key], $prefix.$key.'.');
      } else {
        $string .= $prefix.$key.' = '.str_replace("\n", "\\\n", $this->convertValue($val)).$this->lineend;
      }
    }
    return $string;
  }
  
  /**
   * convert ini values
   * 
   * turns booleans into ini keywords, in all other cases the return value is the
   * same as the incoming $val.
   * 
   * @param mixed $val  
   * @return mixed
   */
  protected function convertValue($val) {
    if ($val === true) { return 'true'; }
    else if ($val === false) { return 'false'; }
    return $val;
  }
  
  /**
   * print to debuglog
   * 
   */
  public function debugDump()
  {
    $this->Web2All->debugLog(print_r($this->inifile_data,true));
  }
  
  /**
   * Get an ini value
   *
   * @param string $key  the key for which to get the value
   * @param string $section  optional section
   * @return string  the value or null if not found
   */
  public function getValue($key, $section=null)
  {
    if(is_null($section)){
      if(array_key_exists($key,$this->inifile_data)){
        return $this->inifile_data[$key];
      }else{
        // key not found
        return null;
      }
    }else{
      if(array_key_exists($section,$this->inifile_data)){
        if(array_key_exists($key,$this->inifile_data[$section])){
          return $this->inifile_data[$section][$key];
        }else{
          // key not found
          return null;
        }
      }else{
        // unknown section
        return null;
      }
    }
  }
  
  /**
   * Set an ini value
   *
   * @param string $key  the key for which to get the value
   * @param string $value
   * @param string $section  optional section
   * @return boolean
   */
  public function setValue($key, $value, $section=null)
  {
    if(is_null($section)){
      $this->inifile_data[$key]=$value;
    }else{
      if(array_key_exists($section,$this->inifile_data)){
        $this->inifile_data[$section][$key]=$value;
      }else{
        // unknown section
        return false;
      }
    }
    return true;
  }
  
  /**
   * add a new section
   *
   * @param string $name  the name of the new section
   * @return boolean
   */
  public function addSection($name)
  {
    if(array_key_exists($name,$this->inifile_data)){
      // section already exists
      return false;
    }else{
      $this->inifile_data[$name]=array();
      return true;
    }
  }
  
  /**
   * get section data
   *
   * @param string $name  the name of the section
   * @return string[] or null if not found
   */
  public function getSection($name)
  {
    if(!array_key_exists($name,$this->inifile_data)){
      // section not found
      return null;
    }else{
      return $this->inifile_data[$name];
    }
  }
  
  /**
   * Set section data, will overwrite existing data
   *
   * @param string $name  the name of the section
   * @param array $data  assoc array with sectionkey/value pairs
   */
  public function setSection($name, $data)
  {
    $this->inifile_data[$name]=$data;
  }
}
?>