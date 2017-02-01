<?php

namespace whitephp\session;
use SessionHandler;

class SecureSession extends SessionHandler
{

    protected $key;

    public function __construct()
    {
        if (! extension_loaded('openssl')) {
            throw new \RuntimeException(sprintf(
                "Missing OpenSSL extension for %s",
                __CLASS__
                ));
        }
        if (! extension_loaded('mbstring')) {
            throw new \RuntimeException(sprintf(
                "Missing Multibytes extension for %s",
                __CLASS__
                ));
        }
    }

    public function open($save_path, $session_name)
    {
        $this->key = $this->getKey('KEY_' . $session_name);
        return parent::open($save_path, $session_name);
    }

    public function read($id)
    {
        $data = parent::read($id);
        return empty($data) ? '' : $this->decrypt($data, $this->key);
    }

    public function write($id, $data)
    {
        return parent::write($id, $this->encrypt($data, $this->key));
    }

    protected function encrypt($data, $key)
    {
        $iv = random_bytes(16); 
        // Encrypt
        $ciphertext = openssl_encrypt(
            $data,
            'AES-256-CBC',
            mb_substr($key, 0, 32, '8bit'),
            OPENSSL_RAW_DATA,
            $iv
            );
        // Authenticate
        $hmac = hash_hmac(
            'SHA256',
            $iv . $ciphertext,
            mb_substr($key, 32, null, '8bit'),
            true
            );
        return $hmac . $iv . $ciphertext;
    }

    protected function decrypt($data, $key)
    {
        $hmac       = mb_substr($data, 0, 32, '8bit');
        $iv         = mb_substr($data, 32, 16, '8bit');
        $ciphertext = mb_substr($data, 48, null, '8bit');
        // Authenticate
        $hmacNew = hash_hmac(
            'SHA256',
            $iv . $ciphertext,
            mb_substr($key, 32, null, '8bit'),
            true
            );
        if (! $this->hash_equals($hmac, $hmacNew)) {
            throw new \RuntimeException('Authentication failed');
        }
        // Decrypt
        return openssl_decrypt(
            $ciphertext,
            'AES-256-CBC',
            mb_substr($key, 0, 32, '8bit'),
            OPENSSL_RAW_DATA,
            $iv
            );
    }

    protected function getKey($name)
    {
        if (empty($_COOKIE[$name])) {
            $key = random_bytes(64); 
            $cookieParam = session_get_cookie_params();
            setcookie(
                $name,
                base64_encode($key),
                // calculate for lifetime > 0
                // leave 0 for valid till browser close
                ($cookieParam['lifetime'] > 0) ? time() + $cookieParam['lifetime'] : 0,
                $cookieParam['path'],
                $cookieParam['domain'],
                $cookieParam['secure'],
                $cookieParam['httponly']
                );
        } else {
            $key = base64_decode($_COOKIE[$name]);
        }
        return $key;
    }

    protected function hash_equals($expected, $actual)
    {
        $expected     = (string) $expected;
        $actual       = (string) $actual;
        if (function_exists('hash_equals')) {
            return hash_equals($expected, $actual);
        }
        $lenExpected  = mb_strlen($expected, '8bit');
        $lenActual    = mb_strlen($actual, '8bit');
        $len          = min($lenExpected, $lenActual);
        $result = 0;
        for ($i = 0; $i < $len; $i++) {
            $result |= ord($expected[$i]) ^ ord($actual[$i]);
        }
        $result |= $lenExpected ^ $lenActual;
        return ($result === 0);
    }
}