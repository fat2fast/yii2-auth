<?php

namespace fat2fast\auth\components;

use Yii;
use yii\base\InvalidValueException;
use yii\web\IdentityInterface;

class User extends \yii\web\User
{
    public string $loginVerificationSessionKey = 'loginVerification';

    public function createLoginVerificationSession(IdentityInterface $identity, ?string $returnUrl = null, ?int $expirationTime = null)
    {
        if ($expirationTime === null) {
            $expirationTime = time() + (5 * 60); // Expires in 5 minutes
        }

        Yii::$app->session->set($this->loginVerificationSessionKey, [
            'id' => $identity->getId(),
            'exp' => $expirationTime,
            'returnUrl' => $this->getReturnUrl($returnUrl),
        ]);
    }

    /**
     * This method attempts to authenticate a user using the information in the login verification session.
     *
     * @return IdentityInterface|null Returns an 'identity' if valid, otherwise null.
     */
    public function getIdentityFromLoginVerificationSession(): ?IdentityInterface
    {
        if ($this->hasValidLoginVerificationSession()) {
            $data = Yii::$app->session->get($this->loginVerificationSessionKey);
            $class = $this->identityClass;
            $identity = $class::findIdentity($data['id']);
            if ($identity !== null) {
                if (!$identity instanceof IdentityInterface) {
                    throw new InvalidValueException("$class::findIdentity() must return an object implementing IdentityInterface.");
                }
                if ($data['returnUrl']) {
                    $this->setReturnUrl($data['returnUrl']);
                }

                return $identity;
            }
        }
        $this->destroyLoginVerificationSession();

        return null;
    }

    protected function hasValidLoginVerificationSession(): bool
    {
        $data = Yii::$app->session->get($this->loginVerificationSessionKey);
        if ($data === null) {
            return false;
        }
        if (is_array($data) && count($data) == 3) {
            if (time() < $data['exp']) {
                return true;
            }
        }
        $this->destroyLoginVerificationSession();

        return false;
    }

    public function destroyLoginVerificationSession()
    {
        Yii::$app->session->remove($this->loginVerificationSessionKey);
    }
}
