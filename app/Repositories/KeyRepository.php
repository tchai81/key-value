<?php

namespace App\Repositories;

use App\Models\Key;
use Illuminate\Database\Eloquent\Model;

class KeyRepository {

   /**
   * Create a brand new key value pairs to db
   * @param String  $key
   * @param String  $value
   * @return Boolean
   */
    public function createWithVal($key, $value) {
        return Key::create(['name' => $key])->values()->create(['value' => $value]);
    }

   /**
   * Create value for an existing key
   * @param String $key
   * @param Model $key
   * @param String  $value
   * @return Boolean
   */
    public function createVal($key, $value) {
        $key = $this->checkAndConvertKeyToModel($key);
        return $key->values()->create(['value' => $value]);
    }

   /**
   * Retrieve key model from db based on a given name
   * @param String  $key
   * @return Illuminate\Database\Eloquent\Model;
   */
    public function getByKey($key) {
        return Key::where('name', $key)->first();
    }

   /**
   * Retrieve value model from db based on a given key
   * @param String $key
   * @param Model $key
   * @return Illuminate\Database\Eloquent\Model;
   */
    public function getLatestValByKey($key) {
        $key = $this->checkAndConvertKeyToModel($key);
        return $key->values()->orderBy('created_at', 'DESC')->first();
    }

   /**
   * Retrieve value model from db based on a given key & date (and time)
   * @param String $key
   * @param Model $key
   * @param DateTime $dateTime
   * @return Illuminate\Database\Eloquent\Model;
   */
    public function getValByKeyAndDateTime($key, $dateTime) {
        $key = $this->checkAndConvertKeyToModel($key);
        return $key->values()
                    ->where('created_at', '<=', $dateTime)
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('id', 'DESC')->first();
    }

   /**
   * Convert a given key to a model only if it's string
   * @param String $key
   * @param Model $key
   * @return Illuminate\Database\Eloquent\Model;
   */
    private function checkAndConvertKeyToModel($key) {
        if (!$key instanceof Model) {
            $key = $this->getByKey($key);
        }
        return $key;
    }

}