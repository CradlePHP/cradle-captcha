<?php //-->
use Cradle\Curl\CurlHandler;

use Cradle\Http\Request;
use Cradle\Http\Response;

/**
 * Loads captcha token in stage
 *
 * @param *Request  $request
 * @param *Response $response
 */
$cradle->on('captcha-load', function (Request $request, Response $response) {
    $config = $this->package('global')->service('captcha-main');

    //if no config
    if (!$config
        || !isset($config['token'], $config['secret'])
        || $config['token'] === '<GOOGLE CAPTCHA TOKEN>'
        || $config['secret'] === '<GOOGLE CAPTCHA SECRET>'
    )
    {
        return;
    }

    //render the key
    $key = $config['token'];
    $request->setStage('captcha', $key);
});

/**
 * Validates Captcha
 *
 * @param *Request  $request
 * @param *Response $response
 */
$cradle->on('captcha-validate', function (Request $request, Response $response) {
    $actual = $request->getStage('g-recaptcha-response');
    $config = $this->package('global')->service('captcha-main');

    //if no config
    if (!$config
        || !isset($config['token'], $config['secret'])
        || $config['token'] === '<GOOGLE CAPTCHA TOKEN>'
        || $config['secret'] === '<GOOGLE CAPTCHA SECRET>'
    )
    {
        //let it pass
        return;
    }

    $result = CurlHandler::i()
        ->setUrl('https://www.google.com/recaptcha/api/siteverify')
        ->verifyHost(false)
        ->verifyPeer(false)
        ->setPostFields(http_build_query(array(
            'secret' => $config['secret'],
            'response' => $actual
        )))
        ->getJsonResponse();

    if (!isset($result['success']) || !$result['success']) {
        //prepare to error
        $message = $this->package('global')->translate('Captcha Failed');
        $response->setError(true, $message);
    }

    //it passed
});
