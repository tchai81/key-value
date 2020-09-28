<?php

namespace App\Repositories;

use App\Models\Key;
use Illuminate\Database\Eloquent\Model;

class KeyRepository {

   /**
   * Create a brand new key value pairs to db
   * @param String key required
   * @param String val required
   * @return Boolean
   */
    public function createWithVal($key, $val) {
        return Key::create(['name' => $key])->values()->create(['value' => $val]);
    }

   /**
   * Create value for an existing key
   * @param Mixed key required
   * @param String val required
   * @return Boolean
   */
    public function createVal($key, $val) {
        $key = $this->checkAndConvertKeyToModel($key);
        // return $key->values()->create(['value' => $val]);
    }

   /**
   * Retrieve key model from db based on a given name
   * @param String key required
   * @return Illuminate\Database\Eloquent\Model;
   */
    public function getByKey($key) {
        return Key::where('name', $key)->first();
    }

   /**
   * Retrieve value model from db based on a given key
   * @param Mixed key required
   * @return Illuminate\Database\Eloquent\Model;
   */
    public function getLatestValByKey($key) {
        $key = $this->checkAndConvertKeyToModel($key);
        return $key->values()
                    ->latest('created_at')
                    ->latest('id')
                    ->first();
    }

   /**
   * Retrieve value model from db based on a given key & date (and time)
   * @param Mixed key required
   * @param DateTime dateTime required
   * @return Illuminate\Database\Eloquent\Model;
   */
    public function getValByKeyAndDateTime($key, $dateTime) {
        $key = $this->checkAndConvertKeyToModel($key);
        return $key->values()
                    ->where('created_at', '<=', $dateTime)
                    ->latest('created_at')
                    ->latest('id')
                    ->first();
    }

   /**
   * Convert a given key to a model only if it's string
   * @param Mixed key required
   * @return Illuminate\Database\Eloquent\Model;
   */
    private function checkAndConvertKeyToModel($key) {
        if (!$key instanceof Model) {
            $key = $this->getByKey($key);
        }
        return $key;
    }

}