<?php

namespace App\Traits;
use DateTime;

trait FormatDates {

  public function convertTimestampToDatetime($timestamp) {
    $dateTime = new DateTime();
    return $dateTime->setTimestamp($timestamp);
  }
  
}