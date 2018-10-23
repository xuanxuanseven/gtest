<?php
/**
 * Class QRImage
 *
 * @filesource   QRImage.php
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
class QRImage extends QROutputBase implements QROutputInterface{

	/**
	 * @var \Lephp\Helpers\QRCode\Output\QRImageOptions $outputOptions
	 */
	protected $options;

	/**
	 * @var \Lephp\Helpers\QRCode\Output\QRImageOptions $outputOptions
	 */
	public function __construct(QRImageOptions $outputOptions = null){
		$this->options = $outputOptions;

		if(!$this->options instanceof QRImageOptions){
			$this->options = new QRImageOptions;
		}

		// clamp input (determine sane values!)
		$this->options->pixelSize = max(1, min(25, (int)$this->options->pixelSize));
		$this->options->marginSize = max(0, min(25, (int)$this->options->marginSize));

		foreach(['fgRed', 'fgGreen', 'fgBlue', 'bgRed', 'bgGreen', 'bgBlue'] as $val){
			$this->options->{$val} = max(0, min(255, (int)$this->options->{$val}));
		}

		if(!in_array($this->options->type, [QRCode::OUTPUT_IMAGE_PNG, QRCode::OUTPUT_IMAGE_JPG, QRCode::OUTPUT_IMAGE_GIF])){
			$this->options->type = QRCode::OUTPUT_IMAGE_PNG;
		}

		$this->options->transparent = (bool)$this->options->transparent && $this->options->type !== QRCode::OUTPUT_IMAGE_JPG;

		if(!in_array($this->options->pngCompression, range(-1, 9), true)){
			$this->options->pngCompression = -1;
		}

		if(!in_array($this->options->jpegQuality, range(0, 100), true)){
			$this->options->jpegQuality = 85;
		}

	}

	/**
	 * @return string
	 */
	public function dump(){
		$length = $this->pixelCount * $this->options->pixelSize + $this->options->marginSize * 2;
		$image = imagecreatetruecolor($length, $length);
		$foreground = imagecolorallocate($image, $this->options->fgRed, $this->options->fgGreen, $this->options->fgBlue);
		$background = imagecolorallocate($image, $this->options->bgRed, $this->options->bgGreen, $this->options->bgBlue);

		if($this->options->transparent){
			imagecolortransparent($image, $background);
		}

		imagefilledrectangle($image, 0, 0, $length, $length, $background);

		foreach($this->matrix as $r => $row){
			foreach($row as $c => $pixel){
				if($pixel){
					imagefilledrectangle($image,
						$this->options->marginSize +  $c      * $this->options->pixelSize,
						$this->options->marginSize +  $r      * $this->options->pixelSize,
						$this->options->marginSize + ($c + 1) * $this->options->pixelSize - 1,
						$this->options->marginSize + ($r + 1) * $this->options->pixelSize - 1,
						$foreground);
				}
			}
		}

		ob_start();

		switch($this->options->type){
			case QRCode::OUTPUT_IMAGE_PNG: imagepng ($image, $this->options->cachefile, (int)$this->options->pngCompression); break;
			case QRCode::OUTPUT_IMAGE_JPG: imagejpeg($image, $this->options->cachefile, (int)$this->options->jpegQuality); break;
			case QRCode::OUTPUT_IMAGE_GIF: imagegif ($image, $this->options->cachefile); break; /** Actually, it's pronounced "DJIFF". *hides* */
		}

		$imageData = ob_get_contents();
		imagedestroy($image);
		ob_end_clean();

		if((bool)$this->options->base64){
			$imageData = 'data:image/'.$this->options->type.';base64,'.base64_encode($imageData);
		}

		return $imageData;
	}

}
