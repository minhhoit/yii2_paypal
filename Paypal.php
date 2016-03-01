<?php
namespace nlsoft\yii2_paypal;

use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\base\Component;

use PayPal\Api\Address;
use PayPal\Api\CreditCard;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\FundingInstrument;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\RedirectUrls;
use PayPal\Rest\ApiContext;

/**
 * Component excecute class paypal
 *
 * @author minh.ho
 * @since 1.0
 * @package nlsoft
*/
class Paypal extends Component
{
    #Define enviroment for paypal
    const MODE_SANDBOX  = 'sandbox';
    const MODE_LIVE     = 'live';
    const IS_LIVE       = false;

    #Define log level
    const LOG_LEVEL_INFO  = 'INFO';
    const LOG_LEVEL_WARN  = 'WARN';
    const LOG_LEVEL_ERROR = 'ERROR';

    #Perameter connection
    const PAYPAL_TIMEOUT  = 30;
    const PAYPAL_RETRY    = 1;

    #Region api settings
    public $client_id;
    public $client_secret;
    public $currency = 'USD';
    public $config = [];

    /**
     * @var $_apiContext
    */
    private $_apiContext = null;

    /**
     * Constructure for class paypal
    */
    public function init()
    {
        $this->setConfig();
    }

    /**
     * Configure enviroment for paypal
     * @inheritdoc
     * @return void
    */
    private function setConfig()
    {
        #User ApiContext object to authenticate API calls. The client_id and
        #client_secret for the OAuthTokenCredential class can be retrived from
        #Developer paypal.com
        $this->_apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->client_id;
                $this->client_secret
            )
        );

        #SDK configuration
        $this->_apiContext->setConfig(ArrayHelper::merge([
            'mode'                   => self::MODE_SANDBOX
            'http.ConnectionTimeOut' => self::PAYPAL_TIMEOUT,
            'http.Retry'             => self::PAYPAL_RETRY,
            'log.LogEnabled'         => YII_DEBUG ? 1 : 0,
            'log.FileName'           => Yii::getAlias('@runtime/logs/paypal.log'),
            'log.LogLevel'           => self::LOG_LEVEL_INFO,
            'validation.level'       => 'log',
            'cache.enabled'          => 'true'
        ], $this->config));

        #Set file name of the log if present
        if (isset($this->config['log.FileName'])
            && isset($this->config['log.LogEnabled'])
            && ((bool)$this->config['log.LogEnabled'] === true)
        ) {
            $logFileName = \Yii::getAlias($this->config['log.FileName']);

            if ($logFileName) {
                if (!file_exists($logFileName)) {
                    if (!touch($logFileName)) {
                        throw new ErrorException('Can\'t create paypal.log file at: ' . $logFileName);
                    }
                }
            }

            $this->config['log.FileName'] = $logFileName;
        }
    }

    /**
     * Set client id value
     * @param string clientId
    */
    protected function setClientId($key = null)
    {
        if ($key !== null) {
            $this->client_id = $key;
        }
    }

    /**
     * Set client secret value
     * @param string clientSecret
    */
    protected function setClientSecret($key = null)
    {
        if ($key !== null) {
            $this->client_secret = $key;
        }
    }
}
