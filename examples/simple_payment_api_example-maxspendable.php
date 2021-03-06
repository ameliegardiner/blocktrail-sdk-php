<?php

use Ameliegardiner\SDK\BlocktrailSDK;
use Ameliegardiner\SDK\Connection\Exceptions\ObjectNotFound;
use Ameliegardiner\SDK\Wallet;
use Ameliegardiner\SDK\WalletInterface;

require_once __DIR__ . "/../vendor/autoload.php";

$client = new BlocktrailSDK(getenv('BLOCKTRAIL_SDK_APIKEY') ?: "MY_APIKEY", getenv('BLOCKTRAIL_SDK_APISECRET') ?: "MY_APISECRET", "BTC", true /* testnet */, 'v1');
// $client->setVerboseErrors();
// $client->setCurlDebugging();

/**
 * @var $wallet             \Blocktrail\SDK\WalletInterface
 * @var $backupMnemonic     string
 */
try {
    /** @var Wallet $wallet */
    $wallet = $client->initWallet([
        "identifier" => "example-wallet",
        "passphrase" => "example-strong-password"
    ]);
} catch (ObjectNotFound $e) {
    list($wallet, $primaryMnemonic, $backupMnemonic, $blocktrailPublicKeys) = $client->createNewWallet([
        "identifier" => "example-wallet",
        "passphrase" => "example-strong-password",
        "key_index" => 9999
    ]);
}

var_dump($wallet->getBalance());
var_dump($wallet->getMaxSpendable());
