<?php

namespace D3strukt0r\VotifierClient\VoteType;

interface VoteInterface
{
    /**
     * @return string
     */
    public function getServiceName();

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @return string
     */
    public function getAddress();

    /**
     * @return int|null
     */
    public function getTimestamp();

    /**
     * @param \DateTime $timestamp
     *
     * @return $this
     */
    public function setTimestamp(\DateTime $timestamp = null);
}
