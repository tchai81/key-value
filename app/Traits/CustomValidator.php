<?php

namespace App\Traits;

trait CustomValidator {

  /**
   * To validate key value creation payload
   * @param Array payload required
   * @return Boolean
   */
  public function isCreatePayloadValid($payload) {
    $error = false;
    if (empty($payload)) {
      $error = true;
    } else {
      foreach($payload as $key => $value) {
        if (empty($key) || empty($value)) {
          $error = true;
          break;
        }
      }
    }
    return $error;
  }

  /**
   * To validate whether a string is a valid timestamp
   * Taken from: 
   * https://stackoverflow.com/questions/2524680/check-whether-the-string-is-a-unix-timestamp
   * @param String $timestamp required
   * @return Boolean
   */
  public function isValidTimeStamp($timestamp) {
    return ((string) (int) $timestamp === $timestamp) 
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX);
  }
   
}