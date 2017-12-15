<?php
class Web2All_INI_Config_B extends Web2All_INI_File_Config {
  
  protected $Web2All_INI_File_Config = array(
    'ini_file_mode' => self::INI_PATH_RELATIVE,
    'ini_file_path' => __DIR__,
    'ini_file_location' => '/../resources/config-b.ini',
    'section_to_class_mapping' => array(
      'configtest'        => 'Web2All_INI_ConfigTest'
    )
  );
  
  protected $Web2All_INI_ConfigTest = array(	
    'var1' => 'a'
  );

}

?>