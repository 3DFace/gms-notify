<?php

namespace dface\GmsNotify;

class GmsSettings implements \JsonSerializable
{

	private GmsServerParams $transactional_server;
	private GmsServerParams $promotional_server;

	public function __construct(GmsServerParams $transactional_server, GmsServerParams $promotional_server)
	{
		$this->transactional_server = $transactional_server;
		$this->promotional_server = $promotional_server;
	}

	public function getTransactionalServer() : GmsServerParams
	{
		return $this->transactional_server;
	}

	public function getPromotionalServer() : GmsServerParams
	{
		return $this->promotional_server;
	}

	public function withTransactionalServer(GmsServerParams $val) : self
	{
		$clone = clone $this;
		$clone->transactional_server = $val;
		return $clone;
	}

	public function withPromotionalServer(GmsServerParams $val) : self
	{
		$clone = clone $this;
		$clone->promotional_server = $val;
		return $clone;
	}

	public function jsonSerialize() : array
	{
		return [
			'transactional_server' => $this->transactional_server->jsonSerialize(),
			'promotional_server' => $this->promotional_server->jsonSerialize(),
		];
	}

	public static function deserialize(array $arr) : self
	{
		if (\array_key_exists('transactional_server', $arr)) {
			$transactional_server = $arr['transactional_server'];
		} else {
			throw new \InvalidArgumentException("Property 'transactional_server' not specified");
		}
		$transactional_server = $transactional_server !== null ? GmsServerParams::deserialize($transactional_server) : null;

		if (\array_key_exists('promotional_server', $arr)) {
			$promotional_server = $arr['promotional_server'];
		} else {
			throw new \InvalidArgumentException("Property 'promotional_server' not specified");
		}
		$promotional_server = $promotional_server !== null ? GmsServerParams::deserialize($promotional_server) : null;

		return new self($transactional_server, $promotional_server);
	}

}
