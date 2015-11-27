<?php

namespace marsoltys\yii2user\components;

use Yii;
use marsoltys\yii2user\models\Profile;
use marsoltys\yii2user\Module;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

class UWjuidate {

    /**
     * @var array
     */
    public $params = [
        'dateFormat' => null,
     //   'ui-theme' => 'base',
        'language' => 'en',
    ];

    /**
     * Initialization
     * @return array
     */
    public function init()
    {
        return [
            'name'=>__CLASS__,
            'label'=>Module::t('jQueryUI datepicker'),
            'fieldType'=> ['DATE','VARCHAR'],
            'params'=>$this->params,
            'paramsLabels' => [
                'dateFormat'=>Module::t('Date format'),
            ],
        ];
    }

    /**
     * @param $model Profile - profile model
     * @param $field - profile fields model item
     * @return string
     */
    public function viewAttribute($model, $field)
    {
        return $model->getAttribute($field->varname);
    }

    /**
     * @param $model - profile model
     * @param $field - profile fields model item
     * @param $params - htmlOptions
     * @return string
     */
    public function editAttribute($model, $field, $htmlOptions = [])
    {
        if (!isset($htmlOptions['size'])) {
            $htmlOptions['size'] = 60;
        }
        if (!isset($htmlOptions['maxlength'])) {
            $htmlOptions['maxlength'] = (($field->field_size)?$field->field_size:10);
        }
        if (!isset($htmlOptions['id'])) {
            $htmlOptions['id'] = get_class($model).'_'.$field->varname;
        }

       // $this->params['dateFormat'] = 'yy-mm-dd';
        $this->params['options']['class'] = 'form-control';

        /** @var $afield ActiveForm*/
        $field = $htmlOptions['formField'];
        unset($htmlOptions['formField']);

        return $field->widget(DatePicker::className(), $this->params);
    }
}
