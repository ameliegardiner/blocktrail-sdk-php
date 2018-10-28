<?php

namespace Ameliegardiner\SDK;

use BitWasp\Bitcoin\Address\PayToPubKeyHashAddress;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\CompactSignatureSerializerInterface;
use BitWasp\Bitcoin\Crypto\Random\Random;
use BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey;
use BitWasp\Bitcoin\Key\Deterministic\HierarchicalKeyFactory;
use BitWasp\Bitcoin\MessageSigner\MessageSigner;
use BitWasp\Bitcoin\MessageSigner\SignedMessage;
use BitWasp\Bitcoin\Mnemonic\Bip39\Bip39SeedGenerator;
use BitWasp\Bitcoin\Mnemonic\MnemonicFactory;
use BitWasp\Bitcoin\Network\NetworkFactory;
use BitWasp\Bitcoin\Transaction\TransactionFactory;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;
use Ameliegardiner\CryptoJSAES\CryptoJSAES;
use Ameliegardiner\SDK\Address\AddressReaderBase;
use Ameliegardiner\SDK\Address\BitcoinAddressReader;
use Ameliegardiner\SDK\Address\BitcoinCashAddressReader;
use Ameliegardiner\SDK\Address\CashAddress;
use Ameliegardiner\SDK\Backend\BlocktrailConverter;
use Ameliegardiner\SDK\Backend\BtccomConverter;
use Ameliegardiner\SDK\Backend\ConverterInterface;
use Ameliegardiner\SDK\Bitcoin\BIP32Key;
use Ameliegardiner\SDK\Connection\RestClient;
use Ameliegardiner\SDK\Exceptions\BlocktrailSDKException;
use Ameliegardiner\SDK\Network\BitcoinCash;
use Ameliegardiner\SDK\Connection\RestClientInterface;
use Ameliegardiner\SDK\Network\BitcoinCashRegtest;
use Ameliegardiner\SDK\Network\BitcoinCashTestnet;
use Ameliegardiner\SDK\V3Crypt\Encryption;
use Ameliegardiner\SDK\V3Crypt\EncryptionMnemonic;
use Ameliegardiner\SDK\V3Crypt\KeyDerivation;

class Throttler {

    /**
     * @var float|null
     */
    private $lastTime = null;

    /**
     * interval to wait in seconds, can be float
     *
     * @var float
     */
    private $interval;

    public function __construct($interval) {
        $this->interval = $interval;
    }

    public function waitForThrottle() {
        if (!$this->lastTime) {
            $this->lastTime = \microtime(true);
            return;
        }

        $diff = $this->interval - (\microtime(true) - $this->lastTime);

        if ($diff > 0) {
            usleep((int)ceil($diff * 1000 * 1000));
        }

        $this->lastTime = \microtime(true);
    }

    private static $instances = [];

    public static function getInstance($key, $interval) {
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new Throttler($interval);
        }

        return self::$instances[$key];
    }
}
