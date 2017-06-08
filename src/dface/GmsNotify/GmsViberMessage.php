<?php

namespace dface\GmsNotify;

class GmsViberMessage implements GmsMessage {

	const MAX_TEXT_LENGTH = 1000;
	const MAX_CAPTION_LENGTH = 20;
	const MAX_ACTION_LENGTH = 255;
	const MAX_IMG_LENGTH = 255;
	const MIN_TTL = 15;
	const MAX_TTL = 86400;
	const MAX_IOS_EXPIRATION_TEXT_LENGTH = 1000;

	/** @var  string */
	private $text;
	/** @var int */
	private $ttl;
	/** @var  string */
	private $caption;
	/** @var  string */
	private $action;
	/** @var  string */
	private $img;
	/** @var  string */
	private $ios_expiration_text;

	public function __construct(
		string $text,
		int $ttl,
		string $caption = null,
		string $action = null,
		string $img = null,
		string $ios_expiration_text = null
	){

		$text = trim($text);

		if(mb_strlen($text, 'utf-8') > self::MAX_TEXT_LENGTH){
			throw new GmsMessageMalformed('too long message');
		}

		if($text === ''){
			throw new GmsMessageMalformed('empty message');
		}

		if(!empty($caption) && mb_strlen($caption, 'utf-8') > self::MAX_CAPTION_LENGTH){
			throw new GmsMessageMalformed('too long caption');
		}

		if(!empty($action)){
			if(mb_strlen($action, 'utf-8') > self::MAX_ACTION_LENGTH){
				throw new GmsMessageMalformed('too long action');
			}
			if(filter_var($action, FILTER_VALIDATE_URL) === false){
				throw new GmsMessageMalformed('action is not valid url');
			}
		}

		if(!empty($img)){
			if(mb_strlen($img, 'utf-8') > self::MAX_IMG_LENGTH){
				throw new GmsMessageMalformed('too long img url');
			}
			if(filter_var($img, FILTER_VALIDATE_URL) === false){
				throw new GmsMessageMalformed('img url is not valid');
			}
		}

		if($ttl > self::MAX_TTL || $ttl < self::MIN_TTL){
			throw new GmsMessageMalformed('ttl out of bounds');
		}

		if(!empty($ios_expiration_text)){
			if(mb_strlen($ios_expiration_text, 'utf-8') > self::MAX_IOS_EXPIRATION_TEXT_LENGTH){
				throw new GmsMessageMalformed('too long ios expiration text');
			}
		}

		$this->text = $text;
		$this->ttl = $ttl;
		$this->caption = $caption;
		$this->action = $action;
		$this->img = $img;
		$this->ios_expiration_text = $ios_expiration_text;
	}

	function getType() : string{
		return 'viber';
	}

	function jsonSerialize(){

		$channel = [
			'text' => $this->text,
			'ttl' => $this->ttl,
		];

		if($this->caption && $this->action){
			$channel['caption'] = $this->caption;
			$channel['action'] = $this->action;
			if($this->img){
				$channel['img'] = $this->img;
			}
		}

		if($this->ios_expiration_text){
			/** @noinspection SpellCheckingInspection */
			$channel['ios_expirity_text'] = $this->ios_expiration_text;
		}

		return $channel;
	}

	static function deserialize($arr) : GmsViberMessage{
		/** @noinspection SpellCheckingInspection */
		return new self(
			$arr['text'],
			$arr['ttl'],
			$arr['caption'] ?? null,
			$arr['action'] ?? null,
			$arr['img'] ?? null,
			$arr['ios_expirity_text'] ?? null
		);
	}

}
