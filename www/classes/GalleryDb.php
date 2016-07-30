<?php
  class GalleryDb extends SQLite3
  {
    function __construct()
    {
      $this->open(Config::get('data_path') . 'Gallery.db');
    }
  }
?>