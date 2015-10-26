<?php
    use mariusz_soltys\yii2user\components\UHtml;
    use mariusz_soltys\yii2user\models\ProfileField;
    use mariusz_soltys\yii2user\Module;
    use yii\grid\GridView;
    use yii\helpers\Html;

    /**
     * @var \yii\web\View $this
     * @var \mariusz_soltys\yii2user\models\search\UserSearch $searchModel
     * @var \yii\data\ActiveDataProvider $dataProvider
     */

    $this->params['breadcrumbs']=array(
        ['label' => Module::t('Profile Fields'), 'url' => ['admin']],
        Module::t('Manage'),
    );
    Module::getInstance()->setMenu([
        ['label'=>Module::t('Create Profile Field'), 'url'=> ['create']],
        ['label'=>Module::t('Manage Profile Field'), 'url'=> ['/user/profile-field/admin']],
        ['label'=>Module::t('Manage Users'), 'url'=> ['/user/admin']],
    ]);

    $this->registerJs("
        $('.search-button').click(function(){
            $('.search-form').toggle();
            return false;
        });
        $('.search-form form').submit(function(){
            $.fn.yiiGridView.update('profile-field-grid', {
                data: $(this).serialize()
            });
            return false;
        });
    ");

?>
<h1><?= Module::t('Manage Profile Fields'); ?></h1>

<p><?= Module::t("You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done."); ?></p>

<?= Html::a(Module::t('Advanced Search'), '#', ['class'=>'search-button']); ?>
<div class="search-form" style="display:none">
    <?= $this->render('_search', ['model'=>$searchModel]); ?>
</div><!-- search-form -->

<?php \yii\widgets\Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns'=> [
        'id',
        [
            'attribute' => 'varname',
            'format' => 'raw',
            'value' => function ($data) {
                return UHtml::markSearch($data, "varname");
            },
        ],
        [
            'attribute' => 'title',
            'value' => function ($data) {
                return Module::t($data->title);
            },
        ],
        [
            'attribute' => 'field_type',
            'filter' => ProfileField::itemAlias("field_type"),
        ],
        'field_size',
        //'field_size_min',
        [
            'attribute' => 'required',
            'value' => function ($data) {
                return ProfileField::itemAlias("required", $data->required);
            },
            'filter'=>ProfileField::itemAlias("required"),
        ],
        //'match',
        //'range',
        //'error_message',
        //'other_validator',
        //'default',
        'position',
        [
            'attribute' => 'visible',
            'value' => function ($data) {
                return ProfileField::itemAlias("visible", $data->visible);
            },
            'filter' => ProfileField::itemAlias("visible"),
        ],
        ['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>
<?php \yii\widgets\Pjax::end(); ?>
