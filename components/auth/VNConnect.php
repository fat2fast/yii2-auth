<?php

namespace fat2fast\auth\components\auth;

use Yii;
use yii\authclient\OAuth2;

class VNConnect extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    public $authUrl = 'https://testxacthuc.dichvucong.gov.vn/oauth2/authorize';
    /**
     * {@inheritdoc}
     */
    public $tokenUrl = 'https://testxacthuc.dichvucong.gov.vn/oauth2/token';
    /**
     * {@inheritdoc}
     */
    public $apiBaseUrl = 'https://xacthuc.dichvucong.gov.vn/oauth2/';


    protected function defaultReturnUrl()
    {
        $params = Yii::$app->getRequest()->getQueryParams();
        $params = array_intersect_key($params, array_flip($this->parametersToKeepInReturnUrl));

        $params[0] = Yii::$app->controller->getRoute();

        return Yii::$app->getUrlManager()->createAbsoluteUrl($params, true);

    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->scope === null) {
            $this->scope = implode(' ', [
                'openid'
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
        return 'vnconnect';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultTitle()
    {
        return 'Cổng Dịch vụ công';
    }
}
