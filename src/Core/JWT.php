<?php

namespace Wepesi\Core;

/**
 *
 */
class JWT
{
    /**
     *
     */
    const CIPHERING = "AES-128-CTR";
    /**
     *
     */
    const OPTION = 110;
    /**
     * @var false|int
     */
    private $iv_length;
    /**
     * @var string
     */
    private string $encryption_key;
    /**
     * @var string
     */
    private string $app_encryption_iv;
    /**
     * @var string
     */
    private string $decryption_key;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->decryption_key = bin2hex(random_bytes(32));
        $this->encryption_key = $this->decryption_key;
        $this->iv_length = 16;
        // Use OpenSSl Encryption method
        $this->iv_length = openssl_cipher_iv_length(self::CIPHERING);
        // generate random bytes to be store on the encryption token-key
        $this->app_encryption_iv = random_bytes($this->iv_length);
    }

    /**
     * @param array $data data to be encrypted with two specific key `expired`: can be time in second or a datetime `data`: data to be encrypted
     * @param string|null $cypherkey
     * @param bool $isDate specify if the expired value is a date ex: 2021-12-19
     * @return array|string
     */
    public function generate(array $data, string $cypherkey = null, bool $isDate = false)
    {
        try {
            if (!isset($data["data"]) && empty($data["data"])) {
                throw new \Exception("no data to be managed");
            }
            $this->encryption_key = $cypherkey ? bin2hex($cypherkey) : $this->decryption_key;
            $expired = $data["expired"] ?? 3600;
            $time = strtotime("now + $expired second");
            //from this p
            if ($isDate) {
                $time = strtotime($data["expired"]);
            }
            $data["time"] = $time;
            return bin2hex($this->app_encryption_iv) . "." . $this->cryptData($data, $this->encryption_key) . "." . $this->encryption_key;
        } catch (\Exception $ex) {
            return ["exception" => $ex->getMessage()];
        }
    }

    /**
     * @param array $data
     * @param string $cypher_key
     * @return false|string
     */
    private function cryptData(array $data, string $cypher_key)
    {
        $simple_string = json_encode($data, true);
        // Store the encryption key
        $this->encryption_key = $cypher_key;
        // Use openssl_encrypt() function to encrypt the data
        return openssl_encrypt(
            $simple_string,
            self::CIPHERING,
            $this->encryption_key,
            self::OPTION,
            $this->app_encryption_iv
        );
    }

    /**
     * @param string $token_value
     * @param string|null $cypherkey
     * @return array|false|mixed
     */
    public function decode(string $token_value, string $cypherkey = null)
    {
        try {
            $decrypt_data = $this->decryptData($token_value, $cypherkey);
            if (isset($decrypt_data['exception'])) {
                return $decrypt_data;
            }
            if (!is_array($decrypt_data)) return false;
            $decrypt_time = $decrypt_data["time"] ?? 0;
            $_thisTime = strtotime("now");
            if (($_thisTime - $decrypt_time) > 0) {
                throw new \Exception("token expired");
            }
            return $decrypt_data;
        } catch (\Exception $ex) {
            return ["exception" => $ex->getMessage()];
        }
    }

    /**
     * @param string $token_key
     * @param string|null $cypherkey
     * @return array|mixed
     * this method help to decode information store on the token key
     */
    private function decryptData(string $token_key, string $cypherkey = null)
    {
        try {
            $explode = explode(".", $token_key);
            $this->decryption_key = $explode[2];
            if ($cypherkey && hex2bin($explode[2]) == $cypherkey) {
                $this->decryption_key = bin2hex($cypherkey);
            }
            if (strlen($explode[0]) % 2 != 0) {
                throw new \Exception("token invalid");
            }
            $this->app_encryption_iv = hex2bin($explode[0]);
            if (strlen($this->app_encryption_iv) < 16) {
                throw new \Exception("token invalid");
            }
            $token_key = $explode[1];
            $decryption = openssl_decrypt(
                $token_key,
                self::CIPHERING,
                $this->decryption_key,
                self::OPTION,
                $this->app_encryption_iv,
            );
            return json_decode($decryption, true);
        } catch (\Exception $ex) {
            return ["exception" => $ex->getMessage()];
        }
    }
}
