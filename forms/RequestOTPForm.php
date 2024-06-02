<?php
/**
 * Created by PhpStorm.
 * User: tungmangvien
 * Date: 8/1/17
 * Time: 1:57 PM
 */

namespace fat2fast\auth\forms;

use fat2fast\auth\jobs\SendOTPJob;
use Yii;
use yii\base\Model;
use yii\web\User;

class RequestOTPForm extends Model
{

    public $phoneNumber;

    public function rules()
    {
        return [

            ['phoneNumber', 'filter', 'filter' => 'trim'],
            ['phoneNumber', 'required'],
            ['phoneNumber', 'match', 'pattern' => '/^0[0-9]{9,10}$/', 'message' => Yii::t('yii2-authz', 'Phone Number is invalid')]

        ];
    }

    public function attributeLabels()
    {
        return [
            'phoneNumber' => Yii::t('yii2-authz', 'Số điện thoại')
        ];
    }

    public function process()
    {
        if (!$this->validate()) {
            return [
                false, false, $this->getErrors()
            ];
        }
        $authzModule = Yii::$app->getModule('yii2-authz');
        $userIdentityClass = $authzModule->userIdentityClass;
        $otpConfigurations = $authzModule->otpConfigurations;

        $user = $userIdentityClass::findByUsername($this->phoneNumber);
        if ($user) {
            // do nothing
        } else {
            /** @var User $user */
            $user = new $userIdentityClass([
                'username' => $this->phoneNumber,
                'email' => uniqid('example_') . '@lcs.com.vn',
                'status' => $userIdentityClass::STATUS_ACTIVE
            ]);
            $user->generateAuthKey();
            $user->setPassword(Yii::$app->security->generateRandomKey());
            if (!$user->save()) {
                return [
                    false, false, $user->getErrors()
                ];
            }
        }

        $tokenLength = $otpConfigurations['tokenLength'];
        if ($user->token_expired_at == null || $user->token_expired_at < time()) {
            $user->updateAttributes([
                'verification_token' => !empty($otpConfigurations['tokenTest'])
                    ? $otpConfigurations['tokenTest']
                    : str_pad(mt_rand(0, 999999), $tokenLength, '0', STR_PAD_LEFT),
                'token_expired_at' => time() + $otpConfigurations['tokenExpiredInterval']
            ]);

            // send job to send sms
            // only send when creating a new one
            $job = new SendOTPJob([
                'toNumber' => $user->username,
                'message' => Yii::t('app', $otpConfigurations['otpMessage'], [
                    'token' => $user->verification_token
                ]),
                'uid' => $user->id
            ]);
            Yii::$app->queue->push($job);

        }

        return [
            true,
            [
                'verify_token' => $user->verification_token,
                'uid' => $user->id
            ],
            []
        ];
    }

}
