<?php

namespace Amply\Exceptions;

class ResourceNotFoundException extends \Exception {
    public $errors;

    public function __construct($errors) {
        $this->errors = $errors;
        parent::__construct();
    }

    public function __toString() {
        return 'The resource was not found while making an API request';
    }
}
