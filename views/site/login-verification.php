<?php

use fat2fast\auth\models\form\TwoFaForm;
use yii\widgets\ActiveForm;
/**
 * @var TwoFaForm $model
 */
echo $this->render('partials/main'); ?>

<head>

    <?php echo $this->render('partials/title-meta', array('title' => 'Xác thực hai yêu tố')); ?>

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
                        <div class="row justify-content-center g-0">
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
                                                <div class="carousel-indicators">
                                                    <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="0" class="active" aria-current="true"
                                                            aria-label="Slide 1"></button>
                                                    <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                    <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                </div>
                                                <div class="carousel-inner text-center text-white pb-5">
                                                    <div class="carousel-item active">
                                                        <p class="fs-15 fst-italic"><span
                                                                style="font-size: 35px">MIDAS</span><br><span>Copyright ©<script>document.write(new Date().getFullYear())</script> LCS</span>
                                                        </p>
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
                                    <div class="mb-4">
                                        <div class="avatar-lg mx-auto">
                                            <div class="avatar-title bg-light text-primary display-5 rounded-circle">
                                                <i class="ri-key-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted text-center mx-lg-3">
                                        <h4 class="">Xác thực</h4>
                                        <p>Vui lòng nhập <?php echo Yii::$app->otp->digits ?> số
                                        </p>
                                    </div>

                                    <div class="mt-4">
                                        <?php $form = ActiveForm::begin(['id' => 'login-verification-form']); ?>
                                            <div class="row">
                                                <?php
                                                $lengthCode = Yii::$app->otp->digits;
                                                for ($i = 1; $i <= $lengthCode; $i++):

                                                    ?>
                                                    <div class="col-2">
                                                        <div class="mb-3">
                                                            <?php echo $form->field($model, 'code[]')->label(false)->textInput([
                                                                'autofocus' => true,
                                                                'class' => 'form-control form-control-lg bg-light border-light text-center',
                                                                'autocomplete' => 'off',
                                                                'maxLength' => 1,
                                                                'id' => "digit$i-input",
                                                                'onkeyup' => "moveToNext($i,$lengthCode, event)"
                                                            ]) ?>
                                                        </div>
                                                    </div><!-- end col -->
                                                <?php endfor; ?>
                                            </div>
                                            <p class="form-text text-muted hint text-center">
                                                <?php echo Yii::t("authz","Enter the code from your two-factor authenticator app.") ?>
                                            </p>
                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-primary w-100">Xác nhận</button>
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
                        <p class="mb-0">&copy;
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

<!-- two-step-verification js -->
<script src="/js/pages/two-step-verification.init.js"></script>

</body>

</html>
