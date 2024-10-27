<?php

namespace fat2fast\auth\models\form;

use fat2fast\auth\models\User;
use fat2fast\otp\Otp;
use Yii;
use yii\base\Model;
use yii\web\IdentityInterface;

/**
 * Enable Two-Factor Authentication form
 */
class TwoFaForm extends Model
{
    /**
     * @var string Scenario defaults to "default". Otherwise, override the constructor or init.
     * @see https://github.com/yiisoft/yii2/issues/12707
     */
    const SCENARIO_ACTIVATE = self::SCENARIO_DEFAULT;
    const SCENARIO_LOGIN = 'login';

    /** The generated secret */
    public string $secret;

    /** The code entered by the user */
    public string $code = '';

    /** Keeps the user logged in. */
    public bool $rememberMe = true;

    /** Time window in which the key is valid. Leave this null to use the default component setting. */
    public ?int $window = null;

    private IdentityInterface $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['secret', 'code'], 'required'],
            ['code', 'filter', 'filter' => 'trim'],
            ['code', 'string', 'min' => 6],
            ['rememberMe', 'required', 'on' => self::SCENARIO_LOGIN],
            ['rememberMe', 'boolean', 'on' => self::SCENARIO_LOGIN],
        ];
    }

    public function getUser(): IdentityInterface
    {
        return $this->_user;
    }

    public function setUser(IdentityInterface $user)
    {
        /**
         * @var Otp $otp
         */
        $otp = \Yii::$app->otp;
        $this->_user = $user;
        $this->secret = $user->hasTwoFaEnabled() ? $user->getTwoFaSecret() : strtoupper($otp->getSecret());
    }

    /**
     * Enables Two Factor Authentication for a user.
     *
     * @return bool
     * @throws \Exception
     */
    public function save(): bool
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->enableTwoFa($this->secret);

            return !$user->hasErrors();
        }

        return false;
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        $user = $this->getUser();
        if ($this->validate()) {
            $duration = Yii::$app->params['user.loginDuration'];
            $loggedIn = Yii::$app->user->login($user, $this->rememberMe ? $duration : 0);
            if ($loggedIn) {
                User::updateAll([
                    'failed_login_attempts' => 0,
                    'login_lock_time' => null
                ], [
                    'username' => $user->username
                ]);
            }
            return $loggedIn;
        }
        User::updateAllCounters([
            'failed_login_attempts' => 1
        ], [
            'username' => $user->username
        ]);
        if (($user->failed_login_attempts + 1) >= Yii::$app->params['user.loginMaxAttempts']) {
            User::updateAll([
                'login_lock_time' => time() + Yii::$app->params['user.loginLockDuration']
            ], [
                'username' => $user->username
            ]);
        }
        return false;
    }
}
