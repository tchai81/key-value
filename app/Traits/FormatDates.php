<?php

namespace App\Traits;
use DateTime;

trait FormatDates {

  /**
   * To convert unix timestamp to Date Time format
   * @param String timestamp required
   * @return DateTime
   */
  public function convertTimestampToDatetime($timestamp) {
    if (!empty($timestamp)) {
      $dateTime = new DateTime();
      return $dateTime->setTimestamp($timestamp);
    }
  }
  
}