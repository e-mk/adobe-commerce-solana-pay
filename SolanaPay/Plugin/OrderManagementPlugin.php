<?php

/**
 * @category ArmMage
 * @author Artashes Baghdasaryan <artashes@armmage.com>
 * @copyright Copyright (c) 2024 ArmMage (https://www.armmage.com)
 */

declare(strict_types=1);

namespace ArmMage\SolanaPay\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use ArmMage\SolanaPay\Model\Order\Transaction;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OrderManagement
 */
class OrderManagementPlugin
{
    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * OrderManagement constructor.
     *
     * @param CookieManagerInterface $cookieManager
     * @param Transaction $transaction
     * @param LoggerInterface $logger
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        Transaction $transaction,
        LoggerInterface $logger,
    ) {
        $this->cookieManager = $cookieManager;
        $this->transaction = $transaction;
        $this->logger = $logger;
    }

    /**
     * After Place Order plugin to create
     *
     * @param OrderManagementInterface $subject
     * @param OrderInterface           $result
     *
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPlace(
        OrderManagementInterface $subject,
        OrderInterface $result
    ) {
        $orderId = $result->getEntityId();
        $payment = $result->getPayment();
        $method = $payment->getMethod();
        try {
            if ($method == 'solanapay' && $orderId) {
                $signature = $this->cookieManager->getCookie("solana_input_signature");
                $publicKey = $this->cookieManager->getCookie("solana_input_from_wallet_public_key");

                $transactionData = [
                    'signature' => $signature,
                    Transaction::TRANSACTION_ID => $publicKey,
                    Transaction::ORDER_ID => $orderId,
                ];

                $this->transaction->createTransaction($transactionData);
            }
        } catch (\Exception $e) {

            $this->logger->error($e->getMessage());
        }

        $this->cookieManager->deleteCookie("solana_input_signature");
        $this->cookieManager->deleteCookie("solana_input_from_wallet_public_key");
        return $result;
    }
}
