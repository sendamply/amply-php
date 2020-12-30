<?php

namespace Amply\Helpers;

class EmailHelper {
    private $data;
    private $requestData = array();

    public function __construct($data) {
        $this->data = $data;
    }

    public function parsedData() {
        $this->setFrom($this->getKey('from'));
        $this->setSubject($this->getKey('subject'));
        $this->setText($this->getKey('text'));
        $this->setHtml($this->getKey('html'));
        $this->setContent($this->getKey('content'));
        $this->setReplyTo($this->getKey('replyTo'));
        $this->setTemplate($this->getKey('template'));
        $this->setDynamicTemplateData($this->getKey('dynamicTemplateData'));
        $this->setUnsubscribeGroupUuid($this->getKey('unsubscribeGroupUuid'));
        $this->setIpOrPoolUuid($this->getKey('ipOrPoolUuid'));
        $this->setAttachments($this->getKey('attachments'));
        $this->setHeaders($this->getKey('headers'));
        $this->setCategories($this->getKey('categories'));
        $this->setClicktracking($this->getKey('clicktracking'));
        $this->setSubstitutions($this->getKey('substitutions'));

        $personalizations = $this->getKey('personalizations');

        if (isset($personalizations)) {
            $this->setPersonalizations();
        }
        else {
            $to = $this->getKey('to');
            $cc = $this->getKey('cc');
            $bcc = $this->getKey('bcc');

            $this->setPersonalizationsFromTo($to, $cc, $bcc);
        }

        return $this->requestData;
    }

    private function setFrom($from) {
        if (!is_string($from)) {
            throw new \Exception('String expected for `from`');
        }

        $this->requestData['from'] = $this->formatEmails($from)[0];
    }

    private function setSubject($subject) {
        if (!is_string($subject)) {
            throw new \Exception('String expected for `subject`');
        }

        $this->requestData['subject'] = $subject;
    }

    private function setText($text) {
        if (!isset($text)) {
            return;
        }

        $part = array('type' => 'text/plain', 'value' => $text);
        $this->requestData['content'] = $this->requestData['content'] ?? array();
        array_push($this->requestData['content'], $part);
    }

    private function setHtml($html) {
        if (!isset($html)) {
            return;
        }

        $part = array('type' => 'text/html', 'value' => $html);
        $this->requestData['content'] = $this->requestData['content'] ?? array();
        array_push($this->requestData['content'], $part);
    }

    private function setContent($content) {
        if (!isset($content)) {
            return;
        }

        if (!is_array($content) || $this->isAssoc($content)) {
            throw new \Exception('Sequential array expected for `content`');
        }

        $this->requestData['content'] = $this->requestData['content'] ?? array();

        foreach ($content as $i => $part) {
            if (!is_array($part) || !$this->isAssoc($part)) {
                throw new \Exception("Associative array expected for `content[$i]`");
            }

            if (!isset($part['type'])) {
                throw new \Exception("`type` must be defined for `content[$i][type]`");
            }

            if (!isset($part['value'])) {
                throw new \Exception("`value` must be defined for `content[$i][value]`");
            }

            $part = array('type' => $part['type'], 'value' => $part['value']);
            array_push($this->requestData['content'], $part);
        }
    }

    private function setReplyTo($replyTo) {
        if (!isset($replyTo)) {
            return;
        }

        $this->requestData['reply_to'] = $this->formatEmails($replyTo)[0];
    }

    private function setTemplate($template) {
        if (!isset($template)) {
            return;
        }

        $this->requestData['template'] = $template;
    }

    private function setDynamicTemplateData($dynamicTemplateData) {
        if (!isset($dynamicTemplateData)) {
            return;
        }

        if (!is_array($dynamicTemplateData) || !$this->isAssoc($dynamicTemplateData)) {
            throw new \Exception('Associative array expected for `dynamicTemplateData`');
        }

        $this->requestData['substitutions'] = $this->requestData['substitutions'] ?? array();

        foreach ($dynamicTemplateData as $subFrom => $subTo) {
            $this->requestData['substitutions']["\${{$subFrom}}"] = strval($subTo);
        }
    }

    private function setUnsubscribeGroupUuid($unsubscribeGroupUuid) {
        if (!isset($unsubscribeGroupUuid)) {
            return;
        }

        $this->requestData['unsubscribe_group_uuid'] = $unsubscribeGroupUuid;
    }

