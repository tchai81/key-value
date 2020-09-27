<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class ApiServiceController extends Controller {

    private $statusCode = 200;

    /**
     * @return mixed
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     *
     * @return self
     */
    public function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Respond with not found message
     * @param  string $message
     * @return Illuminate\Support\Facades\Response
     */
    public function respondNotFound($message = "Not Found!") {
        return $this->setStatusCode(404)->respondWithMessage($message);
    }

    /**
     * Respond with not authorized message
     * @param  string $message
     * @return Illuminate\Support\Facades\Response
     */
    public function respondForbidden($message = "Not Authorized!") {
        return $this->setStatusCode(403)->respondWithMessage($message);
    }

    /**
     * Respond with designated error message
     * @param  String $message
     * @return Illuminate\Support\Facades\Response
     */
    public function respondWithMessage($message) {
        return $this->respondWithJson([
            "message" => $message
        ]);
    }

    /**
     * Construct json structure to be returned
     * @param  array $data
     * @param  array  $headers
     * @return Illuminate\Support\Facades\Response
     */
    public function respondWithJson($data, $headers = []) {
        return response()->json($data, $this->getStatusCode());
    }

}
