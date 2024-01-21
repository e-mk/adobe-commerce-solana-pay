<?php

/**
 * @category ArmMage
 * @author Artashes Baghdasaryan <artashes@armmage.com>
 * @copyright Copyright (c) 2024 ArmMage (https://www.armmage.com)
 */

declare(strict_types=1);

namespace ArmMage\SolanaPay\Model\Order;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction as MagentoTransaction;
use Magento\Sales\Model\Order\Payment\Transaction\Builder;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;

class Transaction
{
    public const ORDER_ID = 'OrderId';
    public const TRANSACTION_ID = 'TransactionID';
    public const STATUS = 'Status';
    public const DATE = 'Date';
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Builder
     */
    protected $transactionBuilder;

    /**
     * @var TimezoneInterface
     */
    protected $date;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param TimezoneInterface $date
     * @param Order $order
     * @param Builder $transactionBuilder
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        TimezoneInterface $date,
        Order $order,
        Builder $transactionBuilder,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->date = $date;
        $this->order = $order;
        $this->transactionBuilder = $transactionBuilder;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Create transaction
     *
     * @param mixed[] $paymentData
     * @param int $shouldCloseParentTransaction
     * @param int $isTransactionClosed
     *
     * @return OrderInterface $order
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createTransaction(
        $paymentData,
        $shouldCloseParentTransaction = 0,
        $isTransactionClosed = 0
    ) {
        $transactionId = $paymentData[self::TRANSACTION_ID];

        $order = $this->getOrderById($paymentData[self::ORDER_ID]);

        $status = $order->getStatus() == 'canceled' ?  __('Payment failed') : $status = __('Payment confirmed');

        $payment = $order->getPayment();

        $paymentData[self::STATUS] = $status;
        $paymentData[self::DATE] =  $this->date->date()->format('Y/m/d');
        $payment->setLastTransId($transactionId);
        $payment->setTransactionId($transactionId);
        $payment->setIsTransactionClosed($isTransactionClosed);
        $payment->setShouldCloseParentTransaction($shouldCloseParentTransaction);

        $payment->setAdditionalInformation(
            [MagentoTransaction::RAW_DETAILS => (array)$paymentData]
        );

        $payment->setTransactionAdditionalInfo(self::ORDER_ID, $paymentData[self::ORDER_ID]);
        $payment->setTransactionAdditionalInfo(self::TRANSACTION_ID, $paymentData[self::TRANSACTION_ID]);
        $payment->setTransactionAdditionalInfo('signature', $paymentData['signature']);
        $payment->setTransactionAdditionalInfo(self::STATUS, $status);
        $payment->setTransactionAdditionalInfo(self::DATE, $this->date->date()->format('Y/m/d'));

        $formattedPrice = $order->getBaseCurrency()->formatTxt(
            $order->getBaseGrandTotal()
        );

        $message = __('Captured amount is %1.', $formattedPrice);

        $trans = $this->transactionBuilder;
        $transaction = $trans->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($transactionId)
            ->setAdditionalInformation(
                [MagentoTransaction::RAW_DETAILS => (array)$paymentData]
            )
            ->setFailSafe(false)
            ->build(MagentoTransaction::TYPE_CAPTURE);

        $payment->addTransactionCommentsToOrder(
            $transaction,
            $message
        );

        $payment->save();
        $order->setStatus('processing')->setState('paid');
        $order->save();
        $transaction->save();

        return $order;
    }

    /**
     * Get order by id
     *
     * @param int $id
     *
     * @return OrderInterface $order
     */
    public function getOrderById($id)
    {
        return $this->orderRepository->get($id);
    }
}
