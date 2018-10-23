<?php
/**
 * Interface QROutputInterface,
 *
 * @filesource   QROutputInterface.php
 * @created      02.12.2015
 * @package      Lephp\Helpers\QRCode\Output
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace Lephp\Helpers\QRCode\Output;

/**
 *
 */
interface QROutputInterface{

	/**
	 * @return mixed
	 */
	public function dump();

	/**
	 * @param array $matrix
	 *
	 * @return $this
	 * @throws \Lephp\Helpers\QRCode\Output\QRCodeOutputException
	 */
	public function setMatrix(array $matrix);

}
