<?php

use Amply\Client;
use Amply\Email;

/**
 * Send emails through Amply
 */

/**
 * This class is used to construct a request body for API calls
 */
class Amply {
    public $email;

    public function __construct($accessToken) {
        $client = new Client($accessToken);

        $this->email = new Email($client);
    }
}
