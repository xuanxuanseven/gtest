<?php
/**
 * Interface QRDataInterface
 *
 * @filesource   QRDataInterface.php
 * @created      01.12.2015
 * @package      Lephp\Helpers\QRCode\Data
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace Lephp\Helpers\QRCode\Data;

use Lephp\Helpers\QRCode\BitBuffer;

/**
 * @property string data
 * @property int    dataLength
 * @property int    mode
 */
interface QRDataInterface{

	/**
	 * @param \Lephp\Helpers\QRCode\BitBuffer $buffer
	 * @return void
	 */
	public function write(BitBuffer &$buffer);

	/**
	 * @param $type
	 *
	 * @return int
	 * @throws \Lephp\Helpers\QRCode\Data\QRCodeDataException
	 */
	public function getLengthInBits($type);

}
