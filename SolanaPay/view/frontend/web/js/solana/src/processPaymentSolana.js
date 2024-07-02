define([
    'ArmMage_SolanaPay/js/lib/buffer',
    'ArmMage_SolanaPay/js/node_modules/@solana/web3.js/lib/index.iife'
], function () {
    'use strict';
    const { Connection, LAMPORTS_PER_SOL } = solanaWeb3;

    async function establishConnection(cluster = 'devnet') {
        const endpoint = solanaWeb3.clusterApiUrl(cluster);
        const connection = new Connection(endpoint, 'confirmed');
        const version = await connection.getVersion();
        console.log('Connection to cluster established:', endpoint, version);

        return connection;
    }

    async function processPayment(provider, fromWallet, toWallet, amount) {
        try {
            const provider = getProvider();
            const connection = await establishConnection();
            const amountSolana = await usdtoSolana(amount);

            const transaction = new solanaWeb3.Transaction().add(
                solanaWeb3.SystemProgram.transfer({
                    fromPubkey: fromWallet,
                    toPubkey: toWallet,
                    lamports: amountSolana * LAMPORTS_PER_SOL
                }),
            );

            transaction.feePayer = await provider.publicKey;
            const blockhashObj = await connection.getRecentBlockhash();
            transaction.recentBlockhash = blockhashObj.blockhash;

            const signature = await provider.signAndSendTransaction(transaction);
            await connection.confirmTransaction(signature);
            return signature;
        } catch (error) {
            console.error(error);
            throw error; // Re-throw the error to handle it further if needed
        }
    }

    const getProvider = () => {
        if ('phantom' in window) {
            const provider = window.phantom?.solana;

            if (provider?.isPhantom) {
                return provider;
            }
        }
        window.open('https://phantom.app/', '_blank');
    };

    function usdtoSolana(amountUsd) {
        return new Promise((resolve, reject) => {
            var url = "https://api.coingecko.com/api/v3/simple/price?ids=solana&vs_currencies=usd";
            var xhr = new XMLHttpRequest();

            xhr.open("GET", url);
            xhr.setRequestHeader("accept", "application/json");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        let solperUsd = JSON.parse(xhr.responseText);
                        let solanaAmount = amountUsd / solperUsd.solana.usd;
                        resolve(solanaAmount.toFixed(2));
                    } else {
                        reject(`Error fetching exchange rate: ${xhr.status}`);
                    }
                }
            };

            xhr.send();
        });
    }

    return {
        processPayment: processPayment
    };
});