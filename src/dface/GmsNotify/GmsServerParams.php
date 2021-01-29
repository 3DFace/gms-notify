<?php

namespace dface\GmsNotify;

class GmsServerParams implements \JsonSerializable
{

	private string $login;
	private string $password;
	private string $url;

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

	public function jsonSerialize() : array
	{
		return [
			'login' => $this->login,
			'password' => $this->password,
			'url' => $this->url,
		];
	}

	public static function deserialize(array $arr) : self
	{
		return new self($arr['login'], $arr['password'], $arr['url']);
	}

}
