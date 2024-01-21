define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'ArmMage_SolanaPay/js/solana/src/processPayment',
        'Magento_Checkout/js/model/totals',
        'mage/url',
        'Magento_Checkout/js/model/quote',
        'Magento_Ui/js/model/messageList',
        'mage/translate',
        'ArmMage_SolanaPay/js/lib/buffer',
        'ArmMage_SolanaPay/js/lib/splToken',
        'ArmMage_SolanaPay/js/node_modules/@solana/web3.js/lib/index.iife'
    ],
    function ($, Component, processPayment, totals, urlBuilder, quote, messageList, $t) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'ArmMage_SolanaPay/payment/solanapay',
                usdcAccounts: [],
                selectedUsdcAccount: null
            },
            defaults: {
                template: 'ArmMage_SolanaPay/payment/solanapay',
                usdcAccounts: [],
                selectedUsdcAccount: null
            },

            initObservable: function () {
                this._super()
                    .observe(['usdcAccounts', 'selectedUsdcAccount', 'connectedWalletAddress']);

                return this;
            },
            
            connectWallet: function () {
                var self = this;
                if (typeof window.solana !== 'undefined') {
                    window.solana.connect()
                        .then(async function (wallet) {
                            self.connectedWalletAddress(self.truncateAddress(wallet.publicKey.toString()));
                            self.fetchUsdcAccounts(wallet.publicKey.toString());

                        }).catch(function (error) {
                            console.error('Web3 wallet connection error:', error);
                        });
                } else {
                    console.error('Web3 library not found.');
                }
            },

            onUsdcAccountChange: async function () {
                var self = this;
                var selectedAccount = this.selectedUsdcAccount();
                
                if (selectedAccount) {
                    this.isPlaceOrderActionAllowed(true);
                    const provider = self.getProvider();
                    // Ensure wallet and provider are available
                    if (provider) {
                        if (totals.totals()) {
                            var grandTotal = parseFloat(totals.totals()['grand_total']);
                            if (provider.publicKey) {
                                const senderPubkeyString = provider.publicKey.toString();
                                const sender = new solanaWeb3.PublicKey(senderPubkeyString);
                                const merchant = new solanaWeb3.PublicKey(self.getPublicKey()); 
                                
                                try {
                                    let signature = await processPayment.processPayment(provider, sender,selectedAccount, merchant, grandTotal);
                                    if (signature) {
                                      
                                        $.cookie('solana_input_signature', signature.signature);
                                        $.cookie('solana_input_from_wallet_public_key', senderPubkeyString);
                                        // Trigger place order
                                        $('.action.primary.checkout.solanapay').click();
                                    } else {
                                        $('.solanapay-method .message.error.message-error span').text('Order wasn\'t placed.');
                                        $('.solanapay-method .message.error.message-error').css('visibility', 'visible');
                                    }
                                } catch (error) {
                                    console.error('Payment processing error:', error);
                                    messageList.addErrorMessage({ message: $t('Error fetching USDC accounts.') });
                                }
                            }
                        }
                    }
                }
            },
            
            getProvider: function () {
                if ('phantom' in window) {
                    const provider = window.phantom?.solana;

                    if (provider?.isPhantom) {
                        return provider;
                    }
                }

                window.open('https://phantom.app/', '_blank');
            },

            fetchUsdcAccounts: async function (publicKey) {
                var self = this;
                var connection =  await processPayment.establishConnection(self.getMode());

                // Assuming findUsdcAccount is modified to return an array of accounts
                await processPayment.findUsdcAccount(new solanaWeb3.PublicKey(publicKey), connection).then(function (accounts) {
                    if (accounts.length === 0) {
                        messageList.addErrorMessage({ message: $t('No USDC accounts found.') });
                    }

                        var formattedAccounts = accounts.map(function (account) {
                                return {
                                    formattedAddress: self.truncateAddress(account.mintAddress), // For display
                                    mintAddress: account.mintAddress, // Actual value
                                    balance: account.balance
                                };
                            });
                     self.usdcAccounts(formattedAccounts);
                    if (accounts.length === 1) {
                        self.selectedUsdcAccount(accounts[0]);
                        self.onUsdcAccountChange();
                    }
                   
                }).catch(function (error) {
                    console.error('Error fetching USDC accounts:', error);
                    messageList.addErrorMessage({ message: $t('Error fetching USDC accounts.') });
                });
            },

            getGrandTotal: function () {
                if (totals.totals()) {
                    var grandTotal = parseFloat(totals.totals()['grand_total']);
                    return grandTotal;
                }
            },
            truncateAddress: function (address) {
                if (address.length >= 8) {
                    var firstPart = address.substring(0, 4);
                    var lastPart = address.substring(address.length - 4);
                    return firstPart + '...' + lastPart;
                } else {
                    return address;
                }
            },

            checkPlaceOrderButton: function () {
                // Add your logic here to enable/disable the Place Order button based on conditions
                var isPlaceOrderAllowed = true; // Replace this with your actual condition
                this.isPlaceOrderActionAllowed(isPlaceOrderAllowed);
            },

            getImage: function () {
                return window.checkoutConfig.payment.solanapay.image;
            },

            getPublicKey: function () {
                return window.checkoutConfig.payment.solanapay.public_key;
            },

            getMode: function () {
                return window.checkoutConfig.payment.solanapay.mode;
            },
        });
    }
);