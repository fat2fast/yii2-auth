<?php

namespace fat2fast\auth\controllers;

use fat2fast\auth\components\AuthHandler;
use yii\web\Controller;

class SsoController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'callback' => [
                'class' => 'fat2fast\auth\actions\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ]
        ];
    }

    public function onAuthSuccess($client)
    {
        (new AuthHandler($client))->handle();
    }
}
