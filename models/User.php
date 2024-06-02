<?php

namespace fat2fast\auth\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $full_name
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property integer $failed_login_attempts
 * @property integer $login_lock_time
 * @property integer $force_change_pwd
 * @property integer $last_updated_password
 * @property integer $use_totp
 * @property string $secret_totp
 * @property bool $isSystemAdmin
 * @property string $displayRoles
 */
class User extends ActiveRecord implements IdentityInterface
{
    public string $loginVerificationSessionKey = 'loginVerification';

    public $keywords;
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    public $authKey;
    public $accessToken;
    public $roles = [];
//    private static $users = [
//        '100' => [
//            'id' => '100',
//            'username' => 'admin',
//            'password' => 'admin',
//            'authKey' => 'test100key',
//            'accessToken' => '100-token',
//        ],
//        '101' => [
//            'id' => '101',
//            'username' => 'demo',
//            'password' => 'demo',
//            'authKey' => 'test101key',
//            'accessToken' => '101-token',
//        ],
//    ];


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
//            $profile = new UserProfile([
//                'user_id' => $this->id,
//                'phone' => $this->username
//            ]);
//            $profile->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['keywords'],'safe'],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            [['failed_login_attempts', 'login_lock_time', 'force_change_pwd', 'last_updated_password', 'use_totp'], 'integer'],
            [['secret_totp'], 'unique'],
            [['full_name'], 'string'],
            ['secret_totp', 'default', 'value' => null],
            ['use_totp', 'default', 'value' => 0],
        ];
    }

    public function getUserAccessToken()
    {
        return $this->hasMany(UserAccessToken::class, ['id_user' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $userList = self::$users;
        if (!empty($userListParam = \Yii::$app->params['userList'] ?? null)) {
            $userList = $userListParam;
        }
        foreach ($userList as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }
        $user = self::find()->where([
            'user.status' => self::STATUS_ACTIVE
        ])->joinWith('userAccessToken uat')->andWhere([
            'uat.access_token' => $token,
            'uat.status' => UserAccessToken::STATUS_ACTIVE
        ])->one();
        if ($user) {
            return $user;
        }
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getDisplayName()
    {
        $displayName = $this->userProfile->getDisplayName();
        return !empty($displayName) ? $displayName : $this->username;
    }

    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function isLockedLogin()
    {
        if (empty($this->login_lock_time)) {
            return false;
        }
        return $this->login_lock_time > time();
    }

    public function hasTwoFaEnabled()
    {
        return $this->use_totp;
    }

    public function enableTwoFa($secret)
    {
        $this->updateAttributes([
            'use_totp' => 1,
            'secret_totp' => $secret,
        ]);
    }

    public function getTwoFaSecret()
    {
        return $this->secret_totp;
    }

    public function getTotpLabel()
    {
        switch ($this->use_totp) {
            case 1:
                return '<span class="badge bg-success">' . Yii::t("authz", "Enable") . '</span>';
            default:
                return '<span class="badge bg-danger">' . Yii::t("authz", "Disable") . '</span>';
        }
    }

    public function disableTwoFa()
    {
        $this->updateAttributes([
            'use_totp' => 0,
            'secret_totp' => null,
        ]);
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => self::getStatusLabel(self::STATUS_ACTIVE),
            self::STATUS_DELETED => self::getStatusLabel(self::STATUS_DELETED),
        ];
    }

    /**
     * @param $status
     *
     * @return null|string
     */
    public static function getStatusLabel($status)
    {
        switch ($status) {
            case self::STATUS_ACTIVE:
                return '<span class="badge bg-success">' . Yii::t('authz', 'Active') . '</span>';
                break;
            case self::STATUS_DELETED:
                return '<span class="badge bg-danger">' . Yii::t('authz', 'Inactive') . '</span>';
                break;
            default:
                return null;
        }
    }

    public function updateRoles()
    {
        try {

            $auth_manager = Yii::$app->getAuthManager();
            if ($auth_manager && !empty($this->roles)) {
                $auth_manager->revokeAll($this->id);

                if (count($this->roles) > 0) {
                    foreach ($this->roles as $r) {
                        $role = $auth_manager->getRole($r);
                        if ($auth_manager->getAssignment($r, $this->id) == null) {
                            $auth_manager->assign($role, $this->id);
                        }
                    }
                }
            }
        } catch (\Exception  $ex) {
            Yii::error($ex->getMessage());
            Yii::error($ex->getFile());
            Yii::error($ex->getLine());
            Yii::error($ex->getCode());
            Yii::error($ex->getTraceAsString());
        }
    }

    /**
     * @return bool
     */
    public function getIsSystemAdmin()
    {
        $role = Yii::$app->authManager->getRole('System Admin')->name;
        $rolesById = array_keys(Yii::$app->authManager->getRolesByUser($this->id));

        return in_array($role, $rolesById);
    }

    public function getDisplayRoles()
    {
        $roles = null;
        if (Yii::$app->authManager) {
            $authManager = Yii::$app->authManager;
            $assignedRoles = $authManager->getRolesByUser($this->id);
            if ($assignedRoles !== null && count($assignedRoles) > 0) {
                foreach ($assignedRoles as $assignedRole) {
                    $display_text = '<span class="badge bg-primary">' . $assignedRole->name . '</span>&nbsp;';
                    $roles .= $display_text;
                }
            }
        }

        return $roles;
    }

    public function search($params): ActiveDataProvider
    {
        $query = self::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 10, // Set page size as needed
            ],
        ]);

        $this->load($params);


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $keywords = $this->keywords ?? '';
        if ($keywords) {
            $query->andWhere([
                'OR',
                ['username' => trim($keywords)],
                ['email' => trim($keywords)],
            ]);
        }

        return $dataProvider;

    }
}
