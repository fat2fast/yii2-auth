<?php

namespace fat2fast\auth\models\form;

use lsat\otp\Otp;
use yii\base\Model;

class UserOtpForm extends Model
{
    public $id;
    public $secret;
    public $use_totp;
    public $username;
    public $otp;
    public $user;

    public function rules()
    {
        return [
            ['otp', 'number', 'min' => 000000, 'max' => 999999],
            ['otp', 'validateOtp'],
            ['secret', 'string'],
            ['use_totp', 'integer'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateOtp($attribute, $params)
    {
        if (!$this->hasErrors()) {
            /**
             * @var $otp Otp
             */
            $otp = \Yii::$app->otp;
            $check = $otp->valideteCode($this->{$attribute});
            if (!$check) {
                $this->addError($attribute, 'Incorrect otp.');
            }
        }
    }

    public function loadModel($user)
    {
        $this->user = $user;
        $this->id = $user->id;

        if (empty($this->secret)) {
            $this->secret = $user->secret_totp ?? null;
            if (empty($this->secret)) {
                $this->secret = strtoupper(\Yii::$app->otp->getSecret());
            }
        }
        $this->use_totp = $user->use_totp;
        $this->username = $user->username;
    }

    public function save()
    {
        if ($this->validate()) {
            $user = $this->user;
            $user->updateAttributes([
                'use_totp' => $this->use_totp,
                'secret_totp' => $this->secret,
            ]);
            return $user;
        }
        return false;
    }
}
