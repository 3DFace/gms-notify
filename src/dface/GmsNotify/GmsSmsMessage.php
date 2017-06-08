<?php

namespace dface\GmsNotify;

class GmsSmsMessage implements \JsonSerializable {

	const MAX_ALPHA_NAME_LENGTH = 11;
	const MIN_TTL = 15;
	const MAX_TTL = 259200;
	const MAX_ASCII_MESSAGE = 765;
	const MAX_UTF_MESSAGE = 335;

	/** @var  string */
	private $text;
	/** @var int */
	private $ttl;
	/** @var  string */
	private $alpha_name;

	public function __construct(string $text, string $alpha_name, int $ttl){

		if($ttl > self::MAX_TTL || $ttl < self::MIN_TTL){
			throw new GmsMessageMalformed('ttl out of bounds');
		}

		if(strlen($alpha_name) > self::MAX_ALPHA_NAME_LENGTH){
			throw new GmsMessageMalformed('too long alpha_name');
		}

		if(preg_match('/^([\x20-\x7F]+)$/', $alpha_name) !== 1){
			throw new GmsMessageMalformed('invalid alpha_name');
		}

		$text = trim($text);
		if($text === ''){
			throw new GmsMessageMalformed('empty message');
		}

		$is_gsm = preg_match('/^([\r\n\x20-\x7F]+)$/', $text) === 1;
		$too_long = $is_gsm
			? strlen($text) > self::MAX_ASCII_MESSAGE
			: mb_strlen($text, 'utf-8') > self::MAX_UTF_MESSAGE;
		if($too_long){
			throw new GmsMessageMalformed('too long message');
		}

		$this->text = $text;
		$this->ttl = $ttl;
		$this->alpha_name = $alpha_name;
	}

	function getType() : string{
		return 'sms';
	}

	function jsonSerialize(){
		return [
			'text' => $this->text,
			'ttl' => $this->ttl,
			'alpha_name' => $this->alpha_name,
		];
	}

	static function deserialize($arr) : GmsSmsMessage{
		return new self(
			$arr['text'],
			$arr['ttl'],
			$arr['alpha_name']
		);
	}
}
