<?php

namespace dface\GmsNotify;

class GmsSettings implements \JsonSerializable {

	/** @var GmsServerParams */
	protected $transactional_server;
	/** @var GmsServerParams */
	protected $promotional_server;

	function __construct(GmsServerParams $transactional_server, GmsServerParams $promotional_server){
		$this->transactional_server = $transactional_server;
		$this->promotional_server = $promotional_server;
	}

	/**
	 * @return GmsServerParams
	 */
	function getTransactionalServer() : GmsServerParams {
		return $this->transactional_server;
	}

	/**
	 * @return GmsServerParams
	 */
	function getPromotionalServer() : GmsServerParams {
		return $this->promotional_server;
	}

	/**
	 * @param GmsServerParams $val
	 * @return self
	 */
	function withTransactionalServer(GmsServerParams $val) : self {
		$clone = clone $this;
		$clone->transactional_server = $val;
		return $clone;
	}

	/**
	 * @param GmsServerParams $val
	 * @return self
	 */
	function withPromotionalServer(GmsServerParams $val) : self {
		$clone = clone $this;
		$clone->promotional_server = $val;
		return $clone;
	}

	function jsonSerialize(){
		return [
			'transactional_server' => $this->transactional_server !== null ? $this->transactional_server->jsonSerialize() : null,
			'promotional_server' => $this->promotional_server !== null ? $this->promotional_server->jsonSerialize() : null,
		];
	}

	/**
	 * @param array $arr
	 * @return self
	 * @throws \InvalidArgumentException
	 */
	static function deserialize(array $arr) : GmsSettings {
		if(array_key_exists('transactional_server', $arr)){
			$transactional_server = $arr['transactional_server'];
		}else{
			throw new \InvalidArgumentException("Property 'transactional_server' not specified");
		}
		$transactional_server = $transactional_server !== null ? GmsServerParams::deserialize($transactional_server) : null;

		if(array_key_exists('promotional_server', $arr)){
			$promotional_server = $arr['promotional_server'];
		}else{
			throw new \InvalidArgumentException("Property 'promotional_server' not specified");
		}
		$promotional_server = $promotional_server !== null ? GmsServerParams::deserialize($promotional_server) : null;

		return new static($transactional_server, $promotional_server);
	}

}
