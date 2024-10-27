<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap5\ActiveForm */

/* @var $model app\models\LoginForm */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Đăng nhập';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php echo $this->render('partials/main'); ?>

<head>

    <?php echo $this->render('partials/title-meta', array('title' => $this->title)); ?>

    <?php echo $this->render('partials/head-css'); ?>

</head>

<body>

<!-- auth-page wrapper -->
<div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
    <div class="bg-overlay"></div>
    <!-- auth-page content -->
    <div class="auth-page-content overflow-hidden pt-lg-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card overflow-hidden card-bg-fill border-0 card-border-effect-none">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="p-lg-5 p-4 auth-one-bg h-100">
                                    <div class="bg-overlay"></div>
                                    <div class="position-relative h-100 d-flex flex-column">
<!--                                        <div class="mb-4">-->
<!--                                            <a href="/" class="d-block">-->
<!--                                                <img src="/images/logo-light.png" alt="" height="18">-->
<!--                                            </a>-->
<!--                                        </div>-->
                                        <div class="mt-auto">
                                            <div class="mb-3">
                                                <i class="ri-double-quotes-l display-4 text-success"></i>
                                            </div>

                                            <div id="qoutescarouselIndicators" class="carousel slide"
                                                 data-bs-ride="carousel">
                                                <div class="carousel-inner text-center text-white pb-5">
                                                    <div class="carousel-item active">
                                                        <p class="fs-15 fst-italic"><span style="font-size: 35px"><?php echo Yii::$app->params['authz.TitleLogin']?></span><br><span>Copyright ©<script>document.write(new Date().getFullYear())</script> LCS</span></p>
                                                    </div>
<!--                                                    <div class="carousel-item">-->
<!--                                                        <p class="fs-15 fst-italic">" The theme is really great with an-->
<!--                                                            amazing customer support."</p>-->
<!--                                                    </div>-->
<!--                                                    <div class="carousel-item">-->
<!--                                                        <p class="fs-15 fst-italic">" Great! Clean code, clean design,-->
<!--                                                            easy for customization. Thanks very much! "</p>-->
<!--                                                    </div>-->
                                                </div>
                                            </div>
                                            <!-- end carousel -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->

                            <div class="col-lg-6">
                                <div class="p-lg-5 p-4">
                                    <div>
                                        <h5 class="text-primary">Welcome Back !</h5>
                                        <p class="text-muted">Sign in to continue to <?php echo Yii::$app->params['authz.TitleLogin']?>.</p>
                                    </div>

                                    <div class="mt-4">
                                        <?php $form = ActiveForm::begin([
                                            'id' => 'login-form',
                                        ]); ?>
                                        <div class="mb-3">
                                            <?php echo $form->field($model, 'username', [
                                                'options' => [
                                                    'tag' => 'div',
                                                    'class' => 'form-group',
                                                ],
                                            ])->textInput(['autofocus' => true, 'class' => 'form-control', 'placeholder' => 'Enter username']) ?>
                                        </div>
                                        <div class="mb-3">
<!--                                            <div class="float-end">-->
<!--                                                <a href="auth-pass-reset-cover" class="text-muted">Forgot password?</a>-->
<!--                                            </div>-->
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <?php echo $form->field($model, 'password', [
                                                ])->passwordInput(['placeholder' => 'Enter password', 'class' => 'form-control pe-5 password-input']) ?>
                                            </div>
                                        </div>
                                        <?php echo $form->field($model, 'rememberMe', [
                                        ])->checkbox(['class' => 'form-check-input'])->label("Remember me") ?>
                                        <div class="mt-4">
                                            <?php echo Html::submitButton(Yii::t('app', 'Login'), [
                                                'class' => 'btn btn-primary w-100', 'name' => 'login-button'
                                            ]) ?>
                                        </div>
                                        <?php ActiveForm::end(); ?>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->

            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end auth page content -->

    <!-- footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <p class="mb-0 footer-detail">&copy;
                            <script>document.write(new Date().getFullYear())</script>
                             <?php echo Yii::$app->params['authz.fullNameOrganization'] ?? "Fat2Fast" ?> . Crafted with <i class="mdi mdi-heart text-danger"></i> by <?php echo Yii::$app->params['authz.acronymOrganization'] ?? "F2F" ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end Footer -->
</div>
<!-- end auth-page-wrapper -->

<?php echo $this->render('partials/vendor-scripts'); ?>

<!-- password-addon init -->
<script src="/js/pages/password-addon.init.js"></script>
</body>

</html>
