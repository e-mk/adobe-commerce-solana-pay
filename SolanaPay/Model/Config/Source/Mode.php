<?php

/**
 * @category ArmMage
 * @author Artashes Baghdasaryan <artashes@armmage.com>
 * @copyright Copyright (c) 2024 ArmMage (https://www.armmage.com)
 */

declare(strict_types=1);

namespace ArmMage\SolanaPay\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Mode is returning  array of options network
 */
class Mode implements OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'devnet', 'label' => __('Dev Net')],
            ['value' => 'testnet', 'label' => __('Test Net')],
            ['value' => 'mainnet-beta', 'label' => __('Main Net')],
        ];
    }
}
