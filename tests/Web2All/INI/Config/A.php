<?php
class Web2All_INI_Config_A extends Web2All_INI_File_Config {
  
  protected $Web2All_INI_File_Config = array(
    'ini_file_location' => 'non_existing.ini',
    'section_to_class_mapping' => array(
      'configtest'        => 'Web2All_INI_SomeClass'
    )
  );
  
  protected $Web2All_INI_ConfigTest = array(	
    'var1' => 'a'
  );

}

?>