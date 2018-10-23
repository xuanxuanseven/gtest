<?php
/**
 * Class QRDataBase
 *
 * @filesource   QRDataBase.php
 * @created      25.11.2015
 * @package      Lephp\Helpers\QRCode\Data
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace Lephp\Helpers\QRCode\Data;
use Lephp\Helpers\QRCode\QRCode;

/**
 *
 */
class QRDataBase{

	/**
	 * @var string
	 */
	public $data;

	/**
	 * @var int
	 */
	public $dataLength;

	/**
	 * @var array
	 */
	protected $lengthBits = [0, 0, 0];

	/**
	 * QRDataBase constructor.
	 *
	 * @param string $data
	 */
	public function __construct($data){
		$this->data = $data;
		$this->dataLength = strlen($data);
	}

	/**
	 * @param int $type
	 *
	 * @return int
	 * @throws \Lephp\Helpers\QRCode\Data\QRCodeDataException
	 * @see QRCode::createData()
	 * @codeCoverageIgnore
	 */
	public function getLengthInBits($type){

		switch(true){
			case $type >= 1 && $type <= 9:
				return $this->lengthBits[0]; //  1 -  9
			case $type <= 26:
				return $this->lengthBits[1]; // 10 - 26
			case $type <= 40:
				return $this->lengthBits[2]; // 27 - 40
			default:
				throw new QRCodeDataException('$type: '.$type);
		}

	}

}
