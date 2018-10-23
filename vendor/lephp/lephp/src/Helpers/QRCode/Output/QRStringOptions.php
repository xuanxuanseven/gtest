<?php
/**
 * Class QRStringOptions
 *
 * @filesource   QRStringOptions.php
 * @created      08.12.2015
 * @package      Lephp\Helpers\QRCode\Output
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace Lephp\Helpers\QRCode\Output;
use Lephp\Helpers\QRCode\QRCode;

/**
 *
 */
class QRStringOptions{

	public $type = QRCode::OUTPUT_STRING_HTML;

	public $textDark = '#';

	public $textLight = ' ';

	public $textNewline = PHP_EOL;

	public $htmlRowTag = 'p';

	public $htmlOmitEndTag = true;

}
