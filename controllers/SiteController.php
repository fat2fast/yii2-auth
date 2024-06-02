<?php

namespace fat2fast\auth\controllers;

use fat2fast\auth\forms\LoginForm;
use fat2fast\auth\forms\RequestOTPForm;
use fat2fast\auth\forms\VerifyOTPForm;
use fat2fast\auth\models\form\TwoFaForm;
use lsat\otp\behavior\OtpBehavior;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLoginOtp()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $requestOtpModel = new RequestOTPForm();
        $verifyOtpModel = new VerifyOTPForm();
        if ($requestOtpModel->load(Yii::$app->request->post()) && $requestOtpModel->validate()) {
            $verifyOtpModel->phoneNumber = $requestOtpModel->phoneNumber;
            $requestOtpModel->process();

        }

        if ($verifyOtpModel->load(Yii::$app->request->post())) {
            [$isOk, $data, $errors] = $verifyOtpModel->process();
            if ($isOk) {

                Yii::$app->user->login($data);

                return $this->render($this->module->pathViewSite.'login', [
                    'requestOtpModel' => $requestOtpModel,
                    'verifyOtpModel' => $verifyOtpModel,
                    'showModal' => true,
                    'isSuccess' => true
                ]);

            }
        }

        return $this->render($this->module->pathViewSite.'login', [
            'requestOtpModel' => $requestOtpModel,
            'verifyOtpModel' => $verifyOtpModel,
            'showModal' => true,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::error("pass validate");
            $identity = $model->getUser();

            if (!$identity->hasTwoFaEnabled()) {
                if ($model->login()) {
                    Yii::error("Login success");
                    return $this->goBack();
                }
            }
            Yii::$app->user->createLoginVerificationSession($identity); //Allow the user to verify the login

            return $this->redirect(['login-verification']);

        }
        Yii::error($model->getErrors());
        $model->password = '';
        $this->layout = false;
        return $this->render($this->module->pathViewSite.'login', [
            'model' => $model,
        ]);
    }

    public function actionLoginVerification()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $user = Yii::$app->user->getIdentityFromLoginVerificationSession();
        if ($user === null) {
            Yii::$app->session->destroy();

            return $this->goHome();
        }
        $model = new TwoFaForm();
        $model->setScenario(TwoFaForm::SCENARIO_LOGIN);
        $model->setUser($user);
        $model->attachBehavior("otp", [
            'class' => OtpBehavior::class,
        ]);
        $payload = [];
        if(Yii::$app->request->method == 'POST'){
            $form = Yii::$app->request->post('TwoFaForm');
            $code = $form['code'];
            if(is_array($code)){
                $code = implode('',$code);
            }

            $payload = Yii::$app->request->post();
            $payload['TwoFaForm']['code'] = $code;
            Yii::error($payload);
            Yii::error(Yii::$app->request->post());
        }

        if ($model->load($payload) && $model->login()) {
            return $this->goBack();
        }
        $this->layout = false;
        return $this->render($this->module->pathViewSite.'login-verification', ['model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
