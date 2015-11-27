<?php

namespace marsoltys\yii2user\components;

use marsoltys\yii2user\Module;

class UWrelBelongsTo {

    public $params = [
        'modelName'=>'',
        'optionName'=>'',
        'emptyField'=>'',
        'relationName'=>'',
    ];

    /**
     * Widget initialization
     * @return array
     */
    public function init() {
        return [
            'name' => __CLASS__,
            'label' => Module::t('Relation Belongs To', [],__CLASS__),
            'fieldType'=> ['INTEGER'],
            'params'=>$this->params,
            'paramsLabels' => [
                'modelName'=>Module::t('Model Name', [],__CLASS__),
                'optionName'=>Module::t('Lable field name', [],__CLASS__),
                'emptyField'=>Module::t('Empty item name', [],__CLASS__),
                'relationName'=>Module::t('Profile model relation name', [],__CLASS__),
            ],
        ];
    }

    /**
     * @param $value
     * @param $model
     * @param $field_varname
     * @return string
     */
    public function setAttributes($value,$model,$field_varname) {
        return $value;
    }

    /**
     * @param $model - profile model
     * @param $field - profile fields model item
     * @return string
     */
    public function viewAttribute($model,$field) {
        $relation = $model->relations();
        if ($this->params['relationName']&&isset($relation[$this->params['relationName']])) {
            $m = $model->__get($this->params['relationName']);
        } else {
            $m = ActiveRecord::model($this->params['modelName'])->findByPk($model->getAttribute($field->varname));
        }

        if ($m)
            return (($this->params['optionName'])?$m->getAttribute($this->params['optionName']):$m->id);
        else
            return $this->params['emptyField'];

    }

    /**
     * @param $model - profile model
     * @param $field - profile fields model item
     * @param $params - htmlOptions
     * @return string
     */
    public function editAttribute($model,$field,$htmlOptions= []) {
        $list = [];
        if ($this->params['emptyField']) $list[0] = $this->params['emptyField'];

        $models = CActiveRecord::model($this->params['modelName'])->findAll();
        foreach ($models as $m)
            $list[$m->id] = (($this->params['optionName'])?$m->getAttribute($this->params['optionName']):$m->id);
        return CHtml::activeDropDownList($model,$field->varname,$list,$htmlOptions= []);
    }

}