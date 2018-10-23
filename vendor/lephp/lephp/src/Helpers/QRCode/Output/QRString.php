<?php
/**
 * Class QRString
 *
 * @filesource   QRString.php
 * @created      05.12.2015
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
class QRString extends QROutputBase implements QROutputInterface{

	/**
	 * @var \Lephp\Helpers\QRCode\Output\QRStringOptions
	 */
	protected $options;

	/**
	 * @var \Lephp\Helpers\QRCode\Output\QRStringOptions $outputOptions
	 * @throws \Lephp\Helpers\QRCode\Output\QRCodeOutputException
	 */
	public function __construct(QRStringOptions $outputOptions = null){
		$this->options = $outputOptions;

		if(!$this->options instanceof QRStringOptions){
			$this->options = new QRStringOptions;
		}

		if(!in_array($this->options->type, [QRCode::OUTPUT_STRING_TEXT, QRCode::OUTPUT_STRING_JSON, QRCode::OUTPUT_STRING_HTML], true)){
			throw new QRCodeOutputException('Invalid string output type!');
		}

	}

	/**
	 * @return string
	 */
	public function dump(){

		if($this->options->type === QRCode::OUTPUT_STRING_JSON){
			return json_encode($this->matrix);
		}

		else if($this->options->type === QRCode::OUTPUT_STRING_TEXT){
			$text = '';

			foreach($this->matrix as $row){
				foreach($row as $col){
					$text .= $col
						? $this->options->textDark
						: $this->options->textLight;
				}

				$text .= $this->options->textNewline;
			}

			return $text;
		}

		else if($this->options->type === QRCode::OUTPUT_STRING_HTML){
			$html = '';

			foreach($this->matrix as $row){
				// in order to not bloat the output too much, we use the shortest possible valid HTML tags
				$html .= '<'.$this->options->htmlRowTag.'>';
				foreach($row as $col){
					$tag = $col
						? 'b'  // dark
						: 'i'; // light

					$html .= '<'.$tag.'></'.$tag.'>';
				}

				if(!(bool)$this->options->htmlOmitEndTag){
					$html .= '</'.$this->options->htmlRowTag.'>';
				}

				$html .= PHP_EOL;
			}

			return $html;
		}

	}

}
