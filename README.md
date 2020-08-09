Interkassa API for PHP
======================

This library simplifies working with [interkassa payment system V 2.0](http://interkassa.com).

Basic usage
-----------

First you have to include and initialize the library:

```php
<?php

include_once 'path/to/interkassa.php';
Interkassa::register();
```

This will register the library's autoload with PHP and allow you to use its
classes without any further include statements.

Next, let's create a simple payment:

```php
<?php

// The following parameters are provided by interkassa
$shop_id    = '...';
$secret_key = '...';

// Create a shop
$shop = Interkassa_Shop::factory(array(
    'id'         => $shop_id,
    'secret_key' => $secret_key
));

// Create a payment
$payment_id     = '...'; // Your payment id
$payment_amount = '...'; // The amount to charge your shop's user
$payment_desc   = '...'; // Payment description

$payment = $shop->createPayment(array(
    'id'          => $payment_id,
    'amount'      => $payment_amount,
    'description' => $payment_desc
));
```

We now have everything we need to render the payment form:

```php
<?php

// ... create and configure the payment object

?>
<form action="<?= htmlentities($payment->getFormAction()); ?>" method="post">
    <?php foreach ($payment->getFormValues() as $field => $value): ?>
    <input type="hidden" name="<?= htmlentities($field); ?>" value="<?= htmlentities($value); ?>" />
    <?php endforeach; ?>
    <button type="submit">Submit</button>
</form>
```

Processing payment status requests
----------------------------------

Interkassa can send payment status updates to a URL of your choosing. You can
configure this URL via your account on interkassa or send the URL with other
payment data:

```php
<?php

$payment = $shop->createPayment(array(
    // ... usual payment data
    'status_url' => 'http://example.com/ik-status.php'
));
```

And inside the `ik-status.php`:

```php
<?php

// ... initialize library as usual

$shop = Interkassa_Shop::factory(array(
    'id'         => $shop_id,
    'secret_key' => $secret_key
));

try {
    $status = $shop->receiveStatus($_POST); // POST is used by default
} catch (Interkassa_Exception $e) {
    // The signature was incorrect, send a 400 error to interkassa
    // They should resend payment status request until they receive a 200 status
    header('HTTP/1.0 400 Bad Request');
    exit;
}

$payment = $status->getPayment();
```

This transparently checks the signature of the payment.

The `$status` variable now contains an instance of `Interkassa_Status` class. The
`$payment` variable holds an instance of `Interkassa_Payment` with all the initial
data.

Note, that success and fail status updates are also supported, but do not have
a signature, and are sent via the user's browser, so it is not recommended to
rely on them.

Requirements
------------

This library requires at least PHP 5.1.0 to work correctly. However, the most
recent PHP version is always recommended.

License
-------

This library is released under the Open Source MIT license, which gives you the
possibility to use it and modify it in every circumstance.

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
