<?php

namespace Amply\Helpers;

class EmailAddress {
    public $name;
    public $email;

    public function __construct($data) {
        if (is_string($data)) {
            $data = $this->fromString($data);
        }

        if (is_a($data, 'Amply\Helpers\EmailAddress')) {
            $data = array('name' => $data->name, 'email' => $data->email);
        }

        if (!is_array($data)) {
            throw new \Exception('Expecting array or string for email address data');
        }

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;

        $this->setName($name);
        $this->setEmail($email);
    }

    public function toJson() {
        $json = array('email' => $this->email);

        if (isset($this->name)) {
            $json['name'] = $this->name;
        }

        return $json;
    }

    private function setName($name) {
        if (!isset($name)) {
            return;
        }

        if (!is_string($name)) {
            throw new \Exception('String expected for `name`');
        }

        $this->name = $name;
    }

    private function setEmail($email) {
        if (!isset($email)) {
            throw new \Exception('Must provide `email`');
        }

        if (!is_string($email)) {
            throw new \Exception('String expected for `email`');
        }

        $this->email = $email;
    }

    private function fromString($data) {
        if (strpos($data, '<') === false) {
            return array('name' => null, 'email' => $data);
        }

        list($name, $email) = explode('<', $data);

        $name = trim($name);
        $email = trim($email, " \n\r\t\v\0<>");

        return array('name' => $name, 'email' => $email);
    }
}
