<?php

namespace D3strukt0r\VotifierClient\ServerType;

use D3strukt0r\VotifierClient\ServerConnection;
use D3strukt0r\VotifierClient\VoteType\VoteInterface;

interface ServerTypeInterface
{
    /**
     * Returns the host.
     *
     * @return string
     */
    public function getHost();

    /**
     * Returns the port.
     *
     * @return int
     */
    public function getPort();

    /**
     * Returns the public key.
     *
     * @return string
     */
    public function getPublicKey();

    /**
     * Verifies that the connection is correct.
     *
     * @param string $header
     *
     * @return bool
     */
    public function verifyConnection($header);

    /**
     * Sends the vote package to the server.
     *
     * @param \D3strukt0r\VotifierClient\ServerConnection       $connection
     * @param \D3strukt0r\VotifierClient\VoteType\VoteInterface $vote
     *
     * @throws \Exception
     */
    public function send(ServerConnection $connection, VoteInterface $vote);
}
