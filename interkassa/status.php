<?php
/**
 * Interkassa API for PHP
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT-style license
 * @package Interkassa
 * @author Anton Suprun <kpobococ@gmail.com>
 * @version 1.0.0
 */

/**
 * Interkassa payment status class
 *
 * This class represents payment status. It contains additional data sent from
 * interkassa and automatically checks data signature. Note, that only status
 * url updates contain signature, so it is not recommended to rely on succes url
 * or fail url statuses to confirm user payment.
 *
 * @license MIT-style license
 * @package Interkassa
 * @author Anton Suprun <kpobococ@gmail.com>
 * @version 1.0.0
 */
class Interkassa_Status
{
    protected $_verified = false;

    protected $_timestamp;
    protected $_state;
    protected $_trans_id;
    protected $_currency_rate;
    protected $_fees_payer;

    protected $_shop;
    protected $_payment;

    /**
     * Create payment status instance
     *
     * @param Interkassa_Shop $shop
     * @param array $source
     *
     * @see Interkassa_Status::__constructor()
     *
     * @return Interkassa_Status
     */
    public static function factory(Interkassa_Shop $shop, array $source)
    {
        return new Interkassa_Status($shop, $source);
    }

    /**
     * Constructor
     *
     * @param Interkassa_Shop $shop
     * @param array $source the data source to use, e.g. $_POST.
     *
     * @throws Interkassa_Exception if received shop id does not match current
     *                              shop id or received signature is invalid
     */
    public function __construct(Interkassa_Shop $shop, array $source)
    {
        $this->_shop = $shop;

        $received_id = strtoupper($source['ik_shop_id']);
        $shop_id     = strtoupper($shop->getId());

        if ($received_id !== $shop_id) {
            throw new Interkassa_Exception('Received shop id does not match current shop id');
        }

        if (isset($source['ik_sign_hash']))
        {
            if (!$this->_checkSignature($source)) {
                throw new Interkassa_Exception('Signature does not match the data');
            }

            $this->_verified = true;
        }

        $payment = $shop->createPayment(array(
            'id'          => $source['ik_payment_id'],
            'amount'      => $source['ik_payment_amount'],
            'description' => $source['ik_payment_desc']
        ));

        if ($source['ik_paysystem_alias']) {
            $payment->setPaysystemAlias($source['ik_paysystem_alias']);
        }

        if ($source['ik_baggage_fields']) {
            $payment->setBaggage($source['ik_baggage_fields']);
        }

        $this->_payment = $payment;

        $this->_timestamp     = (int) $source['ik_payment_timestamp'];
        $this->_state         = (string) $source['ik_payment_state'];
        $this->_trans_id      = (string) $source['ik_trans_id'];
        $this->_currency_rate = (float) $source['ik_currency_exch'];
        $this->_fees_payer    = (int) $source['ik_fees_payer'];
    }

    /**
     * Get transaction time as a timestamp
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->_timestamp;
    }

    /**
     * Get transaction time as a DateTime instance
     *
     * @see http://php.net/http://ua2.php.net/manual/en/class.datetime.php
     *
     * @return DateTime
     */
    public function getDateTime()
    {
        return new DateTime('@' . $this->getTimestamp());
    }

    /**
     * Get transaction state
     *
     * Returns {@link Interkassa::STATE_SUCCESS} or {@link Interkassa::STATE_FAIL}
     *
     * @return string
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     * Get transaction id
     *
     * This id is provided by interkassa
     *
     * @return string
     */
    public function getTransId()
    {
        return $this->_trans_id;
    }

    /**
     * Get currency exchange rate
     *
     * Returns the currency exchange rate defined in shop preferences at the
     * time of the transaction
     *
     * @return float
     */
    public function getCurrencyRate()
    {
        return $this->_currency_rate;
    }

    /**
     * Get currency exchage rate as string
     *
     * Returns the currency exchange rate defined in shop preferences at the
     * time of the transaction, formatted as a string
     *
     * @param int $decimals number of decimal points
     *
     * @return string
     */
    public function getCurrencyRateAsString($decimals = 2)
    {
        return number_format($this->_currency_rate, $decimals, '.', '');
    }

    /**
     * Get transaction fees payer
     *
     * Returns {@link Interkassa::FEES_PAYER_SHOP}, {@link Interkassa::FEES_PAYER_BUYER}
     * or {@link Interkassa::FEES_PAYER_EQUAL}
     *
     * @return type
     */
    public function getFeesPayer()
    {
        return $this->_fees_payer;
    }

    /**
     * Get verification status
     *
     * Returns true if the status update signature was present and correctly
     * verified. Returns false if the status update had no signature present.
     *
     * Note, if the status update contained a signature but the data was not
     * correctly verified, the constructor throws an error.
     *
     * @return bool
     */
    public function getVerified()
    {
        return $this->_verified;
    }

    /**
     * Get payment instance
     *
     * @return Interkassa_Payment
     */
    public function getPayment()
    {
        return $this->_payment;
    }

    /**
     * Get shop instance
     *
     * @return Interkassa_Shop
     */
    public function getShop()
    {
        return $this->_shop;
    }

    /**
     * Check status data signature
     *
     * @param array $source the data source
     *
     * @return bool
     */
    final protected function _checkSignature($source)
    {
        $signature = strtoupper(md5(implode(':', array(
            $source['ik_shop_id'],
            $source['ik_payment_amount'],
            $source['ik_payment_id'],
            $source['ik_paysystem_alias'],
            $source['ik_baggage_fields'],
            $source['ik_payment_state'],
            $source['ik_trans_id'],
            $source['ik_currency_exch'],
            $source['ik_fees_payer'],
            $this->getShop()->getSecretKey()
        ))));

        return $source['ik_sign_hash'] === $signature;
    }
}