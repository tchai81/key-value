<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;
use App\Services\KeyValueService;
use App\Traits\CustomValidator;

class KeyValueController extends ApiServiceController {

  use CustomValidator;

	public function __construct(KeyValueService $service) {
		$this->service = $service;
	}

  /**
   * Key value pair creation
   * Assume that only a key value pair will be given at a time. Example:-
   * {"key" : "value"}
   * @param  Illuminate\Http\Request request optional
   * @return Boolean
   */
  public function create(HttpRequest $request) {
    $payload = $request->all();
    if ($this->isCreatePayloadValid($payload)) {
      return $this->respondUnprocessedEntity('The payload must contain at least a key value pair.');
    } else {
      $key = array_keys($payload)[0];
      list($key => $val) = $payload;
      $keyVal = $this->service->create($key, $val);
      return $this->respondWithJson(
        ['created' => [
          'key' => $keyVal->value,
          'unixtimestamp' => strtotime($keyVal->created_at)
        ]]);
    }
  }

  /**
   * Get value assign for a given key with the following condition:-
   * 1. Get latest value if timestamp is not given
   * 2. Get value based on a given timestamp
   * @param String key required
   * @return Illuminate\Support\Facades\Response
   */
  public function get($key) {
    if (empty(trim($key))) {
      return $this->respondUnprocessedEntity('Key cannot be empty.');
    } else {
      $timestamp = Request::get('timestamp');     
      if (!empty($timestamp) && !$this->isValidTimeStamp($timestamp)) {
        return $this->respondUnprocessedEntity('Invalid timestamp.');
      }
      $val = $this->service->getVal($key, $timestamp);
      if (!empty($val)) {
        return $this->respondWithJson(['result' => $val]);
      }
      return $this->respondNotFound("Key - {$key} not found.");
    }
  }

}