    private function setIpOrPoolUuid($ipOrPoolUuid) {
        if (!isset($ipOrPoolUuid)) {
            return;
        }

        $this->requestData['ip_or_pool_uuid'] = $ipOrPoolUuid;
    }

    private function setAttachments($attachments) {
        if (!isset($attachments)) {
            return;
        }

        if (!is_array($attachments) || $this->isAssoc($attachments)) {
            throw new \Exception('Sequential array expected for `content`');
        }

        $this->requestData['attachments'] = $this->requestData['attachments'] ?? array();

        foreach ($attachments as $i => $attachment) {
            if (!is_array($attachment) || !$this->isAssoc($attachment)) {
                throw new \Exception("Associative array expected for `attachments[$i]`");
            }

            if (!isset($attachment['content'])) {
                throw new \Exception("`content` must be defined for `attachments[$i][content]`");
            }

            if (!isset($attachment['filename'])) {
                throw new \Exception("`filename` must be defined for `attachments[$i][filename]`");
            }

            $data = array('content' => $attachment['content'], 'filename' => $attachment['filename']);

            if (isset($attachment['type'])) {
                $data['type'] = $attachment['type'];
            }

            if (isset($attachment['disposition'])) {
                $data['disposition'] = $attachment['disposition'];
            }

            if (isset($attachment['contentId'])) {
                $data['content_id'] = $attachment['contentId'];
            }

            array_push($this->requestData['attachments'], $data);
        }
    }

    private function setHeaders($headers) {
        if (!isset($headers)) {
            return;
        }

        if (!is_array($headers) || !$this->isAssoc($headers)) {
            throw new \Exception("Associative array expected for `headers`");
        }

        $this->requestData['headers'] = $this->requestData['headers'] ?? array();

        foreach ($headers as $headerName => $headerValue) {
            $this->requestData['headers'][$headerName] = strval($headerValue);
        }
    }

    private function setCategories($categories) {
        if (!isset($categories)) {
            return;
        }

        if (!is_array($categories) || $this->isAssoc($categories)) {
            throw new \Exception("Sequential array expected for `categories`");
        }

        $this->requestData['analytics'] = $this->requestData['analytics'] ?? array();
        $this->requestData['analytics']['categories'] = $this->requestData['analytics']['categories'] ?? array();

        foreach ($categories as $category) {
            array_push($this->requestData['analytics']['categories'], strval($category));
        }
    }

    private function setClicktracking($clicktracking) {
        if (!isset($clicktracking)) {
            return;
        }

        if (!is_bool($clicktracking)) {
            throw new \Exception('Bool expected for `clicktracking`');
        }

        $this->requestData['analytics'] = $this->requestData['analytics'] ?? array();
        $this->requestData['analytics']['clicktracking'] = $clicktracking;
    }

    private function setSubstitutions($substitutions) {
        if (!isset($substitutions)) {
            return;
        }

        if (!is_array($substitutions) || !$this->isAssoc($substitutions)) {
            throw new \Exception("Associative array expected for `substitutions`");
        }

        $this->requestData['substitutions'] = $this->requestData['substitutions'] ?? array();

        foreach ($substitutions as $subFrom => $subTo) {
            $this->requestData['substitutions'][$subFrom] = strval($subTo);
        }
    }

    private function setPersonalizationsFromTo($to, $cc, $bcc) {
        if (!isset($to) && !isset($cc) && !isset($bcc)) {
            throw new \Exception('Provide at least one of `to`, `cc` or `bcc`');
        }

        $this->requestData['personalizations'] = [array()];

        if (isset($to)) {
            $this->requestData['personalizations'][0]['to'] = $this->formatEmails($to);
        }

        if (isset($cc)) {
            $this->requestData['personalizations'][0]['cc'] = $this->formatEmails($cc);
        }

        if (isset($bcc)) {
            $this->requestData['personalizations'][0]['bcc'] = $this->formatEmails($bcc);
        }
    }

    private function setPersonalizations($personalizations) {
        $this->requestData['personalizations'] = $personalizations;
    }

    private function getKey($key) {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    private function formatEmails($emails) {
        if (is_array($emails) && !$this->isAssoc($emails)) {
            $createEmailAddress = function ($email) {
                $email = new EmailAddress($email);
                return $email->toJson();
            };

            return array_map($createEmailAddress, $emails);
        }

         $email = new EmailAddress($emails);
        return array($email->toJson());
    }

    private function isAssoc($arr) {
        if (array() === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
