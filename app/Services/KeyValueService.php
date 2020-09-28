<?php

namespace App\Services;

use App\Repositories\KeyRepository;
use App\Traits\FormatDates;
use DateTime;

class KeyValueService {

    use FormatDates;

    public function __construct(KeyRepository $repo) {
      $this->repo = $repo;
    }

    /**
     * Key value pair creation.
     * @param String key required
     * @param String val required
     * @return Illuminate\Database\Eloquent\Model;
     */
    public function create($key, $val) {
      $dbKey = $this->getByKey($key);
      if (!empty($dbKey)) {
        return $this->repo->createVal($dbKey, $val);
      } 
      return $this->repo->createWithVal($key, $val);
    }

    /**
     * Get latest value assign for a given key or
     * Get value assign for a given key with provided timestamp
     * @param String key required
     * @param String timestamp optional
     * @return String
     */
    public function getVal($key, $timestamp) {
      $dbKey = $this->getByKey($key);
      if (!empty($dbKey)) {
        if ($timestamp) {
          $dateTime = $this->convertTimestampToDatetime($timestamp);
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
     * @param String key required
     */
    private function getByKey($key) {
      return $this->repo->getByKey($key);
    }

    /**
     * Get latest value assign for a given key
     * @param String key required
     * @return Illuminate\Database\Eloquent\Model;
     */
    private function getLatestValByKey($key) {
      if (!empty($key)) {
        return $this->repo->getLatestValByKey($key);
      }
    }

    /**
     * Get value assign for a key based on a given timestamp
     * @param String key required
     * @param datetime dateTime required
     * @return Illuminate\Database\Eloquent\Model;
     */
    private function getValByKeyAndDateTime($key, $dateTime) {
      if (!empty($key)) {
        return $this->repo->getValByKeyAndDateTime($key, $dateTime);
      }
    }
}