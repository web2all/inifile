<?php
use PHPUnit\Framework\TestCase;

class Web2All_INI_ConfigTest extends TestCase
{
  /**
   * Test config without ini configuration at all
   * 
   */
  public function testNoIniConfig()
  {
    include_once(__DIR__ . '/Config/C.php');
    $config = new Web2All_INI_Config_C();
    $web2all = new Web2All_Manager_Main($config);
    $config->setWeb2All($web2all);
    
    $ini_config=$web2all->Config->makeConfig('Web2All_INI_ConfigTest',array());
    
    $this->assertEquals('a', $ini_config['var1'], 'config value var1');
  }

  /**
   * Test config with ini file where we don't actually use it
   * 
   */
  public function testNoIniNeeded()
  {
    include_once(__DIR__ . '/Config/A.php');
    $config = new Web2All_INI_Config_A();
    $web2all = new Web2All_Manager_Main($config);
    $config->setWeb2All($web2all);
    
    $ini_config=$web2all->Config->makeConfig('Web2All_INI_ConfigTest',array());
    
    $this->assertEquals('a', $ini_config['var1'], 'config value var1');
  }

  /**
   * Test missing web2all assignment
   * 
   * @expectedException Exception
   * @expectedExceptionMessage Web2All_INI_File_Config call setWeb2All() before using
   */
  public function testNoWeb2AllException()
  {
    include_once(__DIR__ . '/Config/A.php');
    $config = new Web2All_INI_Config_A();
    $web2all = new Web2All_Manager_Main($config);
    
    $ini_config=$web2all->Config->makeConfig('Web2All_INI_SomeClass',array());
  }

  /**
   * Test missing ini file exception
   * 
   * @expectedException Exception
   * @expectedExceptionMessage Web2All_INI_File_Config the ini_file_location file
   */
  public function testNoIniException()
  {
    include_once(__DIR__ . '/Config/A.php');
    $config = new Web2All_INI_Config_A();
    $web2all = new Web2All_Manager_Main($config);
    $config->setWeb2All($web2all);
    
    $ini_config=$web2all->Config->makeConfig('Web2All_INI_SomeClass',array());
  }

  /**
   * Test config with ini file where we don't actually use it
   * 
   */
  public function testNoIniB()
  {
    include_once(__DIR__ . '/Config/B.php');
    $config = new Web2All_INI_Config_B();
    $web2all = new Web2All_Manager_Main($config);
    $config->setWeb2All($web2all);
    
    $ini_config=$web2all->Config->makeConfig('Web2All_INI_ConfigTest',array());
    
    $this->assertEquals('b', $ini_config['var1'], 'config value var1');
  }

}
?>