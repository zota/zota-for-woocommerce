<?php

namespace Zotapay;

/**
 * ApiClientInterface.
 */
interface ApiClientInterface
{
    /**
     * Zotapay API request.
     *
     * @param \Zotapay\Data $data
     *
     * @return self
     */
    public function request($data);
}
