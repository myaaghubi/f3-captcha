# F3 Captcha
A better captcha for Fat-Free Framework.

The default captcha template contains the bootstrap class, which can look like this:

![screenshot1](screenshots/shot1.jpg?raw=true "F3 Captcha Screentshot 1") ![screenshot2](screenshots/shot2.jpg?raw=true "F3 Captcha Screentshot 2")

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
There is some properties in `captcha.php` you may prefer to change. Also for more customization, check out `ui/captcha/captcha.html`.

### - Font
The default font is `monofont.ttf`, you can add your font in `ui/fonts/` and set the font name in `captcha.php`.


## License

You are allowed to use this plugin under the terms of the GNU General Public License version 3 or later.

Copyright (C) 2020 Mohammad Yaghobi