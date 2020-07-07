<?php

namespace dface\GmsNotify;

class GmsServerParams implements \JsonSerializable
{

	/** @var string */
	private $login;
	/** @var string */
	private $password;
	/** @var string */
	private $url;

	public function __construct(string $login, string $password, string $url)
	{
		$this->login = $login;
		$this->password = $password;
		$this->url = $url;
	}

	public function getLogin() : string
	{
		return $this->login;
	}

	public function getPassword() : string
	{
		return $this->password;
	}

	public function getUrl() : string
	{
		return $this->url;
	}

	public function jsonSerialize()
	{
		return [
			'login' => $this->login,
			'password' => $this->password,
			'url' => $this->url,
		];
	}

	public static function deserialize(array $arr) : GmsServerParams
	{
		return new self($arr['login'], $arr['password'], $arr['url']);
	}

}
