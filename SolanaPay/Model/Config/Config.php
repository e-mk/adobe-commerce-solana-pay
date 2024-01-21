<?php

/**
 * @category ArmMage
 * @author Artashes Baghdasaryan <artashes@armmage.com>
 * @copyright Copyright (c) 2024 ArmMage (https://www.armmage.com)
 */

declare(strict_types=1);

namespace  ArmMage\SolanaPay\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config System config data getting class
 */
class Config
{
    /**
     * Admin config paths
     *
     * @var string
     */
    public const XML_PATH_SOLANAPAY_ACTIVE = "payment/solanapay/active";
    public const XML_PATH_SOLANAPAY_TITLE = "payment/solanapay/title";
    public const XML_PATH_SOLANAPAY_MERCHANTADDRESS = "payment/solanapay/merchant_solana_address";
    public const XML_PATH_SOLANAPAY_API_URL = "payment/solanapay/solana_api_url";
    public const XML_PATH_SOLANAPAY_API_KEY = "payment/solanapay/solana_api_key";
    public const XML_PATH_SOLANAPAY_PUBLIC_KEY = "payment/solanapay/solana_public_key";
    public const XML_PATH_SOLANAPAY_PRIVATE_KEY = "payment/solanapay/solana_private_key";
    public const XML_PATH_SOLANAPAY_MODE = "payment/solanapay/mode";
    public const XML_PATH_SOLANAPAY_SPECIFIC_COUNTRY = "payment/solanapay/specificcountry";
    public const XML_PATH_SOLANAPAY_RPCMAINNET = "payment/solanapay/rpcmainnet";
    public const XML_PATH_SOLANAPAY_RPCMAINNETAPIKEY = "payment/solanapay/rpcapikey";
    public const MAINNETURL = "https://api.mainnet-beta.solana.com/";

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get is Active the payment
     *
     * @return bool
     */
    public function getIsActive()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SOLANAPAY_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get payment description
     *
     * @return string
     */
    public function getSolanaMethodTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SOLANAPAY_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get solanapay public key
     *
     * @return string
     */
    public function getSolanaPublicKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SOLANAPAY_PUBLIC_KEY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SOLANAPAY_MODE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get solanapay SpecificCountry
     *
     * @return mixed
     */
    public function getPaymentfromSpecificCountries()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SOLANAPAY_SPECIFIC_COUNTRY, ScopeInterface::SCOPE_STORE);
    }


    /**
     * Get RPC API URL
     *
     * @return string
     */
    public function getRpcClusterUrl()
    {
        $url =  $this->scopeConfig->getValue(self::XML_PATH_SOLANAPAY_RPCMAINNET, ScopeInterface::SCOPE_STORE);
        if ($url) {
            return $url;
        }
        return self::MAINNETURL;
    }

    /**
     * Get RPC APi KEY
     *
     * @return string
     */
    public function getRpcClusterApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SOLANAPAY_RPCMAINNETAPIKEY, ScopeInterface::SCOPE_STORE);
    }
}
