<?php

namespace Amply\Exceptions;

class ValidationException extends \Exception {
    public $errors;

    public function __construct($errors) {
        $this->errors = $errors;
        parent::__construct();
    }

    public function __toString() {
        return 'A validation error occurred while making an API request';
    }
}
