<?php

namespace fat2fast\auth;

use Yii;

/**
 * yii2-auth module definition class
 */
class Module extends \yii\base\Module
{

    public $userIdentityClass = 'fat2fast\auth\models\user\User';
    // Default view path can override the view path
    public $pathViewSite = '@fat2fast/auth/views/site/';
    public $pathViewUser = '@fat2fast/auth/views/user/';

    public $otpConfigurations = [
        'otpMessage' => 'Ma OTP cua ban la {token}. Vui long KHONG chia se ma OTP voi bat ky ai.',
        'tokenExpiredInterval' => 60,
        'tokenLength' => 6,
        'tokenTest' => '123456'
    ];

    public $supportedClients = [
        'password', 'otp', 'sso'
    ];

    public $clientConfigurations = [

    ];

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'fat2fast\auth\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if (!self::isChildClass($this->userIdentityClass, 'fat2fast\auth\models\User')) {
            throw new \InvalidArgumentException($this->userIdentityClass . " must be an instance of \fat2fast\auth\models\User");
        }
        // custom initialization code goes here
        Yii::setAlias('@auth', __DIR__);
        $this->registerTranslations();
    }

    protected function registerTranslations()
    {
        Yii::$app->i18n->translations['authz'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath' => '@auth/messages',
            'fileMap' => [
                'authz' => 'app.php'
            ]
        ];
    }

    // Check if $childClassPath is a child of $parentClassPath
    function isChildClass($childClassPath, $parentClassPath)
    {
        // Get the parent class of $childClassPath
        $parentClassOfChild = get_parent_class($childClassPath);

        // Check if the parent class of $childClassPath matches $parentClassPath
        return $parentClassOfChild === $parentClassPath;
    }
}
