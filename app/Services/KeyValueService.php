<?php

namespace App\Services;

use App\Repositories\KeyRepository;
use DateTime;

class KeyValueService {

    public function __construct(KeyRepository $repo) {
      $this->repo = $repo;
    }

    /**
     * Key value pair creation.
     * @param string $mykey
     * @param string timestamp
     * @return Illuminate\Database\Eloquent\Model;
     */
    public function create($key, $value) {
      $dbKey = $this->getByKey($key);
      if (!empty($dbKey)) {
        return $this->repo->createVal($dbKey, $value);
      } 
      return $this->repo->createWithVal($key, $value);
    }

    /**
     * Get latest value assign for a given key or
     * Get value assign for a given key with provided timestamp
     * @param string $mykey
     * @param string timestamp
     * @return string
     */
    public function getVal($key, $timestamp) {
      $dbKey = $this->getByKey($key);
      if (!empty($dbKey)) {
        if ($timestamp) {
          $dateTime = new DateTime();
          $dateTime->setTimestamp($timestamp);
          $val = $this->getValByKeyAndDateTime($dbKey, $dateTime);
        } else {
          $val = $this->getLatestValByKey($dbKey);
        }
        if (!empty($val)) {
          return $val->toArray()['value'];
        }
      }
    }

    /**
     * Get key model based on a given name
     * @param string $key
     */
    private function getByKey($key) {
      return $this->repo->getByKey($key);
    }

    /**
     * Get latest value assign for a given key
     * @param  string $mykey
     * @return Illuminate\Database\Eloquent\Model;
     */
    private function getLatestValByKey($key) {
      if (!empty($key)) {
        return $this->repo->getLatestValByKey($key);
      }
    }

    /**
     * Get value assign for a key based on a given timestamp
     * @param string $key
     * @param datetime $dateTime
     * @return Illuminate\Database\Eloquent\Model;
     */
    private function getValByKeyAndDateTime($key, $dateTime) {
      if (!empty($key)) {
        return $this->repo->getValByKeyAndDateTime($key, $dateTime);
      }
    }
}