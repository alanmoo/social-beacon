<?php

namespace SocialBeacon;

class SocialBeaconConfig {
  private static $file_location = "../auth/config.json";

  public function __construct() {
    $config = json_decode(file_get_contents(static::$file_location));
    $this->master_sheet = $config->config->master_spreadsheet;
  }

  public function getMasterSpreadsheet() {
    return $this->master_sheet;
  }

  private $master_sheet = "";
}

?>
