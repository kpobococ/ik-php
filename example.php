<?php
include 'interkassa.php';
Interkassa::register();

$shop_id    = '52e0105ebf4efc070d704c1d';
$secret_key = 'rVCrCFMt3Tdz2kzT';

// Create a shop
$shop = Interkassa_Shop::factory(array(
    'id'         => $shop_id,
    'secret_key' => $secret_key
));

if (count($_POST)):
    try {
        $status = $shop->receiveStatus($_POST); // POST is used by default
    } catch (Interkassa_Exception $e) {
        // The signature was incorrect, send a 400 error to interkassa
        // They should resend payment status request until they receive a 200 status
        header('HTTP/1.0 400 Bad Request');
        exit;
    }

    $payment = $status->getPayment();

else:
    // Create a payment
    $payment_id     = '1';     // Your payment id
    $payment_amount = '12.52'; // The amount to charge your shop's user
    $payment_desc    = 'Test'; // Payment description

    $payment = $shop->createPayment(array(
        'id'          => $payment_id,
        'amount'      => $payment_amount,
        'description' => $payment_desc,
        'locale'      => 'en',
        'currency'    => 'USD'
    ));
    $payment->setBaggage('test_baggage');

    ?>

    <form action="<?php echo htmlentities($payment->getFormAction()); ?>" method="post">
        <?php foreach ($payment->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?php echo htmlentities($field); ?>" value="<?php echo htmlentities($value); ?>"/>
        <?php endforeach; ?>
        <button type="submit">Submit</button>
    </form>

<?php endif;
