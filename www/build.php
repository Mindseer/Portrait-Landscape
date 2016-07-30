<?php
  require_once(trim($_SERVER["DOCUMENT_ROOT"], "\\/") . "/config.php");
?>
<pre>
<?php
  //print_r(Config::$config);
  
  $db->exec("
CREATE TABLE ge_portrait (
  portraitirn   INTEGER,
  title         TEXT,
  datecreated   TEXT,
  datetopical   TEXT,
  media         TEXT,
  medium        TEXT,
  support       TEXT,
  dimensions    TEXT,
  label         TEXT,
  creditline    TEXT,
  location      TEXT
);

CREATE TABLE ge_portrait_image (
  portrait      INTEGER,
  imageurl      TEXT
);

CREATE TABLE ge_portrait_artist (
  portrait      INTEGER,
  artist        INTEGER
);

CREATE TABLE ge_portrait_subject (
  portrait      INTEGER,
  subject       INTEGER
);

CREATE TABLE ge_person (
  personirn     INTEGER,
  name          TEXT,
  biography     TEXT
);
  ");
  exit;
?>