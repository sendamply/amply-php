# Amply

This is the Amply PHP SDK that integrates with the [v1 API](https://docs.sendamply.com/docs/api/docs/Introduction.md).

__Table of Contents__

- [Install](#install)
- [Quick Start](#quick-start)
- [Methods](#methods)
	- [email](#email)

## Install

### Prerequisites
- PHP 7.0+
- Amply account, [sign up here.](https://sendamply.com/plans)

### Access Token

Obtain your access token from the [Amply UI.](https://sendamply.com/home/settings/access_tokens)

### Install Package
Add Amply to your composer.json file. If you are not using Composer, we highly recommend it. It's an excellent way to manage dependencies in your PHP application.
```
{
  "require": {
    "amply/amply-php": "*"
  }
}
```

## Alternative: Install from repo
If you are not using Composer, simply download and checkout the version you want to use from the Github repository.

```
git clone https://github.com/sendamply/amply-php.git
git checkout VERSION
```

### Domain Verification
Add domains you want to send `from` via the [Verified Domains](https://sendamply.com/home/settings/verified_domains) tab on your dashboard.

Any emails you attempt to send from an unverified domain will be rejected.  Once verified, Amply immediately starts warming up your domain and IP reputation.  This warmup process will take approximately one week before maximal deliverability has been reached.

## Quick Start
The following is the minimum needed code to send a simple email. Use this example, and modify the `to` and `from` variables:

```php
// Uncomment the next line if you're using a dependency loader (such as Composer) (recommended)
// require 'vendor/autoload.php';

// Uncomment the next line if you're not using a dependency loader (such as Composer), replacing <PATH TO> with the path to the amply-php.php file
// require_once '<PATH TO>/amply-php.php';

$amply = new Amply(getenv('AMPLY_ACCESS_TOKEN'));

try {
    $amply->email->create(array(
        'to' => 'test@example.com',
        'from' => 'test@verifieddomain.com',
        'subject' => 'My first Amply email!',
        'text' => 'This is easy',
        'html' => '<strong>and fun :)</strong>'
    ));
}
catch (\Amply\Exceptions\APIException $e) {
    echo "Generic API error\n";
    echo "$e->code\n";
    echo "$e->text\n";
}
catch (\Amply\Exceptions\ValidationException $e) {
    echo "Invalid input\n";
    print_r($e->errors);
}
catch (\Amply\Exceptions\ResourceNotFoundException $e) {
    echo "Missing resource\n";
    print_r($e->errors);
}
```

Once you execute this code, you should have an email in the inbox of the recipient.  You can check the status of your email in the UI from the [Search](https://sendamply.com/home/analytics/searches/basic/new), [SQL](https://sendamply.com/home/analytics/searches/sql/new), or [Users](https://sendamply.com/home/analytics/users) page.

## Methods

### email

Parameter(s)         | Description
:---------------- | :---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
to, cc, bcc | Email address of the recipient(s).  This may be a string `"Test <test@example.com>"`, an associative array `array('name' => 'Bill', 'email' => 'test@test.com')`, or a sequential array of strings/associative arrays.
personalizations | For fine tuned access, you may override the to, cc, and bcc keys and use advanced personalizations.  See the API guide [here](https://docs.sendamply.com/docs/api/Mail-Send.v1.yaml/paths/~1email/post).
from | Email address of the sender.  This may be formatted as a string or associative array with name and email keys.  An array of senders is not allowed.
subject | Subject of the message.
html | HTML portion of the message.
text | Text portion of the message.
content | A sequential array of associative arrays containing the following fields: `type` (required), `value` (required).
template | The template to use. This may be a string (the UUID of the template), a sequential array of UUID strings (useful for A/B/... testing where one is randomly selected), or an associative array of the format `array('template1Uuid' => 0.25, 'template2Uuid' => 0.75)` (useful for weighted A/B/... testing).
dynamicTemplateData | The dynamic data to be replaced in your template.  This is an associative array of the format `array('variable1' => 'replacement1', ...)`. Variables should be defined in your template body as `${variable1}`.
replyTo |Email address of who should receive replies.  This may be a string or an associative array with `name` (optional) and `email` fields.
headers | An associative array where the header name is the key and header value is the value.
ipOrPoolUuid | The UUID string of the IP address or IP pool you want to send from.  Default is your Global pool.
unsubscribeGroupUuid | The UUID string of the unsubscribe group you want to associate with this email.
attachments[] | An array of attachments containting an associative array with fields `content`, `filename`, `type`, `disposition`, and`contentId`.
ttachments[][content] | A base64 encoded string of your attachment's content (required, string).
attachments[][type] | The MIME type of your attachment (optional, string).
attachments[][filename] | The filename of your attachment (required, string).
attachments[][disposition] | The disposition of your attachment (`inline` or `attachment`) (optional, string).
attachments[][contentId] | The content ID of your attachment (optional, string).
clicktracking | Enable or disable clicktracking (bool).
categories | A sequential array of email category strings you can associate with your message.
substitutions | An associative array of the format `array('subFrom' => 'subTo', ...}` of substitutions.

__Example__

```php
$amply.email.create(array(
    'to' => "example@test.com",
    'cc' => array('name' => 'Billy', 'email' => 'Smith'),
    'from' => 'From <example@verifieddomain.com>',
    'text' => 'Text part',
    'html' => 'HTML part',
    'content' => array(
        array(
            'type' => 'text/testing',
            'value' => 'Test!',
        ),
    ),
    'subject' => 'A new email!',
    'replyTo' => 'Reply To <test@example.com>',
    'template' => 'faecb75b-371e-4062-89d5-372b8ff0effd',
    'dynamicTemplateData' => array('name' => 'Jimmy'),
    'unsubscribeGroupUuid' => '5ac48b43-6e7e-4c51-817d-f81ea0a09816',
    'ipOrPoolUuid' => '2e378fc9-3e23-4853-bccb-2990fda83ca9',
    'attachments' => array(
        array(
            'content' => 'dGVzdA==',
            'filename' => 'test.txt',
        ),
    ),
    'headers' => array('X-Testing' => 'Test'),
    'categories' => array('Test'),
    'clicktracking' => true,
    'substitutions' => array('sub1' => 'replacement1')
));
```
