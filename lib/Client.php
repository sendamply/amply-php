<?php
/**
 * This helper builds the request body for a /mail/send API call
 */

namespace Amply;

/**
 * This class is used to construct a request body for the /mail/send API call
 */
class Client {
    const DEFAULT_HEADERS = array(
        'Accept: application/json',
        'Content-Type: application/json'
    );

    private $accessToken;
    private $url = 'https://sendamply.com/api/v1';

    public function __construct($accessToken) {
        $this->accessToken = $accessToken;
    }

    public function post($path, $body, $options = []) {
        $headers = array_merge(
            self::DEFAULT_HEADERS,
            $options['headers'] ?? array(),
            $this->authorizationHeader()
        );

        $payload = json_encode($body);

        $ch = curl_init("$this->url$path");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        curl_close($ch);


        return $this->checkResponse($code, $result);
    }

    private function checkResponse($code, $body) {
        if ($code == 204) {
            return;
        }
        else if ($code == 401 || $code == 403) {
            throw new \Amply\Exceptions\APIException($code, $body);
        }
        else if ($code == 404) {
            $json = json_decode($body, true);
            throw new \Amply\Exceptions\ResourceNotFoundException($json['errors']);
        }
        else if ($code == 422) {
            $json = json_decode($body, true);
            throw new \Amply\Exceptions\ValidationException($json['errors']);
        }
        else if ($code < 200 || $code >= 300) {
            throw new \Amply\Exceptions\APIException($code, $body);
        }

        return json_decode($body, true);
    }

    private function authorizationHeader() {
        return array("Authorization: Bearer $this->accessToken");
    }
}
