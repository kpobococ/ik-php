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
 * Interkassa payment class
 *
 * This class represents a payment. It can be used to acquire an array of all
 * the form field values with the correct field names (see
 * {@link Interkassa_Payment::getFormValues() getFormValues() method}.
 *
 * @license MIT-style license
 * @package Interkassa
 * @author Anton Suprun <kpobococ@gmail.com>
 * @version 1.0.0
 */
class Interkassa_Payment
{
    /**
     * Shop instance
     *
     * @var Interkassa_Shop
     */
    protected $_shop;

    /**
     * Payment id
     *
     * @var string
     */
    protected $_id;

    /**
     * Payment amount
     *
     * @var float
     */
    protected $_amount;

    /**
     * Payment description
     *
     * @var string
     */
    protected $_description;

    /**
     * Paysystem alias
     *
     * @var string|bool
     */
    protected $_paysystem_alias = false;

    /**
     * Payment baggage field
     *
     * @var string
     */
    protected $_baggage = false;

    /**
     * Success url
     *
     * @var string
     */
    protected $_success_url = false;

    /**
     * Failure url
     *
     * @var string
     */
    protected $_fail_url = false;

    /**
     * Status url
     *
     * @var string
     */
    protected $_status_url = false;

    /**
     * Success url method
     *
     * @var string
     */
    protected $_success_method = Interkassa::METHOD_POST;

    /**
     * Failure url method
     *
     * @var string
     */
    protected $_fail_method = Interkassa::METHOD_POST;

    /**
     * Status url method
     *
     * @var string
     */
    protected $_status_method = Interkassa::METHOD_POST;

    /**
     * Payment form action
     *
     * @var string
     */
    protected $_form_action = 'http://www.interkassa.com/lib/payment.php';

    /**
     * Create payment instance
     *
     * @param Interkassa_Shop $interkassa
     *
     * @see Interkassa_Payment::__construct()
     *
     * @return Interkassa_Payment
     */
    public static function factory(Interkassa_Shop $shop, array $options)
    {
        return new Interkassa_Payment($shop, $options);
    }

    /**
     * Constructor
     *
     * Accepted payment options are:
     * - id - payment id
     * - amount - payment amount
     * - description - payment description
     * - paysystem_alias - payment system alias. Optional
     * - baggage - payment baggage field. Optional
     * - success_url - url to redirect the user in case of success. Optional
     * - fail_url - url to redirect the user in case of failure. Optional
     * - status_url - url to send payment status. Optional
     * - success_method - method to use when redirecting to success_url. Optional
     * - fail_method - method to use when redirecting to fail_url. Optional
     * - status_method - method to use when sending payment status. Optional
     * - form_action - payment form action url. Optional
     *
     * @param Interkassa_Shop $shop
     * @param array $options an array of payment options
     *
     * @throws Interkassa_Exception if any required options are missing
     */
    public function __construct(Interkassa_Shop $shop, array $options)
    {
        $this->_shop = $shop;

        if (!isset($options['id'])) {
            throw new Interkassa_Exception('Payment id is required');
        }

        if (!isset($options['amount'])) {
            throw new Interkassa_Exception('Payment amount is required');
        }

        if (!isset($options['description'])) {
            throw new Interkassa_Exception('Payment description is required');
        }

        $this->_id          = (string) $options['id'];
        $this->_amount      = (float)  $options['amount'];
        $this->_description = (string) $options['description'];

        if (!empty($options['paysystem_alias'])) {
            $this->setPaysystemAlias($options['paysystem_alias']);
        }

        if (!empty($options['baggage'])) {
            $this->setBaggage($options['baggage']);
        }

        if (!empty($options['success_url'])) {
            $this->setSuccessUrl($options['success_url']);
        }

        if (!empty($options['success_method'])) {
            $this->setSuccessMethod($options['success_method']);
        }

        if (!empty($options['fail_url'])) {
            $this->setFailUrl($options['fail_url']);
        }

        if (!empty($options['fail_method'])) {
            $this->setFailMethod($options['fail_method']);
        }

        if (!empty($options['status_url'])) {
            $this->setStatusUrl($options['status_url']);
        }

        if (!empty($options['status_method'])) {
            $this->setStatusMethod($options['status_method']);
        }

        if (!empty($options['form_action'])) {
            $this->setFormAction($options['form_action']);
        }
    }

    /**
     * Get payment id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get payment amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * Get payment amount as string
     *
     * @param int $decimals number of decimal points
     *
     * @return string
     */
    public function getAmountAsString($decimals = 2)
    {
        return number_format($this->_amount, $decimals, '.', '');
    }

    /**
     * Get payment description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Get payment system alias
     *
     * @return string
     */
    public function getPaysystemAlias()
    {
        return $this->_paysystem_alias;
    }

    /**
     * Set payment system alias
     *
     * See interkassa tech doc for a list of accepted values
     *
     * @param string $paysystem_alias
     *
     * @return Interkassa_Payment self
     */
    public function setPaysystemAlias($paysystem_alias)
    {
        if (!empty($paysystem_alias)) {
            $this->_paysystem_alias = (string) $paysystem_alias;
        }

        return $this;
    }

    /**
     * Get payment baggage field
     *
     * @return string
     */
    public function getBaggage()
    {
        return $this->_baggage;
    }

    /**
     * Set payment baggage field
     *
     * @param string $baggage
     *
     * @return Interkassa_Payment self
     */
    public function setBaggage($baggage)
    {
        if (!empty($baggage)) {
            $this->_baggage = (string) $baggage;
        }

        return $this;
    }

    /**
     * Get success url
     *
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->_success_url;
    }

    /**
     * Set success url
     *
     * @param string $url
     *
     * @return Interkassa_Payment self
     */
    public function setSuccessUrl($url)
    {
        if (!empty($url)) {
            $this->_success_url = (string) $url;
        }

        return $this;
    }

    /**
     * Get success url method
     *
     * Returns {@link Interkassa::METHOD_POST}, {@link Interkassa::METHOD_GET}
     * or {@link Interkassa::METHOD_LINK}
     *
     * @return string
     */
    public function getSuccessMethod()
    {
        return $this->_success_method;
    }

    /**
     * Set success url method
     *
     * @param string $method
     *
     * @uses Interkassa::METHOD_POST
     * @uses Interkassa::METHOD_GET
     * @uses Interkassa::METHOD_LINK
     *
     * @return Interkassa_Payment self
     */
    public function setSuccessMethod($method)
    {
        if (empty($method)) {
            return $this;
        }

        $methods = array(
            Interkassa::METHOD_POST,
            Interkassa::METHOD_GET,
            Interkassa::METHOD_LINK
        );

        if (in_array($method, $methods)) {
            $this->_success_method = $method;
        }

        return $this;
    }

    /**
     * Get failure url
     *
     * @return string
     */
    public function getFailUrl()
    {
        return $this->_fail_url;
    }

    /**
     * Set failure url
     *
     * @param string $url
     *
     * @return Interkassa_Payment self
     */
    public function setFailUrl($url)
    {
        if (!empty($url)) {
            $this->_fail_url = (string) $url;
        }

        return $this;
    }

    /**
     * Get failure url method
     *
     * Returns {@link Interkassa::METHOD_POST}, {@link Interkassa::METHOD_GET}
     * or {@link Interkassa::METHOD_LINK}
     *
     * @return string
     */
    public function getFailMethod()
    {
        return $this->_fail_method;
    }

    /**
     * Set failure url method
     *
     * @param string $method
     *
     * @uses Interkassa::METHOD_POST
     * @uses Interkassa::METHOD_GET
     * @uses Interkassa::METHOD_LINK
     *
     * @return Interkassa_Payment self
     */
    public function setFailMethod($method)
    {
        if (empty($method)) {
            return $this;
        }

        $methods = array(
            Interkassa::METHOD_POST,
            Interkassa::METHOD_GET,
            Interkassa::METHOD_LINK
        );

        if (in_array($method, $methods)) {
            $this->_fail_method = $method;
        }

        return $this;
    }

    /**
     * Get status url
     *
     * @return string
     */
    public function getStatusUrl()
    {
        return $this->_status_url;
    }

    /**
     * Set status url
     *
     * @param string $url
     *
     * @return Interkassa_Payment self
     */
    public function setStatusUrl($url)
    {
        if (!empty($url)) {
            $this->_status_url = (string) $url;
        }

        return $this;
    }

    /**
     * Get status url method
     *
     * Returns {@link Interkassa::METHOD_POST}, {@link Interkassa::METHOD_GET}
     * or {@link Interkassa::METHOD_OFF}
     *
     * @return string
     */
    public function getStatusMethod()
    {
        return $this->_status_method;
    }

    /**
     * Set status url method
     *
     * @param string $method
     *
     * @uses Interkassa::METHOD_POST
     * @uses Interkassa::METHOD_GET
     * @uses Interkassa::METHOD_OFF
     *
     * @return Interkassa_Payment self
     */
    public function setStatusMethod($method)
    {
        if (empty($method)) {
            return $this;
        }

        $methods = array(
            Interkassa::METHOD_POST,
            Interkassa::METHOD_GET,
            Interkassa::METHOD_OFF
        );

        if (in_array($method, $methods)) {
            $this->_status_method = $method;
        }

        return $this;
    }

    /**
     * Get payment form field values
     *
     * Returns an associative array of the payment form field names as array
     * keys, and their respective values as array values
     *
     * @uses Interkassa_Payment::getAmountAsString() to form payment amount value
     *
     * @return array
     */
    public function getFormValues()
    {
        $return = array(
            'ik_shop_id'         => $this->getShop()->getId(),
            'ik_payment_amount'  => $this->getAmountAsString(),
            'ik_payment_id'      => $this->getId(),
            'ik_payment_desc'    => $this->getDescription(),
            'ik_paysystem_alias' => ''
        );

        $alias       = $this->getPaysystemAlias();
        $baggage     = $this->getBaggage();
        $success_url = $this->getSuccessUrl();
        $fail_url    = $this->getFailUrl();
        $status_url  = $this->getStatusUrl();

        if (!empty($alias)) {
            $return['ik_paysystem_alias'] = (string) $alias;
        }

        if (!empty($baggage)) {
            $return['ik_baggage_fields'] = (string) $baggage;
        }

        if (!empty($success_url)) {
            $return['ik_success_url']    = (string) $success_url;
            $return['ik_success_method'] = (string) $this->getSuccessMethod();
        }

        if (!empty($fail_url)) {
            $return['ik_fail_url']    = (string) $fail_url;
            $return['ik_fail_method'] = (string) $this->getFailMethod();
        }

        if (!empty($status_url)) {
            $return['ik_status_url']    = (string) $status_url;
            $return['ik_status_method'] = (string) $this->getStatusMethod();
        }

        return $return;
    }

    /**
     * Get payment form action
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->_form_action;
    }

    /**
     * Set payment form action
     *
     * @param string $url
     *
     * @return Interkassa_Payment self
     */
    public function setFormAction($url)
    {
        if (!empty($url)) {
            $this->_form_action = (string) $url;
        }

        return $this;
    }

    /**
     * Get shop instance for this payment
     *
     * @return Interkassa_Shop
     */
    public function getShop()
    {
        return $this->_shop;
    }
}