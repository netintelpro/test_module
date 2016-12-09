<?php

 
class weather extends Module
{

  private function installDB()
  {

    $query = "CREATE TABLE IF NOT EXISTS `PREFIX_weather_zip`(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `zip` int(5) NOT NULL,
    PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
    $query = str_replace('PREFIX_', _DB_PREFIX_, $query);
    return Db::getInstance()->execute($query);
  }

  private function uninstallDB()
  {
    $query = "DROP  TABLE IF EXISTS `PREFIX_weather_zip`";
    $query = str_replace('PREFIX_', _DB_PREFIX_, $query);
    return Db::getInstance()->execute($query);
  
  }
  
  public function __construct()
  {
    $this->name = 'weather';
    $this->tab = 'other_modules';
    $this->version = '1.0.0';
    $this->author = 'John Williams';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
    $this->bootstrap = true;
 
    parent::__construct();
 
    $this->displayName = $this->l('Weather');
    $this->description = $this->l('Enter zipcode in config display and see weather for that zip code on front office pages.');
 
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
 
    if (!Configuration::get('WEATHER_NAME'))      
      $this->warning = $this->l('No name provided');
  }

  public function install()
  {
    if (!parent::install())
      return false;

      $this->registerHook('top');


    return $this->installDB();
  }


  public function uninstall()
  {
    if (!parent::uninstall())
      return false;
    return $this->uninstallDB();
  }
  
  private function makeAPICall($zip)
  {
    $app_id = "APPID=d5a1a85bdad190f46859d70c7eb3b9b1";
    $region = "zip=".$zip.",us";
    $url = "http://api.openweathermap.org/data/2.5/weather?".implode("&", array($app_id, $region));
    $response     = file_get_contents($url);
    $weather_data = json_decode($response, true);
    $weather_main = $weather_data['weather'][0]['main'];
    $weather_desc = $weather_data['weather'][0]['description'];
    $temp         = $weather_data['main']['temp'];
    $humidity     = $weather_data['main']['humidity'];
    $location     = $weather_data['name'];
    $output = "<ul>".
      "<li>"."Location: ".$location."</li>".
      "<li>"."Temp: ".($temp * 9/5 - 459.67)."F</li>".
      "<li>"."Description: ".ucwords($weather_desc)."</li>".
      "<li>"."Humidity: ".$humidity."</li></ul>";

    
    return $output;
  
  }
  
  public function hookTop($params)
  {
    $zip = Db::getInstance()->getValue("select zip from "._DB_PREFIX_."weather_zip");
    $weather = $this->makeAPICall($zip);
    return '<h1 style= "float: left; margin-left: 17px;">'.$weather.'</h1>';

  }
  
  public function submitConfiguration()
  {
    $table = _DB_PREFIX_.'weather_zip';
    
    if (Tools::isSubmit('weather_form_button'))
      {
          $countQuery  = "select count(*) from ".$table;

          $zipcode     = Tools::getValue('zipcode');

          $data        = array('zip' => $zipcode);

          if ((int)Db::getInstance()->getValue($countQuery) == 0){
            Db::getInstance()->insert($table,$data);
            $sql = 'Insert into  '.$table.' (zip) values ('.$zipcode.')';
            if (!Db::getInstance()->execute($sql))
                die('error!');
          }
            
          else
          {
            $sql = 'UPDATE '.$table.' SET zip='.$zipcode.' WHERE id=1';
            if (!Db::getInstance()->execute($sql))
                die('error!');
          }
        

      }
  }

  public function assignContent()
  {
    $table = _DB_PREFIX_.'weather_zip';

    $zipcode = Db::getInstance()->getValue('SELECT zip FROM '.$table);
    $this->context->smarty->assign('zipcode', $zipcode);
  }

  public function getContent()
  {
    $this->submitConfiguration();
    $this->assignContent();
    return $this->display(__FILE__, 'getContent.tpl');
  }


}
?>
