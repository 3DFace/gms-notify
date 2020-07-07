<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\GmsNotify;

interface GmsMessage extends \JsonSerializable {

	/**
	 * @return string
	 */
	public function getType() : string;

}
