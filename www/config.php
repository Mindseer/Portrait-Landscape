<?php
  class Config
  {
    public static $config = array();
    
    public static function get($key)
    {
      return self::$config[$key];
    }
    
    public static function set($key, $value)
    {
      self::$config[$key] = $value;
    }
  }
  
  $ROOT = trim($_SERVER['DOCUMENT_ROOT'], '\\/');
  
  $config_override = array(
    'root_path' => "{$ROOT}/",
    'data_path' => "{$ROOT}/data/",
    'class_path' => "{$ROOT}/classes/"
  );
  
  array_walk($config_override, function($value, $key) {
    Config::set($key, $value);
  });
  
  spl_autoload_register(function ($class_name) {
    include Config::get('class_path') . $class_name . '.php';
  });
  
  $db = new GalleryDb();
?>