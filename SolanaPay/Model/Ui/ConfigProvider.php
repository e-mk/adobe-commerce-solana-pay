<?php


/**
 * @category ArmMage
 * @author Artashes Baghdasaryan <artashes@armmage.com>
 * @copyright Copyright (c) 2024 ArmMage (https://www.armmage.com)
 */

declare(strict_types=1);


declare(strict_types=1);

namespace ArmMage\SolanaPay\Model\Ui;

use ArmMage\SolanaPay\Model\Config\Config;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Payment method code
     */
    const CODE = 'solanapay';

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var Config
     */
    private $config;

    /**
     * ConfigProvider constructor
     *
     * @param Repository $assetRepo
     * @param Config $config
     */
    public function __construct(
        Repository $assetRepo,
        Config $config
    ) {
        $this->assetRepo = $assetRepo;
        $this->config = $config;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {

        return [
            'payment' => [
                self::CODE => [
                    'image' => $this->assetRepo->getUrl("ArmMage_SolanaPay::images/solana-pay.svg"),
                    'public_key' => $this->config->getSolanaPublicKey(),
                    'mode' => $this->config->getMode(),
                    'rpcapiURL' => $this->config->getRpcClusterUrl(),
                    'rpcapiKEY' => $this->config->getRpcClusterApiKey(),
                ]
            ]
        ];
    }
}
