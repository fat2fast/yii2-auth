<?php

namespace fat2fast\auth\models\form;

use fat2fast\auth\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

class UserForm extends User
{
    public $roles;
    public $user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => User::class, 'message' => Yii::t('authz', 'This username has already been taken.'), 'when' => function ($model) {
                if($this->user){
                    return $model->username != $this->user->username; // or other function for get current username
                }
                return true;
            }],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['full_name', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => Yii::t('authz', 'This email address has already been taken.'),
                'when' => function ($model) {
                if($this->user){
                    return $model->email != $this->user->email; // or other function for get current username
                }
                return true;
            },],
            [['status'], 'integer'],
            [['roles'], 'safe'],
        ];
    }

    public function create()
    {
        if ($this->validate()) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                /**
                 * @var IdentityInterface $userIdentityClass
                 */
                $userIdentityClass = Yii::$app->controller->module->userIdentityClass;
                $user = new $userIdentityClass();
                $user->username = $this->username;
                $user->full_name = $this->full_name;
                $user->email = $this->email;
                $user->status = self::STATUS_ACTIVE;
                $user->force_change_pwd = 1;
                $user->roles = $this->roles;


                $password = getenv("DEFAULT_PASSWORD");
                $user->setPassword($password);
                $user->generateAuthKey();

                if ($user->save()) {
                    $user->updateRoles();

                    $transaction->commit();
                    return $user;
                } else {
                    Yii::error($user->errors);
                }

                Yii::debug($user->getFirstErrors());

                $transaction->rollBack();
            } catch (\Exception $ex) {
                $transaction->rollBack();
                Yii::error($ex->getMessage());
                Yii::error($ex->getFile());
                Yii::error($ex->getLine());
                Yii::error($ex->getCode());
                Yii::error($ex->getTraceAsString());
            }
        }

        return null;
    }
    public function edit()
    {
        if ($this->validate()) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                $user = $this->user;
                $user->username = $this->username;
                $user->email = $this->email;
                $user->full_name = $this->full_name;
                $user->roles = $this->roles;

                if ($user->save()) {
                    $user->updateRoles();

                    $transaction->commit();
                    return $user;
                } else {
                    Yii::error($user->errors);
                }

                Yii::debug($user->getFirstErrors());

                $transaction->rollBack();
            } catch (\Exception $ex) {
                Yii::error($ex->getMessage());
                Yii::error($ex->getFile());
                Yii::error($ex->getLine());
                Yii::error($ex->getCode());
                Yii::error($ex->getTraceAsString());
            }
        }

        return null;
    }
    public function loadModel($user)
    {
        $this->user = $user;
        $this->id = $user->id;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->full_name = $user->full_name;

        $authManager = Yii::$app->authManager;
        if ($authManager !== null) {
            $roles = $authManager->getRolesByUser($user->id);
            if ($roles !== null && !empty($roles) && count($roles) > 0) {
                $this->roles = ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser($user->id), 'name');
            }
        }
    }
}
