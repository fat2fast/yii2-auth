<?php
/**
 * Created by PhpStorm.
 * User: PhatPNH
 * Date: 24/05/2024
 * Time: 12:15 CH
 */

use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var  $searchModel */
/** @var yii\widgets\ActiveForm $form */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'options' => ['class' => 'app-search d-none d-md-block', 'data-pjax' => 1, 'id' => 'user_form'],
]); ?>
<div class="row g-3">
    <div class="col-xxl-10 col-sm-6">
        <div class="search-box">
            <?php echo $form->field($searchModel, 'keywords')->textInput(['placeholder' => 'Tìm kiếm ...', 'class' => 'form-control search', 'autocomplete' => 'off'])->label(false) ?>
            <i class="ri-search-line search-icon"></i>

        </div>
    </div>

    <div class="col-md-1">
        <button type="submit" class="btn btn-primary w-100"><i
                class="ri-equalizer-fill me-1 align-bottom"></i>
            Lọc
        </button>
    </div>
    <div class="col-md-1">
        <div class="d-flex gap-1 flex-wrap">
            <a href="/user" class="btn btn-info w-100">
                <i class="las la-redo-alt"></i>Làm mới
            </a>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>



