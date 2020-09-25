<?php

namespace App\Traits;

trait APIResponseTrait
{
    public function ApiV1Response(ApiV1Response $response){
        return response()->json($response->getAll(), $response->code);
    }

}

class ApiV1Response
{
    public $code = 200;
    public $success = true;
    public $data = null;
    public $errors = null;

    public function __construct($code = 200, $success = true, $data = [], $error = [])
    {

    }

    public function getAll(){
        return [
            'code' => $this->code,
            'success' => $this->success,
            'data' => $this->data,
            'errors' => $this->errors,
        ];
    }

}

class ApiV2Response
{

}
