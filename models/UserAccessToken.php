<?php


namespace fat2fast\auth\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_access_token}}".
 *
 * @property int                 $id_user_access_token
 * @property int                 $id_user
 * @property string              $access_token
 * @property string              $device_token
 * @property string              $device_agent
 * @property string              $device_name
 * @property string              $client_ip
 * @property int                 $status
 * @property int                 $created_at
 * @property int                 $updated_at
 *
 * @property \app\models\User $user
 * @property string              $device_os
 * @property string              $os_version
 */
class UserAccessToken extends \yii\db\ActiveRecord
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE  = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_access_token}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'created_at', 'updated_at'], 'integer'],
            [['access_token', 'device_agent', 'device_name', 'client_ip', 'device_token'], 'string', 'max' => 255],
            [['access_token'], 'unique', 'filter' => function($q) {
                return $q->andWhere(['status' => 10]);
            }]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_user_access_token' => Yii::t('common', 'Id User Access Token'),
            'id_user'              => Yii::t('common', 'Id User'),
            'access_token'         => Yii::t('common', 'Access Token'),
            'device_agent'         => Yii::t('common', 'Device Agent'),
            'device_name'          => Yii::t('common', 'Device Name'),
            'client_ip'            => Yii::t('common', 'IP'),
            'created_at'           => Yii::t('common', 'Created At'),
            'updated_at'           => Yii::t('common', 'Updated At'),
            'device_token'         => Yii::t('common', 'Device Token'),
            'device_os'         => Yii::t('common', 'Device OS'),
            'os_version'         => Yii::t('common', 'OS Version'),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @return \app\models\User|\yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id_user' => 'id_user']);
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE  => self::getStatusLabel(self::STATUS_ACTIVE),
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
                return Yii::t('common', 'Active');
                break;
            case self::STATUS_DELETED:
                return Yii::t('common', 'Deleted');
                break;
            default:
                return null;
        }
    }

    public static function invalidateToken($token)
    {
        $model = self::findOne(['access_token' => $token, 'id_user' => Yii::$app->user->id, 'status' => self::STATUS_ACTIVE]);
        if ($model !== null) {
            $model->status = self::STATUS_DELETED;
            $model->save();
        }
    }
}
