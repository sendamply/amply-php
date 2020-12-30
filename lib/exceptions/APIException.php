<?php

namespace Amply\Exceptions;

class APIException extends \Exception {
    public $code;
    public $text;

    public function __construct($code, $body) {
        $this->code = $code;
        $this->text = $body;
        parent::__construct();
    }

    public function __toString() {
        return 'An error occurred while making an API request';
    }
}
