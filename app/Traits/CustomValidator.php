<?php

namespace App\Traits;

trait CustomValidator {

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
  
}