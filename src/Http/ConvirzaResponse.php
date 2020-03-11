<?php

namespace Skidaatl\Convirza\Http;

use Skidaatl\Convirza\Exception\ConvirzaBadRequestException;

class ConvirzaResponse
{
	public $result;

	public $error;

	public $data;

	public function __construct(array $items)
	{
		if( $items['result'] !== 'success' &&
			$items['err'] !== 'no records found.'
		) {
			throw new ConvirzaBadRequestException($items['err']);
		}

		$this->result = $items['result'];
		$this->error = $items['err'];
		$this->data = $items['data'] ?? [];
	}

	public function count()
	{
		return count($this->data);
	}
}
