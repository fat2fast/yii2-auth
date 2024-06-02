<?php
/**
 * Created by PhpStorm.
 * User: tungmang
 * Date: 8/14/19
 * Time: 9:01 AM
 */

namespace fat2fast\auth\helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use yii\helpers\StringHelper;

class SMSHelpers
{

    /**
     * @param $phoneNumber | +84909699756
     * @param $text
     * @param bool $debug
     *
     * @return array
     */
    public static function sendTo($phoneNumber, $text, $debug = false)
    {
        if (YII_ENV == 'dev') {
            \Yii::warning('In dev mode');
            return [false, 'In dev mode | we dont send sms'];
        }

        if (empty($phoneNumber) || empty($text)) {
            \Yii::warning('Phone or Text is empty');
            return [false, 'Phone or Text is empty'];
        }

        if (count_chars($phoneNumber) < 10) {
            \Yii::warning('Phone Number is invalid');
            return [false, 'Phone Number is invalid. ' . $phoneNumber];
        }

        $text = StringHelper::truncate($text, 160, '');

        $params = [
            'user' => \Yii::$app->params['smsGateway']['username'],
            'password' => \Yii::$app->params['smsGateway']['password'],
            'mobileNo' => $phoneNumber,
            'smsText' => $text
        ];

        $linkToFetch = \Yii::$app->params['smsGateway']['endpoint'] . "?" . http_build_query($params);

        if ($debug) {
            \Yii::warning("Link: " . $linkToFetch);
        }

        $client = new Client();
        $response = $client->get($linkToFetch);

        if ($response->getStatusCode() == 200) {
            return [
                true, $response->getBody()->getContents()
            ];
        } else {
            return [
                false, $response->getBody()->getContents()
            ];
        }
    }


}
