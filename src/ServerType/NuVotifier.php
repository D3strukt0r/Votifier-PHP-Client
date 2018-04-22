<?php

namespace D3strukt0r\VotifierClient\ServerType;

use D3strukt0r\VotifierClient\Messages;
use D3strukt0r\VotifierClient\ServerConnection;
use D3strukt0r\VotifierClient\VoteType\VoteInterface;

class NuVotifier extends ClassicVotifier
{
    private $protocolV2;
    private $token;

    /**
     * NuVotifier constructor.
     *
     * @param string      $host
     * @param int|null    $port
     * @param string      $publicKey
     * @param bool        $protocolV2
     * @param string|null $token
     */
    public function __construct($host, $port, $publicKey, $protocolV2 = false, $token = null)
    {
        parent::__construct($host, $port, $publicKey);

        $this->protocolV2 = $protocolV2;
        $this->token = $token;
    }

    public function isProtocolV2()
    {
        return $this->protocolV2;
    }

    public function verifyConnection($header)
    {
        $header_parts = explode(' ', $header);
        if (false === $header || false === mb_strpos($header, 'VOTIFIER') || 3 !== count($header_parts)) {
            return false;
        }

        return true;
    }

    public function preparePackageV2(VoteInterface $vote, $challenge)
    {
        $payload_json = json_encode(array(
            'username' => $vote->getUsername(),
            'serviceName' => $vote->getServiceName(),
            'timestamp' => $vote->getTimestamp(),
            'address' => $vote->getAddress(),
            'challenge' => $challenge,
        ));
        $signature = base64_encode(hash_hmac('sha256', $payload_json, $this->token, true));
        $message_json = json_encode(array('signature' => $signature, 'payload' => $payload_json));

        $payload = pack('nn', 0x733a, mb_strlen($message_json)).$message_json;

        return $payload;
    }

    /**
     * {@inheritdoc}
     */
    public function send(ServerConnection $connection, VoteInterface $vote)
    {
        if (!$this->isProtocolV2()) {
            parent::send($connection, $vote);

            return;
        }

        if (!$this->verifyConnection($header = $connection->receive(64))) {
            throw new \Exception(Messages::get(Messages::NOT_VOTIFIER));
        }
        $header_parts = explode(' ', $header);
        $challenge = mb_substr($header_parts[2], 0, -1);
        $payload = $this->preparePackageV2($vote, $challenge);

        if (false === $connection->send($payload)) {
            throw new \Exception(Messages::get(Messages::NOT_SENT_PACKAGE));
        }

        if (!$response = $connection->receive(256)) {
            throw new \Exception(Messages::get(Messages::NOT_RECEIVED_PACKAGE));
        }

        $result = json_decode($response);
        if ('ok' !== $result->status) {
            throw new \Exception(Messages::get(Messages::NOT_RECEIVED_PACKAGE, null, array(0 => $result->cause, 1 => $result->error)));
        }
    }
}
