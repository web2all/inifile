<?php

/**
 * Web2All INI File Config class
 * 
 * This is a special class, extending Web2All_Manager_Config. The goal
 * of this class is to allow settings which are defined in the Config
 * classes to be extended with values from INI files.
 * 
 * This class will do it pretty transparent, only slight changes in usage 
 * of configuration handling is needed. The only drawback is you need to include
 * this class right at the top of your script because during config loading, no 
 * framework is yet available.
 * 
 * Example usage:
 * ------------------------------------------------------------------
 * // in your php file
 * $web2all->Config->setWeb2All($web2all);
 * 
 * // in your class (include/Web2All/MYProject/MYClass.class.php):
 * $defaultconfig =  array(
 * );
 * $requiredconfig=array(
 *   'some_config_key' => true
 * );
 * $this->Web2All->Config->validateConfig('Web2All_MYProject_MYClass',$requiredconfig);
 * $this->config=$this->Web2All->Config->makeConfig('Web2All_MYProject_MYClass',$defaultconfig);
 * 
 * // your config (include/Web2All/MYProject/Config/Dev.class.php):
 * class Web2All_MYProject_Config_Dev extends Web2All_INI_File_Config {
 *   protected $Web2All_INI_File_Config = array(
 *     'ini_file_location' => '/etc/myproject.ini',
 *     'section_to_class_mapping' => array(
 *       'myclass' => 'Web2All_MYProject_MYClass'
 *     )
 *   );
 *   protected $Web2All_MYProject_MYClass = array(
 *     'some_config_key' => 'some value'
 *   );
 * }
 * 
 * // your INI file (/etc/myproject.ini)
 * [myclass]
 * some_config_key = some other value
 * ------------------------------------------------------------------
 * 
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2014-2017 Web2All BV
 * @since 2014-07-22
 */
class Web2All_INI_File_Config extends Web2All_Manager_Config implements Web2All_Manager_PluginInterface {
  use Web2All_Manager_PluginTrait;
  
  /**
   * INI_PATH_ABSOLUTE: Use only the 'ini_file_location' to locate the ini file.
   *                    This is the default.
   * 
   * @var int
   */
  const INI_PATH_ABSOLUTE = 0;
  /**
   * INI_PATH_RELATIVE: Use the 'ini_file_path' concatenated with 'ini_file_location' to locate the ini file.
   * 
   * @var int
   */
  const INI_PATH_RELATIVE = 1;
  
  /**
   * Has the ini file been loaded yet? (we try to delay this if possible)
   * 
   * @var boolean
   */
  private $_ini_file_loaded=false;
  
  /**
   * The default configuration for the ini file (none)
   * 
   * @var array
   */
  protected $Web2All_INI_File_Config = array(
    'ini_file_location' => null,
    'section_to_class_mapping' => array(
    )
  );
  
  /**
   * Build a config array for a specific plugin, if config settings are not set,
   * use the default config value. All keys in $overrulingconfig will always
   * override the values in this config and the defaultconfig.
   * 
   * @param string $pluginname
   * @param array $defaultpluginconfig
   * @param array $overrulingconfig
   * @return array
   */
  public function makeConfig($pluginname,$defaultpluginconfig,$overrulingconfig=null)
  {
    if(!$this->_ini_file_loaded){
      if($this->needINIFile($pluginname)){
        $this->loadINIFile();
      }
    }
    
    return parent::makeConfig($pluginname,$defaultpluginconfig,$overrulingconfig);
  }
  
  /**
   * Validate a specific plugin config against the given array
   * with config keys. The pluginconfig must exist and each configkey
   * in requiredconfig must exist also. When not valid, an exception will be thrown
   * (can be catched in calling method)
   * 
   * @param string $pluginname
   * @param array $requiredconfig
   * @param array $overrulingconfig
   * @return boolean  (always true) throws Exception on error
   */
  public function validateConfig($pluginname,$requiredconfig=array(), $overrulingconfig = null)
  {
    if(!$this->_ini_file_loaded){
      if($this->needINIFile($pluginname)){
        $this->loadINIFile();
      }
    }
    
    return parent::validateConfig($pluginname,$requiredconfig,$overrulingconfig);
  }
  
  /**
   * Check if we need to load the ini file for the given plugin class
   *
   * @param string $pluginname  classname
   * @return boolean
   */
  public function needINIFile($pluginname)
  {
    if(isset($this->Web2All_INI_File_Config['section_to_class_mapping']) && is_array($this->Web2All_INI_File_Config['section_to_class_mapping'])){
      foreach($this->Web2All_INI_File_Config['section_to_class_mapping'] as $section_name => $class_name){
        if($pluginname === $class_name){
          return true;
        }
      }
    }
    return false;
  }
  
  /**
   * Update the config with values from the INI file
   *
   * 
   */
  public function loadINIFile()
  {
    // check config:
    // first check if the Web2All_INI_File_Config is available at all
    if (!(isset($this->Web2All_INI_File_Config) && is_array($this->Web2All_INI_File_Config))) {
      throw new Exception('No config defined for plugin Web2All_INI_File_Config');
    }
    // ini_file_location
    if(!isset($this->Web2All_INI_File_Config['ini_file_location'])){
      throw new Exception('Web2All_INI_File_Config needs configuration for ini_file_location');
    }
    // check if not too early
    if(!isset($this->Web2All)){
      // its too early, we cannot load ini file yet
      throw new Exception('Web2All_INI_File_Config call setWeb2All() before using');
    }
    
    if(!isset($this->Web2All_INI_File_Config['ini_file_mode'])){
      $this->Web2All_INI_File_Config['ini_file_mode'] = self::INI_PATH_ABSOLUTE;
    }
    
    // load ini file
    $config_ini_filename = $this->Web2All_INI_File_Config['ini_file_location'];
    if($this->Web2All_INI_File_Config['ini_file_mode'] == self::INI_PATH_RELATIVE){
      // ini_file_path
      if(!isset($this->Web2All_INI_File_Config['ini_file_path'])){
        throw new Exception('Web2All_INI_File_Config needs configuration for ini_file_path because you set ini_file_mode=INI_PATH_RELATIVE');
      }
      $config_ini_filename = $this->Web2All_INI_File_Config['ini_file_path'] . $this->Web2All_INI_File_Config['ini_file_location'];
    }
    if(!is_readable($config_ini_filename)){
      throw new Exception('Web2All_INI_File_Config the ini_file_location file ('.$config_ini_filename.') is not readable');
    }
    $config_ini_file = $this->Web2All->Plugin->Web2All_INI_File();
    $config_ini_file->open($config_ini_filename);
    // section_to_class_mapping
    foreach($this->Web2All_INI_File_Config['section_to_class_mapping'] as $section_name => $class_name){
      $section=$config_ini_file->getSection($section_name);
      if(is_null($section) || !is_array($section)){
        throw new Exception('Web2All_INI_File_Config the '.$config_ini_filename.' is missing section ['.$section_name.']');
      }
      // now overwrite the config settings with settings from ini file
      foreach($section as $key => $value){
        $this->{$class_name}[$key]=$value;
      }
    }
    $this->_ini_file_loaded=true;
  }
  
}

?>