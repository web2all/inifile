<?php
use PHPUnit\Framework\TestCase;

class Web2All_INI_FileTest extends TestCase
{
  /**
   * Web2All framework
   *
   * @var Web2All_Manager_Main
   */
  protected static $Web2All;
  
  public static function setUpBeforeClass()
  {
    self::$Web2All = new Web2All_Manager_Main();
  }
  
  /**
   * Test ini file parsing
   * 
   * @param string $ini_filename
   * @param string $section_name
   * @param array $expected_config
   * @dataProvider iniSectionProvider
   */
  public function testIniFile($ini_filename,$section_name,$expected_config)
  {
    $ini_file = self::$Web2All->Factory->Web2All_INI_File();
    
    $ini_file->open(__DIR__ . DIRECTORY_SEPARATOR . $ini_filename);
    
    $section = $ini_file->getSection($section_name);
    //var_export($section);
    
    $this->assertEquals($expected_config, $section, 'parsed section configuration');
  }

  /**
   * Provide ini section tests
   * 
   * @return array
   */
  public function iniSectionProvider()
  {
    return array(
      array (  'resources/test-section-win.ini',  'section1',  array ( 'var1' => 'a', 'var2' => 'b' ) ),
      array (  'resources/test-section-win.ini',  'section2',  array ( 'var1' => 'a', 'var2' => 'b' ) ),
      array (  'resources/test-section-win.ini',  'section3',  array ( 'var1' => '1', 'var2' => '2' ) ),
      array (  'resources/test-section-win.ini',  'section4',  array ( 'var1' => 'a b c', 'var2' => '1 2 3' ) ),
      array (  'resources/test-section-lin.ini',  'section1',  array ( 'var1' => 'a', 'var2' => 'b' ) ),
      array (  'resources/test-section-lin.ini',  'section2',  array ( 'var1' => 'a', 'var2' => 'b' ) ),
      array (  'resources/test-section-lin.ini',  'section3',  array ( 'var1' => '1', 'var2' => '2' ) ),
      array (  'resources/test-section-lin.ini',  'section4',  array ( 'var1' => 'a b c', 'var2' => '1 2 3' ) ),
      array (  'resources/test-section-comments.ini',  'section1',  null ),
      array (  'resources/test-section-comments.ini',  'section2',  array ( ) ),
      array (  'resources/test-section-comments.ini',  'section3',  array ( 'var2' => '2' ) ),
      array (  'resources/test-section-comments.ini',  'section4',  array ( 'var1' => 'a b c' ) ),
      array (  'resources/test-section-special.ini',  'section1',  array ( 'var1' => '000e0102', 'var2' => '0x111111' ) ),
      array (  'resources/test-section-special.ini',  'section2',  array ( 'var1' => '112.36', 'var2' => '1,000.00' ) ),
      array (  'resources/test-section-special.ini',  'section3',  array ( 'var1' => '0', 'var2' => '' ) ),
      array (  'resources/test-section-special.ini',  'section4',  array ( 'var1' => 'a', 'var2' => '`date`' ) ),
      array (  'resources/test-section-varnames.ini',  'section1',  array ( 'var 1' => '1', 'var_2' => '1' ) ),
      array (  'resources/test-section-varnames.ini',  'section 2',  array ( '1var' => '1', '2_var' => '1' ) ),
      array (  'resources/test-section-varnames.ini',  ' section 3 ',  array ( 'var1' => '1' ) ),
      array (  'resources/test-section-varnames.ini',  'section=4',  array ( 'var1' => '1' ) )
    );
  }
}
?>