<?php
/**
 * This helper builds the request body for a /mail/send API call
 */

namespace Amply;

//use Helpers\EmailHelper;

/**
 * This class is used to construct a request body for the /mail/send API call
 */
class Email {
    private $client;

    public function __construct($client) {
        $this->client = $client;
    }

    public function create($data) {
        $emailHelper = new Helpers\EmailHelper($data);
        $this->client->post('/email', $emailHelper->parsedData());
    }
}
