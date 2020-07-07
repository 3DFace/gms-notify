<?php

namespace dface\GmsNotify;

class GmsPhone
{

	/** @var string */
	private $phone;

	public function __construct(string $phone)
	{
		if (\preg_match('/^\d{12}$/', $phone) !== 1) {
			throw new \InvalidArgumentException('invalid phone');
		}
		$this->phone = $phone;
	}

	public function getPhone() : string
	{
		return $this->phone;
	}

	public function __toString()
	{
		return $this->phone;
	}

}
