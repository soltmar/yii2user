<?php
use marsoltys\yii2user\Module;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs']= [
    Module::t("Users"),
];
if (!Module::isAdmin()) {
    $this->context->layout='main';
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
