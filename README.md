# F3 Captcha
A better captcha for Fat-Free Framework.

The default captcha template contains the [bootstrap] class, which can look like this:

![captcha](screenshot/shot1.jpg?raw=true "F3 Captcha Screentshot 1") ![captcha](screenshot/shot2.jpg?raw=true "F3 Captcha Screentshot 2")

## Usage

### 1. Install

Copy captcha.php into your `lib/` folder & copy content of `ui/` into your `ui/` folder.

### 2. Routing

To show the captcha, you need to add a new route.

``` php
$f3->route('GET /captcha', 'Captcha->makeCaptchaImage');
```

### 3. Serve
Within your controller you need to print the captcha. 

``` php
$f3->set('captcha', \Captcha::instance()->serve());
```

And in your HTML template:

``` html
{{@captcha|raw}}
```

## License

You are allowed to use this plugin under the terms of the GNU General Public License version 3 or later.

Copyright (C) 2020 Mohammad Yaghobi