<?php
?>
    <div class="modal modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <span class="micon micon-4x micon-check micon-success top-0"></span>
                    </div>
                    <h5 class="mtext-heading3 fw-bold text-mprimary-500 mb-3 text-center">
                        Đăng nhập thành công </h5>
                    <div class="mtext-paragraph fw-normal text-mbw-600 text-center">
                        Hệ thống sẽ tự động đóng thông báo trong <span class="text-mdefault"><span id="redirect-timer"></span> giây</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.setTimeout(function () {
            location.href = "<?= $model->redirectUrl ?>";
        }, 3000);

        var timeleft = 3;
        var redirectTimer = setInterval(function(){
            if(timeleft <= 0){
                clearInterval(redirectTimer);
            }
            console.log("redirect in " + timeleft);
            document.getElementById("redirect-timer").innerHTML = timeleft;
            timeleft -= 1;
        }, 1000);

    </script>
<?php
if ($showModal) {
    $js = <<<JS
    var myModal = new bootstrap.Modal(document.getElementById('loginModal'));
    myModal.show();
    JS;
    $this->registerJs($js, \yii\web\View::POS_HEAD);
}
