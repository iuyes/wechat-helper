<?php namespace Huying\WechatHelper\Exceptions;

use Exception;

class SignatureWrongException extends Exception{

    protected $signature;
    protected $token;
    protected $timestamp;
    protected $nonce;

    public function __construct($signature, $token, $timestamp, $nonce, Exception $previous = null)
    {
        parent::__construct('signature验证错误', 0, $previous);
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

}
