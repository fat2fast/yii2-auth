<?php

namespace fat2fast\auth\forms;

use fat2fast\auth\models\User;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;
    public $reCaptcha;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
//            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator2::className(),
//                'secret' => Yii::$app->params['reCaptcha']['secretV2'],
//                'uncheckedMessage' => Yii::$app->params['reCaptcha']['uncheckedMessage'],
//            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {

        return [
            'username' => Yii::t('app','Username'),
            'password' => Yii::t('app','Password'),
            'rememberMe' => Yii::t('app','Remember Me'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if ($user && $user->isLockedLogin()) {
                $this->addError($attribute, Yii::t('authz', 'Login fail. Please contact admin.'));
                return;
            }

            if (!$user || !$user->validatePassword($this->$attribute)) {

                if ($user) {

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
                }

                $this->addError($attribute, Yii::t('authz','Incorrect username or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
//            $duration = Yii::$app->params['user.loginDuration'];
            $duration = 180;
            $loggedIn = Yii::$app->user->login($this->getUser(), $this->rememberMe ? $duration : 0);
            if ($loggedIn) {
                User::updateAll([
                    'failed_login_attempts' => 0,
                    'login_lock_time' => null
                ], [
                    'username' => $this->getUser()->username
                ]);
            }
            return $loggedIn;
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
