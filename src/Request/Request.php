<?php

namespace Jsl\Kernel\Request;

use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class Request extends HttpFoundationRequest implements RequestInterface
{
    /**
     * Checks if the request explicitly expects JSON in response
     *
     * @return bool
     */
    public function acceptsJson(): bool
    {
        return in_array('application/json', $this->getAcceptableContentTypes());
    }
}
