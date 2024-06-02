<?php

namespace fat2fast\auth\components\auth;

use yii\authclient\OAuth2;

class AppleID extends OAuth2
{

    /**
     * {@inheritdoc}
     */
//    public $authUrl = 'https://xacthuc.dichvucong.gov.vn/oauth2/authorize';
    /**
     * {@inheritdoc}
     */
//    public $tokenUrl = 'https://xacthuc.dichvucong.gov.vn/oauth2/token';
    /**
     * {@inheritdoc}
     */
//    public $apiBaseUrl = 'https://xacthuc.dichvucong.gov.vn/oauth2/v1';


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->scope === null) {
            $this->scope = implode(' ', [
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/userinfo.email',
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function initUserAttributes()
    {
        return $this->api('userinfo', 'GET');
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultName()
    {
        return 'apple';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultTitle()
    {
        return 'Apple ID';
    }
}
