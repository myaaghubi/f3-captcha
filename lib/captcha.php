<?php

/**
 * @package F3 Captcha
 * @version 1.0
 * @author Mohammad Yaghobi <info@darbeweb.ir>
 * @copyright Copyright (c) 2020, Mohammad Yaghobi
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3
 */

class Captcha extends \Prefab {
  private $codeLength = 5;
  private static $caseSensitive = true;
  private $captchaWidth = 150;
  private $captchaHeight = 70;
  private $captchaFont = 'monofont.ttf';
  private $captchaFontScale = 0.65;
  private $color = "6d87cf";
  private $letters = '123456789abcdefghijklmnopkrstuvwxyz';
  private static $sessionKey = 'captcha_code';
  
  /**
   * generate the output
   *
   * @return string
   */
  function serve() {
    $f3=Base::instance();
    $f3->set('captcha.code', '<img src="'.$f3->get('BASE').'/captcha" width="'.$this->captchaWidth.'" height="'.$this->captchaHeight.'" >');
    $output = \Template::instance()->render('captcha/captcha.html');
    $f3->clear('captcha');
    return $output;
  }
  
  
  /**
   * make the captcha image
   *
   * @return void
   */
  function makeCaptchaImage() {
    $f3=Base::instance();
    $this->makeCaptchaCode($this->codeLength);
    $captcha = $f3->get('SESSION.'.self::$sessionKey);

    $fontSize = $this->captchaHeight * $this->captchaFontScale;
    $fontAddress = realpath($f3->get('UI')).'/fonts/'.$this->captchaFont;

    $captchaImage = @imagecreate($this->captchaWidth, $this->captchaHeight);

    $backgroundColor = imagecolorallocate($captchaImage, 255, 255, 255);
    $rgbColor = $this->hextorgb($this->color);
    $textColor = imagecolorallocate(
      $captchaImage,
      $rgbColor['red'],
      $rgbColor['green'],
      $rgbColor['blue']
    );

    $dotsNum = mt_rand(10, 40);
    for ($i = 0; $i < $dotsNum; $i++) {
      imagefilledellipse(
        $captchaImage,
        mt_rand(0, $this->captchaWidth),
        mt_rand(0, $this->captchaHeight),
        4,
        3,
        $textColor
      );
    }

    $linesNum = mt_rand(10, 40);
    for ($i = 0; $i < $linesNum; $i++) {
      imageline(
        $captchaImage,
        mt_rand(0, $this->captchaWidth),
        mt_rand(0, $this->captchaHeight),
        mt_rand(0, $this->captchaWidth),
        mt_rand(0, $this->captchaHeight),
        $textColor
      );
    }

    $text = imagettfbbox(
      $fontSize, 0, $fontAddress, $captcha
    );

    $x = ($this->captchaWidth - $text[4]) / 2;
    $y = ($this->captchaHeight - $text[5]) / 2;

    imagettftext(
      $captchaImage, $fontSize, 0, $x, $y, $textColor, $fontAddress, $captcha
    );

    ob_get_clean();
    ob_clean();

    header('Content-Type: image/jpeg');
    imagejpeg($captchaImage);
    imagedestroy($captchaImage);
  }
  
  
  /**
   * make random code for captcha
   *
   * @param  integer $charactersLength
   * @return string
   */
  function makeCaptchaCode($charactersLength) {
    if (empty($charactersLength)) {
      $charactersLength = $this->codeLength;
    }
    $captcha='';
    $letters = $this->letters;
    $lettersLength = strlen($letters);
    for ($i=0; $i<$charactersLength; $i++) {
      $char = substr($this->letters, mt_rand(0, $lettersLength - 1), 1);
      if (self::$caseSensitive && mt_rand(0, 10)<=3) {
        $char = strtoupper($char);
      }
      $captcha .= $char;
    }

    $f3=Base::instance();
    $f3->set('SESSION.'.self::$sessionKey, str_replace(' ', '', $captcha));
    return $captcha;
  }
  
  /**
   * verify the user entered security code
   *
   * @return boolean
   */
  static function verify() {
    $f3=Base::instance();
    $enteredCode = $f3->get('POST.captcha-entered-code');
    $savedCode = $f3->get('SESSION.'.self::$sessionKey);

    if (self::$caseSensitive) {
      return $savedCode == trim($enteredCode);
    }

    return strcasecmp($savedCode, trim($enteredCode))==0;
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
