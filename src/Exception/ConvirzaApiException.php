<?php

namespace Skidaatl\Convirza\Exception;

use Log;
use GuzzleHttp\Exception\RequestException;

class ConvirzaApiException extends ConvirzaException
{
	/**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
    	if($this->getPrevious() instanceof RequestException) {
    		$request = $this->getPrevious()->getRequest();
    		Log::error( $this->getMessage(), [
    				'failed request' => [
    					'method' => $request->getMethod(),
    					'uri' => $request->getUri(),
    					'requestTarget' => $request->getRequestTarget(),
    					'headers' => $request->getHeaders(),
    				],
    				'exception' => $this
    		]);
		}
    }
}
