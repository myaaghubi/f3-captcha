<?php

/**
 * @package F3 Captcha
 * @version 1.1.0
 * @link http://github.com/myaghobi/f3-captcha Github
 * @author Mohammad Yaghobi <m.yaghobi.abc@gmail.com>
 * @copyright Copyright (c) 2020, Mohammad Yaghobi
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3
 */

class Captcha extends \Prefab {
  private $defaultLength = 5;
  private static $defaultCaseSensitiveMode = true;
  private $defaultWidth = 150;
  private $defaultHeight = 70;
  private $defaultFont = 'monofont.ttf';
  private $defaultFontScale = 0.45;
  private $defaultWavesEnable = false;
  private $defaultLetters = '123456789abcdefghijklmnopkrstuvwxyz';
  private static $defaultKey = 'captcha_code';

  /**
   * generate the output
   *
   * @return string
   */
  function serve($f3=null) {
    if (!$f3) {
      $f3=Base::instance();
    }
    
    $width = $f3->get('captcha.WIDTH')?:$this->defaultWidth;
    $height = $f3->get('captcha.HEIGHT')?:$this->defaultHeight;
    $key = $f3->get('captcha.KEY')?:self::$defaultKey;

    $f3->set('captcha.code', '<img src="' . $f3->get('BASE') . '/captcha" width="' . $width . '" height="' . $height . '" >');
    $f3->set('captcha.key', $key);
    $output = \Template::instance()->render('captcha/captcha.html');
    $f3->clear('captcha');
    return $output;
  }


  /**
   * make the captcha image
   *
   * @return void
   */
  function makeCaptchaImage($f3=null) {
    if (!$f3) {
      $f3=Base::instance();
    }
    $this->makeCaptchaCode($f3);
    
    $width = $f3->get('captcha.WIDTH')?:$this->defaultWidth;
    $height = $f3->get('captcha.HEIGHT')?:$this->defaultHeight;
    $font = $f3->get('captcha.FONT')?:$this->defaultFont;
    $fontScale = $f3->get('captcha.FONT_SCALE')?:$this->defaultFontScale;
    $waves = $f3->get('captcha.WAVES')?:$this->defaultWavesEnable;
    $key = $f3->get('captcha.KEY')?:self::$defaultKey;

    $captcha = $f3->get('SESSION.' . $key);

    $fontSize = $height * $fontScale;
    $fontAddress = realpath($f3->get('UI')) . '/fonts/' . $font;

    $captchaImage = @imagecreate($width, $height);

    $color = '6d87cf';
    $backgroundColor = imagecolorallocate($captchaImage, 255, 255, 255);
    $rgbColor = $this->hextorgb($color);
    $textColor = imagecolorallocate(
      $captchaImage,
      $rgbColor['red'],
      $rgbColor['green'],
      $rgbColor['blue']
    );

    $dotsNum = mt_rand(0, 5);
    for ($i = 0; $i < $dotsNum; $i++) {
      imagefilledellipse(
        $captchaImage,
        mt_rand(0, $width),
        mt_rand(0, $height),
        5,
        4,
        $textColor
      );
    }

    $linesNum = mt_rand(5, 10);
    for ($i = 0; $i < $linesNum; $i++) {
      imageline(
        $captchaImage,
        mt_rand(0, $width),
        mt_rand(0, $height),
        mt_rand(0, $width),
        mt_rand(0, $height),
        $textColor
      );
    }

    $text = imagettfbbox(
      $fontSize,
      0,
      $fontAddress,
      $captcha
    );

    $x = ($width - $text[4]) / 2;
    $y = ($height - $text[5]) / 2;

    imagettftext(
      $captchaImage,
      $fontSize,
      0,
      $x,
      $y,
      $textColor,
      $fontAddress,
      $captcha
    );

    ob_get_clean();
    ob_clean();

    if ($waves) {
      $this->wave_region($captchaImage, 0, 0, $width, $height, mt_rand(0, 7), 10);
    }

    header('Content-Type: image/jpeg');
    imagejpeg($captchaImage);
    imagedestroy($captchaImage);
  }


  /**
   * wave_region
   * Displays <a href="https://www.php.net/manual/en/function.imagecopy.php#72393">php.net</a>
   * @link https://www.php.net/manual/en/function.imagecopy.php#72393 php.net
   *
   * @param  resource $img
   * @param  int $x
   * @param  int $y
   * @param  int $width
   * @param  int $height
   * @param  float $amplitude
   * @param  int $period
   * @return void
   */
  function wave_region($img, $x, $y, $width, $height, $amplitude = 4.5, $period = 30) {
    // Make a copy of the image twice the size
    $mult = 2;
    $img2 = imagecreatetruecolor($width * $mult, $height * $mult);
    imagecopyresampled($img2, $img, 0, 0, $x, $y, $width * $mult, $height * $mult, $width, $height);

    // Wave it
    for ($i = 0; $i < ($width * $mult); $i += 2) {
      imagecopy(
        $img2,
        $img2,
        $x + $i - 2,
        $y + sin($i / $period) * $amplitude,
        $x + $i,
        $y,
        2,
        ($height * $mult)
      );
    }

    // Resample it down again
    imagecopyresampled($img, $img2, $x, $y, 0, 0, $width, $height, $width * $mult, $height * $mult);
    imagedestroy($img2);
  }

  /**
   * make random code for captcha
   *
   * @param  integer $charactersLength
   * @return string
   */
  function makeCaptchaCode($f3=null) {
    if (!$f3) {
      $f3=Base::instance();
    }

    $charactersLength = $f3->get('captcha.LENGTH')?:$this->defaultLength;
    $caseSensitive = $f3->get('captcha.CASE_SENSITIVE')?:self::$defaultCaseSensitiveMode;
    $key = $f3->get('captcha.KEY')?:self::$defaultKey;

    $captcha = '';
    $letters = $f3->get('captcha.LETTERS')?:$this->defaultLetters;
    $lettersLength = strlen($letters);
    for ($i = 0; $i < $charactersLength; $i++) {
      $char = substr($letters, mt_rand(0, $lettersLength - 1), 1);
      if ($caseSensitive && mt_rand(0, 10) <= 3) {
        $char = strtoupper($char);
      }
      $captcha .= $char;
    }

    $f3->set('SESSION.' . $key, str_replace(' ', '', $captcha));
    return $captcha;
  }

  /**
   * verify the user entered security code
   *
   * @return boolean
   */
  static function verify($f3=null) {
    if (!$f3) {
      $f3=Base::instance();
    }
    
    $key = $f3->get('captcha.KEY')?:self::$defaultKey;
    $caseSensitive = $f3->get('captcha.CASE_SENSITIVE')?:self::$defaultCaseSensitiveMode;

    $enteredCode = $f3->get('POST.'.$key);
    $savedCode = $f3->get('SESSION.' . $key);

    if ($caseSensitive) {
      return $savedCode == trim($enteredCode);
    }

    return strcasecmp($savedCode, trim($enteredCode)) == 0;
  }


  /**
   * hex color to rgb color
   *
   * @param  string $hexstring
   * @return array [
   *  "red"=>int,
   *  "green"=>int,
   *  "blue"=>int,
   * ]
   */
  function hextorgb($hexstring) {
    $integar = hexdec($hexstring);
    return array(
      "red" => 0xFF & ($integar >> 0x10),
      "green" => 0xFF & ($integar >> 0x8),
      "blue" => 0xFF & $integar
    );
  }
}
