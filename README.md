# PHP SDK for [CloudEvents](https://github.com/cloudevents/spec)

## Status

This SDK is currently a work in progress, therefore things might (and will) break with every update.

This SDK aims to supports the following versions of CloudEvents:

- [v1.0](https://github.com/cloudevents/spec/blob/v1.0.1/spec.md)

## Installation

Install the SDK using [composer](https://getcomposer.org/):
```sh
composer install cloudevents/php-sdk
```

## Send your first CloudEvent

Note, this is just the desired API at this point and doesn't reflect the functionality of this SDK yet.

```php
use \CloudEvents\Client;
use \CloudEvents\V1\CloudEvent;
use \CloudEvents\Request;

$event = (new CloudEvent())
    // The current implementation requires you to maintain your own id.
    ->setId('1n6bFxDMHZFChlI4TVI9tdzphB9')
    ->setSource('/examples/php-sdk')
    ->setType('com.example.type')
    ->setData(json_encode(['example' => 'first-event']));

(new Client('http://localhost:8080/'))
    ->sendRequest(new Request($event));
```

Note that the `CloudEvents\Client` implements the [PSR-18](https://www.php-fig.org/psr/psr-18/) spec and the `CloudEvents\Request` implements the appropriate [PSR-7 interfaces](https://www.php-fig.org/psr/psr-7/).

## Serialize/Deserialize a CloudEvent

```php
use \CloudEvents\V1\CloudEvent;

$event = (new CloudEvent())
    // The current implementation requires you to maintain your own id.
    ->setId('1n6bFxDMHZFChlI4TVI9tdzphB9')
    ->setSource('/examples/php-sdk')
    ->setType('com.example.type')
    ->setData(json_encode(['example' => 'first-event']));

// via class methods
$serializedEvent = $event->toJson(); // or Event::toJson($event);
$deserializedEvent = Event::fromJson($serializedEvent);

```

## Testing

You can use `composer` to build and run test environments when contributing.

```
$ composer run -l

scripts:
  lint          Show all current linting errors according to PSR12
  lint-fix      Show and fix all current linting errors according to PSR12
  tests         Run all tests locally
  tests-build   Build containers to test against supported PHP versions
  tests-docker  Run tests within supported PHP version containers
```

## Community

- There are bi-weekly calls immediately following the [Serverless/CloudEvents
  call](https://github.com/cloudevents/spec#meeting-time) at
  9am PT (US Pacific). Which means they will typically start at 10am PT, but
  if the other call ends early then the SDK call will start early as well.
  See the [CloudEvents meeting minutes](https://docs.google.com/document/d/1OVF68rpuPK5shIHILK9JOqlZBbfe91RNzQ7u_P7YCDE/edit#)
  to determine which week will have the call.
- Slack: #cloudeventssdk channel under
  [CNCF's Slack workspace](https://slack.cncf.io/).
- Email: https://lists.cncf.io/g/cncf-cloudevents-sdk
- Contact for additional information: Denis Makogon (`@denysmakogon` on slack).

Each SDK may have its own unique processes, tooling and guidelines, common
governance related material can be found in the
[CloudEvents `community`](https://github.com/cloudevents/spec/tree/master/community)
directory. In particular, in there you will find information concerning
how SDK projects are
[managed](https://github.com/cloudevents/spec/blob/master/community/SDK-GOVERNANCE.md),
[guidelines](https://github.com/cloudevents/spec/blob/master/community/SDK-maintainer-guidelines.md)
for how PR reviews and approval, and our
[Code of Conduct](https://github.com/cloudevents/spec/blob/master/community/GOVERNANCE.md#additional-information)
information.
