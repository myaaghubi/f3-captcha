# F3 Captcha
A better captcha for Fat-Free Framework.

![screenshot-english](screenshots/shot1.jpg?raw=true "F3 Captcha Screentshot-English Font") ![screenshot-persian](screenshots/shot2.jpg?raw=true "F3 Captcha Screentshot-Persian Font")

## Config
This plugin is configurable via config file:
``` ini
[captcha]
LENGTH=5
CASE_SENSITIVE=TRUE
WIDTH=150
HEIGHT=70
FONT=monofont.ttf
FONT_SCALE=0.65
WAVES=TRUE
LETTERS=123456789abcdefghijklmnopkrstuvwxyz
KEY=captcha_code
```
The above config is the default, you can ignore/remove each one you don't need to change.

## Usage

### 1. Install

Copy `captcha.php` into your `lib/` folder & copy content of `ui/` into your `ui/` folder.

### 2. Routing

To show the captcha, you need to add a new route:

``` php
$f3->route('GET /captcha', 'Captcha->makeCaptchaImage');
```

### 3. Serve
Within your controller you need to serve the captcha:

``` php
$f3->set('captcha', \Captcha::instance()->serve());
```

And in your HTML template:

``` html
{{@captcha|raw}}
```

### 4. Verification
Finally you need to verify entered security code:

``` php
if (\Captcha::verify()) {
...
```

## Customization

### - Style
Default captcha template contains the bootstrap class which can change by developer, check out `ui/captcha/captcha.html`.

### - Font
The default font is `monofont.ttf`, you can add your font in `ui/fonts/` and set the font name in config file.


## License

You are allowed to use this plugin under the terms of the GNU General Public License version 3 or later.

Copyright (C) 2020 Mohammad Yaghobi