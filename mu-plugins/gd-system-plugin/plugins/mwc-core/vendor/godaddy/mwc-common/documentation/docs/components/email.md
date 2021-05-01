---
id: email
title: Email
---

The `Email` component provides a standardized, performant base class for emails.  

## Class properties
| Parameter                	| Type   	| Description                                                     	|
|--------------------------	|--------	|-----------------------------------------------------------------	|
| to                       	| string   	| The recipient's email address                                   	|
| subject                 	| string 	| The email subject                        	                        |
| body                   	| string 	| The email body                            	                    |
| headers                 	| mixed 	| Key-value array of headers                                        |
| contentType               | string  	| The email content type                                         	|

## Getters and Setters

In addition to the given properties, there is a setter and getter provided for each. Those are denoted as follows:

```php
public function setProperty($value) : Email

public function getProperty();
```

### Content Type Header

There is no need to explicitly add a header for the content type. Calling the `setContentType()` method already adds such header.

## Sending Emails

The `Email` class implements the `SendableContract` to ensure the expected functionality is present. To send an email, one should set the email properties and then call the `send()` method.

```php
use GoDaddy\WordPress\MWC\Common\Email\Email;

$email = new Email('johndoe@example.com');

$email->setSubject('This is a test email')
    ->setBody('This is the body of a test email')
    ->setHeaders(['test-key' => 'test value'])
    ->setContentType('text/html')
    ->send();
```
