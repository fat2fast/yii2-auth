<?php
/**
 * Created by PhpStorm.
 * User: tungmangvien
 * Date: 8/1/17
 * Time: 1:57 PM
 */

namespace fat2fast\auth\forms;

use Yii;
use yii\base\Model;

class VerifyOTPForm extends Model
{

    public $phoneNumber;
    public $otpToken;

    private $_user;
    public $reCaptcha;
    public function rules()
    {
        return [

            ['phoneNumber', 'filter', 'filter' => 'trim'],
            ['phoneNumber', 'required'],
            ['phoneNumber', 'match', 'pattern' => '/^0[0-9]{9,10}$/', 'message' => Yii::t('yii2-authz', 'Phone Number is invalid')],

            ['otpToken', 'required'],
            ['otpToken', 'filter', 'filter' => 'trim'],
            ['otpToken', 'validateOtpToken'],
            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator3::className(),
                'secret' => Yii::$app->params['reCaptcha']['secret_key_v3'],
                'threshold' => Yii::$app->params['reCaptcha']['threshold'],
                'action' => 'login',
                'message' => Yii::t('yii2-authz', 'Verify Captcha failed')
            ],
        ];
    }

    public function validateOtpToken($attribute, $params)
    {
        if (!$this->hasErrors()) {
            date_default_timezone_set("Asia/Ho_Chi_Minh");

            if ($this->otpToken != $this->_user->verification_token) {
                $this->addError($attribute, Yii::t('yii2-authz', 'Incorrect OTP token.'));
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'phoneNumber' => Yii::t('yii2-authz', 'Số điện thoại'),
            'otpToken' => Yii::t('yii2-authz', 'Mã OTP')
        ];
    }

    public function process()
    {
        $authzModule = Yii::$app->getModule('yii2-authz');
        $userIdentityClass = $authzModule->userIdentityClass;
        $this->_user = $userIdentityClass::findByUsername($this->phoneNumber);

        if (!$this->validate()) {
            return [
                false, false, $this->getErrors()
            ];
        }

        if ($this->_user) {
            return [
                true,
                $this->_user,
                []
            ];
        }

        return [
            false, false, [
                'username' => 'Unknown error'
            ]
        ];
    }

    public function getRedirectUrl() {
        return Yii::$app->getHomeUrl();
    }

    public function getRemainSeconds()
    {
        $authzModule = Yii::$app->getModule('yii2-authz');
        $userIdentityClass = $authzModule->userIdentityClass;
        $this->_user = $userIdentityClass::findByUsername($this->phoneNumber);
        if (!$this->_user) {
            return 0;
        }
        return $this->_user->token_expired_at - time();
    }

}
