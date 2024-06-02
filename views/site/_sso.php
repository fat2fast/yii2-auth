<?php
$clients = Yii::$app->authClientCollection->clients ?? [];
if (count($clients) == 0) {
    return;
}
?>

<hr class="mhdivider my-3"/>
<p class="mtext-heading5 fw-normal text-mbw-600 text-center">
    Đăng nhập bằng hình thức khác </p>

<?php

use yii\authclient\widgets\AuthChoice;

?>
<?php $authAuthChoice = AuthChoice::begin([
    'baseAuthUrl' => ['sso/callback'],
    'popupMode' => false
]); ?>
    <?php foreach ($authAuthChoice->getClients() as $client): ?>

        <?php
            $linkText = Yii::t("yii2-authz", "Đăng nhập với {client}", ['client' => $client->getTitle()]);
            $wrapperClass = '';
            switch ($client->getName()) {
                case 'apple':
                    $wrapperClass = "mbtn-dark";
                    $linkText = \yii\helpers\Html::tag('span', '<span class="micon micon-apple"></span>' . $linkText, ['class' => 'button-content']);
                    break;
                case 'facebook':
                    $wrapperClass = "mbtn-facebook";
                    $linkText = \yii\helpers\Html::tag('span', '<span class="micon micon-facebook"></span>' . $linkText, ['class' => 'button-content']);
                    break;
                case 'google':
                    $wrapperClass = "mbtn-primary-light";
                    $linkText = \yii\helpers\Html::tag('span', '<span class="micon micon-google"></span>' . $linkText, ['class' => 'button-content']);
                    break;
                case 'vnconnect':
                    $wrapperClass = "mbtn-primary-light";
                    $linkText = \yii\helpers\Html::tag('span', '<span class="micon micon-dvc"></span>' . $linkText, ['class' => 'button-content']);
                    break;
            }
        echo $authAuthChoice->clientLink($client, $linkText, ['class' => "mbtn mbtn-lg d-block w-100 mb-3 $wrapperClass"])

        ?>
    <?php endforeach; ?>
<?php AuthChoice::end(); ?>

<!--
<a class="mbtn mbtn-lg mbtn-primary-light d-block w-100 mb-3" href="browse.html">
    <div class="button-content">
        <span class="micon micon-google"></span>Đăng nhập bằng Google
    </div>
</a>
<button class="mbtn mbtn-lg mbtn-facebook d-block w-100 mb-3">
    <div class="button-content">
        <span class="micon micon-facebook"></span>Đăng nhập bằng Facebook
    </div>
</button>
<button class="mbtn mbtn-lg mbtn-dark d-block w-100 mb-3">
    <div class="button-content">
        <span class="micon micon-apple"></span>Đăng nhập bằng Apple ID
    </div>
</button>-->
