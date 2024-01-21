define([
  'ArmMage_SolanaPay/js/lib/buffer',
  'ArmMage_SolanaPay/js/lib/splToken',
  'ArmMage_SolanaPay/js/node_modules/@solana/web3.js/lib/index.iife'
], function () {
  'use strict';

  const { Connection } = solanaWeb3;
  const { TOKEN_PROGRAM_ID } = splToken;
  const rpcapiURL = window.checkoutConfig.payment.solanapay.rpcapiURL;
  const rpcapiKEY = window.checkoutConfig.payment.solanapay.rpcapiKEY;

  async function establishConnection(cluster = 'devnet') {

    if(cluster == 'mainnet-beta'){
      const connection = new solanaWeb3.Connection(rpcapiURL+'?api-key='+rpcapiKEY, 'confirmed');
      return connection;
    }
    const endpoint = solanaWeb3.clusterApiUrl(cluster);
    const connection = new Connection(endpoint, 'confirmed');
    const version = await connection.getVersion();
    console.log('Connection to cluster established:', endpoint, version);
    return connection;
  }

  async function processPayment(provider, senderPublicKey, mintAddress, recipientPublicKey, amount) {
    try {
      const connection = await establishConnection(window.checkoutConfig.payment.solanapay.mode);
      // USDC token mint address on devnet
      const usdcMintAddress = new solanaWeb3.PublicKey(mintAddress);

      // Get the associated token accounts for the sender and recipient
      const senderUsdcAccount = await splToken.getAssociatedTokenAddress(usdcMintAddress, senderPublicKey);
      const recipientUsdcAccount = await splToken.getAssociatedTokenAddress(usdcMintAddress, recipientPublicKey);

      // Create a transfer instruction using SPL Token library
      const transferInstruction = splToken.createTransferInstruction(
        senderUsdcAccount,
        recipientUsdcAccount,
        senderPublicKey,
        1000000 * amount,
        [],
        TOKEN_PROGRAM_ID
      );

      // Create a transaction and add the transfer instruction
      const transaction = new solanaWeb3.Transaction().add(transferInstruction);

      transaction.feePayer = senderPublicKey;
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


  async function findTokenMint(senderPublicKey, connection) {
    // Get all token accounts for the user's public key
    const tokenAccounts = await connection.getParsedTokenAccountsByOwner(senderPublicKey, {
      programId: splToken.TOKEN_PROGRAM_ID,
    });

    // Array to store mint addresses and balances
    let mints = [];

    // Extract mint address and balance for each token account
    for (const account of tokenAccounts.value) {
      const tokenAccountInfo = account.account.data.parsed.info;
      mints.push({
        mintAddress: tokenAccountInfo.mint,
        balance: tokenAccountInfo.tokenAmount.uiAmount
      });
    }

    return mints;
  }

  async function findUsdcAccount(senderPublicKey, connection) {
    const mints = await findTokenMint(senderPublicKey, connection);
    const validAccounts = mints.filter(mint => mint.balance > 0);
    return validAccounts.length ? validAccounts : [];
  }

  return {
    processPayment: processPayment,
    findUsdcAccount:findUsdcAccount,
    establishConnection:establishConnection
  };
});