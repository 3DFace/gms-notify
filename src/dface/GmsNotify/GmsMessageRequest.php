<?php

namespace dface\GmsNotify;

class GmsMessageRequest implements \JsonSerializable
{

	private GmsPhone $phone;
	/** @var GmsMessage[] */
	private array $messages;
	private ?string $extra_id;
	private bool $is_promotional;
	private ?string $callback_url;
	private ?string $tag;
	private ?\DateTimeImmutable $start_time;

	private const MAX_TAG_LENGTH = 63;

	public function __construct(
		GmsPhone $phone,
		bool $is_promotional,
		?string $callback_url,
		?string $tag,
		?\DateTimeImmutable $start_time,
		array $messages,
		?string $extra_id
	) {
		if ($callback_url && \filter_var($callback_url, FILTER_VALIDATE_URL) === false) {
			throw new \InvalidArgumentException('callback_url is not valid url');
		}

		if ($tag && \mb_strlen($tag, 'utf-8') > self::MAX_TAG_LENGTH) {
			throw new \InvalidArgumentException('too long tag');
		}

		$this->phone = $phone;
		$this->messages = $messages;
		$this->is_promotional = $is_promotional;
		$this->callback_url = $callback_url;
		$this->tag = $tag;
		$this->start_time = $start_time;
		$this->extra_id = $extra_id;
	}

	public function getExtraId() : ?string
	{
		return $this->extra_id;
	}

	public function getTag() : ?string
	{
		return $this->tag;
	}

	public function isPromotional() : bool
	{
		return $this->is_promotional;
	}

	public function jsonSerialize() : array
	{
		$request = [
			'phone_number' => $this->phone->getPhone(),

			'is_promotional' => $this->is_promotional,
		];

		if (null !== $this->extra_id) {
			$request['extra_id'] = $this->extra_id;
		}

		if (null !== $this->callback_url) {
			$request['callback_url'] = $this->callback_url;
		}

		if (null !== $this->tag) {
			$request['tag'] = $this->tag;
		}

		if ($this->start_time !== null) {
			$request['start_time'] = $this->start_time->format('Y-m-d H:i:s');
		}

		$channels = [];
		$request['channel_options'] = [];
		foreach ($this->messages as $m) {
			$type = $m->getType();
			$channels[] = $type;
			$request['channel_options'][$type] = $m;
		}
		$request['channels'] = $channels;

		return $request;
	}

	public static function deserialize($arr) : self
	{
		static $classMap = [
			'sms' => GmsSmsMessage::class,
			'viber' => GmsViberMessage::class,
		];

		$phone = new GmsPhone($arr['phone_number']);

		$messages = \array_map(static function ($ch) use ($classMap, $arr) {
			/** @noinspection PhpUndefinedMethodInspection */
			return $classMap[$ch]::deserialize($arr['channel_options'][$ch]);
		}, $arr['channels']);

		return new self(
			$phone,
			$arr['is_promotional'],
			$arr['callback_url'] ?? null,
			$arr['tag'] ?? null,
			$arr['start_time'] === null ? null : \DateTimeImmutable::createFromFormat('Y-m-d H:i:s',
				$arr['start_time']),
			$messages,
			$arr['extra_id'] ?? null);
	}
}
