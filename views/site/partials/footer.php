<footer class="footer border-top">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <script>document.write(new Date().getFullYear())</script> © <?php echo Yii::$app->params['authz.fullNameOrganization'] ?? "Fat2Fast" ?>.
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    Designed & Developed by <?php echo Yii::$app->params['authz.acronymOrganization'] ?? "F2F" ?>
                </div>
            </div>
        </div>
    </div>
</footer>
