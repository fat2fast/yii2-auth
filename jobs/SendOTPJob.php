<?php
/**
 * Created by PhpStorm.
 * User: tungmang
 * Date: 11/8/19
 * Time: 3:06 PM
 */

namespace fat2fast\auth\jobs;


use fat2fast\auth\helpers\SMSHelpers;
use fat2fast\auth\models\SmsNotification;
use Yii;
use yii\base\BaseObject;
use yii\helpers\BaseConsole;
use yii\helpers\VarDumper;

class SendOTPJob extends BaseObject implements \yii\queue\JobInterface
{
    public $toNumber;
    public $message;

    public $uid;
    public $type = 'user'; // user | customer

    public function execute($queue)
    {

        Yii::warning([
            $this->toNumber, $this->uid, $this->message
        ]);
        $smsRecord = new SmsNotification();
        $smsRecord->type = $this->type;
        $smsRecord->uid = $this->uid;
        $smsRecord->message = $this->message;
        $smsRecord->to = self::getPhoneNumberToSend($this->toNumber);
        $smsRecord->status = SmsNotification::STATUS_SENDING;
        if ($smsRecord->save()) {
            list($hasSent, $msg) = SMSHelpers::sendTo($smsRecord->to, $smsRecord->message);
            if ($hasSent) {
                $smsRecord->status = SmsNotification::STATUS_SUCCESS;
                $smsRecord->note = $msg;
            } else {
                $smsRecord->status = SmsNotification::STATUS_ERROR;
                $smsRecord->note = $msg;
            }
            $smsRecord->save();
        } else {
            BaseConsole::output(VarDumper::dumpAsString($smsRecord->getErrors()));
        }

    }

    protected static function getPhoneNumberToSend($phone, $prefix = '+84')
    {
        $phone = (string) $phone;
        if (strpos($phone, '0') === 0) { // 0 means the first character
            $phone = substr($phone, 1);
        }
        return $prefix . $phone;
    }
}
