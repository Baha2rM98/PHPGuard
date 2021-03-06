<?php

/**
 * @author  Baha2r
 * @license MIT
 * Date: 02/Nov/2019 00:51 AM
 *
 * Main PHPGuard cryptography class
 **/

namespace PHPGuard\Crypto;


use PHPGuard\Core\BaseCrypto\BaseCrypto;
use PHPGuard\Core\BaseCrypto\Encryption;
use PHPGuard\Core\BaseCrypto\Decryption;
use PHPGuard\Core\Exceptions\CryptoException;
use PHPGuard\Core\Exceptions\EncryptionException;
use PHPGuard\Core\Exceptions\DecryptionException;


class Crypto extends BaseCrypto implements Encryption, Decryption
{

    /**
     * @var string Key
     */
    private $key;

    /**
     * @var string Algorithm
     */
    private $algorithm;


    /**
     * Constructor
     *
     * @param string $algorithm The cryptography algorithm
     *                             <p>
     *                             The default method is AES-256-CBC
     */
    public function __construct($algorithm = "AES-256-CBC")
    {
        $this->algorithm = strtoupper($algorithm);
        parent::__construct($this->algorithm);
    }


    /**
     * Validates cryptography method name is acceptable or not
     *
     * @return boolean Returns true if cryptography method name is acceptable, false otherwise
     */
    private function validateCipherMethod()
    {
        return in_array($this->algorithm, self::supported(), true);
    }


    /**
     * Validates if Key is set or not
     *
     * @return boolean Returns authenticity of Key
     * @throws CryptoException Throws exception if key remain null
     */
    private function isKeySet()
    {
        return isset($this->key) ?? false;
    }


    /**
     * Set key of cryptography system
     *
     * @param string $key Key of cryptography system [recommended use user's password as key]
     */
    public function setKey($key): void
    {
        $this->key = parent::setupKey($key);
    }


    /**
     * Set algorithm of cryptography system
     *
     * @param string $cipher The new cipher
     *
     * @return Crypto Returns new instance of Crypto class with $cipher parameter
     */
    public function setCipher($cipher)
    {
        return new Crypto($cipher);
    }


    /**
     * Returns constructed key of cryptography system
     *
     * @return string Returns key of cryptography system
     */
    public function getKey()
    {
        return $this->key;
    }


    /**
     * Returns current cryptography algorithm
     *
     * @return string Returns current cryptography algorithm
     */
    public function getCipher()
    {
        return $this->algorithm;
    }


    /**
     * Encrypts the given data
     *
     * @param mixed $data The data that will be encrypted
     * @param boolean $serialize [Optional] If set to true, converts mixed types to string
     *
     * @return string Returns encrypted value, false on failure
     * @throws EncryptionException Throws exception if validate method returns false or can not decrypt the the $cipher
     * @throws CryptoException Throws exception if key remain null
     */
    public function encrypt($data, $serialize = true)
    {
        if (!$this->validateCipherMethod()) {
            throw new EncryptionException("Cipher method not defined!");
        }
        if (!$this->isKeySet()) {
            throw new CryptoException("Empty Key!");
        }
        return parent::encryption($data, $this->key, $serialize);
    }


    /**
     * Encrypts the given data
     *
     * @param string $data The data that will be encrypted
     *
     * @return string Returns encrypted data, false on failure
     * @throws EncryptionException Throws exception if validate method returns false or can not encrypt the the $data
     * @throws CryptoException Throws exception if key remain null
     */
    public function encryptString($data)
    {
        return $this->encrypt($data, false);
    }


    /**
     * Decrypts the given cipher
     *
     * @param string $jsonPayload The json payload that contains cipher and mac
     * @param boolean $unserialize [Optional] If set to true, converts string types to mixed
     *
     * @return false|mixed|string Returns encrypted value, false on failure
     * @throws DecryptionException Throws exception if validate method returns false or can not decrypt the the $cipher
     * @throws CryptoException Throws exception if key remain null
     */
    public function decrypt($jsonPayload, $unserialize = true)
    {
        if (!$this->validateCipherMethod()) {
            throw new DecryptionException("Cipher method not defined!");
        }
        if (!$this->isKeySet()) {
            throw new CryptoException("Empty Key!");
        }
        return parent::decryption($jsonPayload, $this->key, $unserialize);
    }


    /**
     * Decrypts the given cipher
     *
     * @param string $jsonPayload The json payload that contains cipher and mac
     *
     * @return false|string Returns decrypted cipher, false on failure
     * @throws DecryptionException Throws exception if validate method returns false or can not decrypt the the $cipher
     * @throws CryptoException Throws exception if key remain null
     */
    public function decryptString($jsonPayload)
    {
        return $this->decrypt($jsonPayload, false);
    }


    /**
     * Generates a new key
     *
     * @param int $length The length of the key
     *
     * @return string Returns generated key
     */
    public static function generateKey($length = 56)
    {
        return self::randomBytes($length);
    }


    /**
     * Generates cryptographically secure pseudo-random bytes
     *
     * @param integer $length The length of the random byte
     *
     * @return string Returns Generated random
     */
    public static function randomBytes($length)
    {
        return parent::byteRandom($length);
    }


    /**
     * Generates cryptographically secure pseudo-random strings
     *
     * @param integer $length The length of the random string
     *
     * @return string Returns Generated random
     */
    public static function randomString($length)
    {
        return parent::stringRandom($length);
    }


    /**
     * Returns supported cryptography algorithms
     *
     * @return array Returns name of supported cryptography algorithms
     */
    public static function supported()
    {
        return [
            "AES-128-CBC",
            "AES-192-CBC",
            "AES-256-CBC",
            "BF-CBC",
            "CAST5-CBC"
        ];
    }
}