<?php
use mariusz_soltys\yii2user\Module;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs']= [
    Module::t("Users"),
];
if (Module::isAdmin()) {
    $this->context->layout='column2';
    $this->params['menu']= [
        ['label'=>Module::t('Manage Users'), 'url'=> ['/user/admin']],
        ['label'=>Module::t('Manage Profile Field'), 'url'=> ['profileField/admin']],
    ];
}
?>

<h1><?php echo Module::t("List User"); ?></h1>

<?= GridView::widget([
    'dataProvider'=>$dataProvider,
    'columns'=> [
        [
            'format' => 'html',
            'value' => function ($data) {
                return Html::a(Html::encode($data->username), ["user/view","id"=>$data->id]);
            },
        ],
        'create_at',
        'lastvisit_at',
    ],
]); ?>
