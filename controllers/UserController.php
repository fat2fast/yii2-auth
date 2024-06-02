<?php

namespace fat2fast\auth\controllers;

use fat2fast\auth\models\form\UserChangePassword;
use fat2fast\auth\models\form\UserForm;
use fat2fast\auth\models\form\UserOtpForm;
use lsat\otp\behavior\OtpBehavior;
use Yii;
use yii\base\UserException;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $userIdentityClass = $this->module->userIdentityClass;
        $modelSearch = new $userIdentityClass();
        $dataProvider = $modelSearch->search(Yii::$app->request->queryParams);
        return $this->render($this->module->pathViewUser.'index',[
            'userProvider' => $dataProvider,
            'searchModel' => $modelSearch
        ]);
    }
    public function actionCreate()
    {
        $model = new UserForm();

        if ($model->load(Yii::$app->request->post()) && $user = $model->create()) {
            return $this->redirect(['user/view', 'id' => $user->id]);
        }

        return $this->render($this->module->pathViewUser.'create', [
            'model' => $model,
        ]);
    }
    /**
     * @param $id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        $checkRoleSystemAdmin = Yii::$app->user->identity->isSystemAdmin;
        if(!$checkRoleSystemAdmin) {
            $userId = Yii::$app->user->getId();
            if($userId != $id) {
                throw new ForbiddenHttpException();
            }
        }
        $user = $this->findModel($id);

        return $this->render($this->module->pathViewUser.'view',[
            'model' => $user,
        ]);
    }
    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = new UserForm();
        $user = $this->findModel($id);
        if($user){
            $model->loadModel($user);
            if ($model->load(Yii::$app->request->post()) && $user = $model->edit()) {
                return $this->redirect(['user/view', 'id' => $user->id]);
            }
            Yii::error($model->errors);
        }

        return $this->render($this->module->pathViewUser.'update', [
            'model' => $model,
        ]);
    }
    /**
     * Change password
     * If Change is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionChangePassword($id)
    {
        $checkRoleSystemAdmin = Yii::$app->user->identity->isSystemAdmin;
        if(!$checkRoleSystemAdmin) {
            $id = Yii::$app->user->getId();
        }
        $model = new UserChangePassword();
        $user = $this->findModel($id);
        if($user){
            $model->loadModel($user);
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $id]);
            }
            Yii::error($model->errors);
        }

        return $this->render($this->module->pathViewUser.'change-password', [
            'model' => $model,
        ]);
    }
    /**
     * Change password
     * If Change is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionResetPassword($id)
    {
        $checkRoleSystemAdmin = Yii::$app->user->identity->isSystemAdmin;
        if(!$checkRoleSystemAdmin || Yii::$app->user->id == $id) {
            throw new ForbiddenHttpException();
        }
        /** @var IdentityInterface $user */
        $user = $this->findModel($id);
        if($user){
            $password = getenv("DEFAULT_PASSWORD");
            $user->setPassword($password);
            $user->generateAuthKey();
            $user->force_change_pwd = 1;
            if ($user->save()) {
                return $this->redirect(['view', 'id' => $id]);
            }
            Yii::error($user->errors);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    /**
     * @param      $id
     * @param      $id_parent
     *
     * @param null $callbackURL
     *
     * @return \yii\web\Response
     * @throws \yii\base\UserException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDelete($id, $id_parent = null, $callbackURL = null)
    {
        /** @var IdentityInterface $user */
        $user = $this->findModel($id);
        $userIdentityClass = $this->module->userIdentityClass;
        if ($user->status == $userIdentityClass::STATUS_ACTIVE && Yii::$app->user->id != $user->id) {
            $user->status = $userIdentityClass::STATUS_DELETED;
            if ($user->save()) {
                if ($callbackURL) {
                    return $this->redirect($callbackURL);
                }

                if ($id_parent != null) {
                    return $this->redirect(['view', 'id' => $id_parent]);
                }

                return $this->redirect(['view', 'id' => $id]);
            } else {
                $errors = $user->firstErrors;
                throw new UserException(reset($errors));
            }
        }

        return $this->redirect(['index']);
    }
    /**
     * @param $id
     *
     */
    public function actionConfigOtp($id)
    {
        $checkRoleSystemAdmin = Yii::$app->user->identity->isSystemAdmin;
        if(!$checkRoleSystemAdmin) {
            $userId = Yii::$app->user->getId();
            if($userId != $id) {
                throw new ForbiddenHttpException();
            }
        }
        $model = new UserOtpForm();
        /** @var \fat2fast\auth\models\User $user */
        $user = $this->findModel($id);
        if($user->hasTwoFaEnabled()){
            return $this->redirect(['view', 'id' => $id]);
        }
        $model->loadModel($user);
        $model->attachBehavior("otp", [
            'class' => OtpBehavior::class,
            'codeAttribute' => 'otp'
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $id]);
        }
        return $this->render($this->module->pathViewUser.'config-otp', [
            'model' => $model,
        ]);
    }
    public function actionDisableOtp($id)
    {
        /** @var \fat2fast\auth\models\User $user */
        $user = $this->findModel($id);
        $checkRoleSystemAdmin = Yii::$app->user->identity->isSystemAdmin;
        if(!$checkRoleSystemAdmin) {
            $userId = Yii::$app->user->getId();
            if($userId != $id) {
                throw new ForbiddenHttpException();
            }
        }
        if (!$user->hasTwoFaEnabled()) {
            Yii::$app->session->setFlash('error', Yii::t('twofa', 'Two-Factor authentication is not enabled.'));
        } else {
            $user->disableTwoFa();
        }

        return $this->redirect(['view', 'id' => $user->id]);
    }

    /**
     * @param $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        /**
         * @var IdentityInterface $userIdentityClass
         */
        $userIdentityClass = $this->module->userIdentityClass;
        if(($model = $userIdentityClass::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
