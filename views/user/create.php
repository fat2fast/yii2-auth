<?php

use fat2fast\auth\models\form\UserForm;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model UserForm */

$this->title = Yii::t('authz', 'Create User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('authz', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

  <?= $this->render('_form', [
    'model' => $model,
  ]) ?>

</div>
