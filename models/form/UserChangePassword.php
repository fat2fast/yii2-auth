<?php

namespace fat2fast\auth\models\form;

use Yii;
use yii\base\Model;
use yii\web\IdentityInterface;

class UserChangePassword extends Model
{
    public $old_password;
    public $new_password;
    public $retype_password;

    /**
     * @var IdentityInterface $user
     */
    public $user;

    public function loadModel($user)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['old_password'], 'required','message' => Yii::t('authz','Old Password is required')],
            [['new_password'], 'required','message' => Yii::t('authz','New Password is required')],
            [['retype_password'], 'required','message' => Yii::t('authz','Retype Password is required')],
            ['old_password', 'validatePassword'],
            ['new_password', 'match', 'pattern' => '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+{};:,<.>]).{8,}$/', 'message' => Yii::t('authz', 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character, and be at least 8 characters long.')], // Strength password
            [['retype_password'], 'compare', 'compareAttribute' => 'new_password', 'message' => Yii::t('authz', 'Retype password must matched new password.')],
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
            $user = $this->user;

            if (!$user || !$user->validatePassword($this->old_password)) {
                $this->addError($attribute, 'Incorrect password.');
            }
        }
    }

    public function save()
    {
        if ($this->validate()) {
            if ($this->user != null) {
                $user           = $this->user;
                /**
                 * @var $user IdentityInterface
                 */

                if (!empty($this->new_password) && !empty($this->retype_password)) {
                    $user->setPassword($this->new_password);
                }
                $user->generateAuthKey();
                if ($user->force_change_pwd === 1) {
                    $user->updateAttributes(['force_change_pwd' => 0]);
                }
                $user->last_updated_password = time();

                if ($user->save()) {
                    return $user;
                }
                Yii::error($user->errors);
            }

        }

        return null;
    }
}
