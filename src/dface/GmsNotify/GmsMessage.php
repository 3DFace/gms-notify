<?php

namespace dface\GmsNotify;

interface GmsMessage extends \JsonSerializable
{

	public function getType() : string;

}
