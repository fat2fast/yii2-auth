<?php
/* @var $this yii\web\View */

/* @var $userProvider yii\data\ActiveDataProvider */
/* @var $searchModel */

use fat2fast\auth\models\User;
use mdm\admin\components\Helper;
use yii\grid\ActionColumn;
use yii\helpers\Html;

$this->title = Yii::t('authz', 'User');
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- Sweet Alert css-->
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header border-0">
                <div class="row align-items-center gy-3">
                    <div class="col-sm">
                    </div>
                    <div class="col-sm-auto">
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="/user/create" class="btn btn-primary add-btn">
                                <i class="ri-add-line align-bottom me-1"></i> Tạo mới
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body border border-dashed border-end-0 border-start-0">
                <?php echo $this->render("_search",[
                    'searchModel' => $searchModel
                ])?>
            </div>
            <div class="card-body pt-0">
                <div>
                    <?php
                    echo \yii\grid\GridView::widget([
                        'tableOptions' => ['class' => 'table align-middle table-nowrap', 'id' => 'payment-log-datatables'],
                        'headerRowOptions' => ['class' => 'text-muted table-light'],
                        'dataProvider' => $userProvider,
                        'summary' => '',
                        'emptyCell' => '',
                        'pager' => [
                            'options' => ['class' => 'pagination right-sidebar pagination listjs-pagination mb-0'],
                            'linkOptions' => ['class' => 'page'],
                            'pageCssClass' => 'paginate_button page-item',
                            'nextPageCssClass' => 'paginate_button page-item next',
                            'lastPageCssClass' => 'paginate_button page-item last',
                            'firstPageCssClass' => 'paginate_button page-item first',
                            'prevPageCssClass' => 'paginate_button page-item previous',
                            'disabledListItemSubTagOptions' => [
                                'tag' => 'div', 'class' => 'page'
                            ],
                            'prevPageLabel' => 'Previous',
                            'nextPageLabel' => 'Next',
                            'firstPageLabel' => 'First',
                            'lastPageLabel' => 'Last',
                        ],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'username',
                                'label' => 'Tài khoản',
                            ],
                            [
                                'attribute' => 'email',
                                'label' => 'Email',
                            ],
                            [
                                'attribute' => 'status',
                                'label' => 'Trạng thái',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->getStatusLabel($model->status);
                                }
                            ],
                            [
                                'attribute' => 'use_totp',
                                'label' => 'Xác thực hai yếu tố',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->getTotpLabel($model->use_totp);
                                }
                            ],
                            [
                                'class' => ActionColumn::class,
                                'template' => Helper::filterActionColumn(['view', 'update', 'restore', 'delete']),
                                'buttons' => [
                                    'delete' => function ($url, $model) {
                                        if ($model->status == User::STATUS_DELETED) {
                                            return '';
                                        }
                                        if (Yii::$app->user->id == $model->id) {
                                            return '';
                                        }
                                        $options = [
                                            'title' => Yii::t('authz', 'Delete'),
                                            'aria-label' => Yii::t('authz', 'Delete'),
                                            'data-confirm' => Yii::t('authz', 'Are you sure you want to delete this item?'),
                                            'data-method' => 'post',
                                            'data-pjax' => '0',
                                        ];

                                        return Html::a('<span class="badge bg-danger"><span class="mdi mdi-close"></span></span>', [
                                            'user/delete',
                                            'id' => $model->id,
                                            'callbackURL' => Yii::$app->request->url,
                                        ], $options);
                                    },
                                ]
                            ],

                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>

    </div>
    <!--end col-->
</div>
<?php
$js = <<<JS
// if (!$('#user-datatables tbody tr td .empty').length) {
//     let historyContractDataTables = new DataTable('#user-datatables', {
//       processing: true,
//       ordering: false,
//       searching: false,
//       pageLength: 20,
//       lengthChange: false,
//       language : {
//          "info" : " _START_ đến _END_ trong _TOTAL_ dòng"
//       }
// });
// }
JS;
$this->registerJs($js, \yii\web\View::POS_END);

?>
