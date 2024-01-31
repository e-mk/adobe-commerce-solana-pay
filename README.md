# Adobe Commerce SolanaPay USDC payment integration

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
 Accept USDC payments on Adobe Commerce (Magento 2) with Solana Pay, an open, free-to-use payments framework built on the Solana blockchain. 

## Installation

    ``armmage/module-solanapay``

###  Installation app directory

 - Unzip or copy module in `app/code/ArmMage`
 - `php bin/magento module:enable ArmMage_SolanaPay`
 - `php bin/magento setup:upgrade`
 - cd [root_magento2]/app/code/ArmMage/SolanaPay/view/frontend/web/js
 - `npm install`
 - `npm run build`
 -  cd [root_magento2]
 - `php bin/magento setup:di:compile`
 - `php bin/magento setup:static-content:deploy` (-f  in development mode )
 - `php bin/magento cache:flush`

### Installation from adobe commerce marketplace | Composer

Link to adobe commerce marketplace https://commercemarketplace.adobe.com/armmage-module-solanapay.html

 -  `composer require armmage/module-solanapay`
 - `php bin/magento module:enable ArmMage_SolanaPay`
 - `php bin/magento setup:upgrade`
 -  cd [root_magento2]/app/code/ArmMage/SolanaPay/view/frontend/web/js
 - `npm install`
 - `npm run build`
 -  cd [root_magento2]
 - `php bin/magento setup:di:compile`
 - `php bin/magento setup:static-content:deploy` (-f  in development mode )
 - `php bin/magento cache:flush`

## Configuration

 - SolanaPay - payment/solanapay/*


## Specifications

 - Payment Method
	- SolanaPay



