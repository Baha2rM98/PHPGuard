<?php

/**
 * @author  Baha2r
 * @license MIT
 * Date: 08/Dec/2019 19:38 PM
 *
 * Base PHPGuard cryptography class
 **/

namespace PHPGuard\Core\Crypto;


use PHPGuard\Core\KeySetup;
use PHPGuard\Core\Exceptions\EncryptionException;
use PHPGuard\Core\Exceptions\DecryptionException;
use PHPGuard\Core\Hashing\Hash;


abstract class BaseCrypto extends KeySetup
{

    /**
     * @var string Algorithm
     */
    private $algorithm;


    /**
     * Constructor
     *
     * @param  string  $algorithm
     */
    protected function __construct($algorithm)
    {
        parent::__construct();
        $this->algorithm = strtoupper($algorithm);
    }


    /**
     * @param  string  $package
     *
     * @return array
     */
    private function getJsonPackage($package)
    {
        $package = json_decode(base64_decode($package), true);
        if (!$this->isValidPackage($package)) {
            throw new DecryptionException("Invalid package!");
        }
        return $package;
    }


    /**
     * @param  mixed  $package
     *
     * @return boolean
     */
    private function isValidPackage($package)
    {
        return is_array($package) && isset($package["iv"], $package["cipher"], $package["mac"]) && (strlen(base64_decode($package["iv"],
                                true)) === openssl_cipher_iv_length($this->algorithm));
    }


    /**
     * @param  mixed    $data
     * @param  string   $key
     * @param  boolean  $serialize
     *
     * @return string
     * @throws EncryptionException
     */
    protected function encryption($data, $key, $serialize)
    {
        $iv = random_bytes(openssl_cipher_iv_length($this->algorithm));
        $cipher = base64_encode(openssl_encrypt($serialize ? json_encode(serialize($data)) : $data, $this->algorithm,
                $key, 0, $iv));
        if (!$cipher) {
            throw new EncryptionException("Could not encrypt the data!");
        }
        $mac = Hash::makeMAC($cipher, Hash::DEFAULT_SALT.$key.$iv);
        $iv = base64_encode($iv);
        $package = json_encode(compact("iv", "cipher", "mac"));
        if (!$package) {
            throw new EncryptionException("Could not encrypt the data!");
        }
        return base64_encode($package);
    }


    /**
     * @param  string   $package
     * @param  string   $key
     * @param  boolean  $unserialize
     *
     * @return false|mixed|string
     * @throws DecryptionException
     */
    protected function decryption($package, $key, $unserialize)
    {
        $package = $this->getJsonPackage($package);
        $iv = base64_decode($package["iv"]);
        $newMAC = Hash::makeMAC($package["cipher"], Hash::DEFAULT_SALT.$key.$iv);
        if (!hash_equals($package["mac"], $newMAC)) {
            throw new DecryptionException("Invalid MAC!");
        }
        $decrypted = openssl_decrypt(base64_decode($package["cipher"]), $this->algorithm, $key, 0, $iv);
        if (!$decrypted) {
            throw new DecryptionException("Could not decrypt the data!");
        }
        return $unserialize ? unserialize(json_decode($decrypted)) : $decrypted;
    }


    /**
     * @param  integer  $length
     *
     * @return string
     */
    protected static function byteRandom($length)
    {
        return random_bytes($length);
    }


    /**
     * @param  integer  $length
     *
     * @return string
     */
    protected static function stringRandom($length)
    {
        return bin2hex(random_bytes($length));
    }
}