# cradle-captcha
Google Captcha Helpers

## Install

```
composer require cradlephp/cradle-captcha
```

Then in `/bootstrap.php`, add

```
->register('cradlephp/cradle-captcha')
```

## Setup

Go to [https://www.google.com/recaptcha/](https://www.google.com/recaptcha/) and
register for a token and secret.

Open `/config/services.php` and add

```
'captcha-main' => array(
    'token' => '<Google Token>',
    'secret' => '<Google Secret>'
),
```

## Usage

In any of your routes add the following code.

```
cradle()->trigger('captcha-load', $request, $response);
```

The CSRF token will be found in `$request->getStage('captcha')`. In your form
template, be sure to add this key in a hidden field like the following.

```
<script src="https://www.google.com/recaptcha/api.js"></script>
<div class="g-recaptcha" data-sitekey="{{captcha}}"></div>
```

When validating this form in a route you can use the following

```
cradle()->trigger('captcha-validate', $request, $response);
```

If there is an error, it will be found in the response error object message.
You can check this using the following.

```
if($response->isError()) {
    $message = $response->getMessage();
    //report the error
}
```
