<?php

namespace marsoltys\yii2user\components;

use marsoltys\yii2user\Module;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Json;

class UWjuiAutoComplete
{

    public $params = [
        'modelName'=>'',
        'optionName'=>'',
        'emptyFieldLabel'=>'Not found',
        'emptyFieldValue'=>0,
        'relationName'=>'',
        'minLength'=>'',
    ];

    /**
     * Widget initialization
     * @return array
     */
    public function init()
    {
        return [
            'name'=>__CLASS__,
            'label'=>Module::t('jQueryUI autocomplete', [], __CLASS__),
            'fieldType'=> ['VARCHAR'],
            'params'=>$this->params,
            'paramsLabels' => [
                'modelName'=>Module::t('Model Name', [], __CLASS__),
                'optionName'=>Module::t('Lable field name', [], __CLASS__),
                'emptyFieldLabel'=>Module::t('Empty item name', [], __CLASS__),
                'emptyFieldValue'=>Module::t('Empty item value', [], __CLASS__),
                'relationName'=>Module::t('Profile model relation name', [], __CLASS__),
                'minLength'=>Module::t('minimal start research length', [], __CLASS__),
            ],
        ];
    }

    /**
     * @param $value
     * @return string
     */
    public function setAttributes($value)
    {
        return $value;
    }

    /**
     * @param ActiveRecord $model - profile model
     * @param $field - profile fields model item
     * @return string
     */
    public function viewAttribute($model, $field)
    {
        $relation = $this->params['relationName'];

        /** @var ActiveRecord $modelName */
        $modelName = $this->params['modelName'];

        if ($relation&&method_exists($model, $relation)) {
            $m = $model->__get($this->params['relationName']);
        } else {
            $m = $modelName::findOne($model->getAttribute($field->varname));
        }

        if ($m) {
            return (($this->params['optionName'])?$m->getAttribute($this->params['optionName']):$m->id);
        } else {
            return $this->params['emptyFieldLabel'];
        }
    }

    /**
     * @param ActiveRecord $model - profile model
     * @param $field - profile fields model item
     * @param $htmlOptions - htmlOptions
     * @return string
     */
    public function editAttribute($model, $field, $htmlOptions = [])
    {

        /** @var ActiveRecord $modelName */
        $modelName = $this->params['modelName'];

        $list = [];
        if (isset($this->params['emptyFieldValue'])) {
            $list[]= ['id' =>$this->params['emptyFieldValue'], 'label' =>$this->params['emptyFieldLabel']];
        }
        $models = $modelName->find()->all();
        foreach ($models as $m) {
            if ($this->params['optionName']) {
                $list[] = ['label' =>$m->getAttribute($this->params['optionName']), 'id' =>$m->id];
            } else {
                $list[] = ['label' =>$m->id, 'id' =>$m->id];
            }
        }

        if (!isset($htmlOptions['id'])) {
            $htmlOptions['id'] = $field->varname;
        }
        $id = $htmlOptions['id'];

        $relation = $this->params['relationName'];

        /** @var ActiveRecord $modelName */
        $modelName = $this->params['modelName'];

        if ($relation&&method_exists($model, $relation)) {
            $m = $model->__get($this->params['relationName']);
        } else {
            $m = $modelName::findOne($model->getAttribute($field->varname));
        }

        if ($m) {
            $default_value = (($this->params['optionName'])?$m->getAttribute($this->params['optionName']):$m->id);
        } else {
            $default_value = '';
        }

        $htmlOptions['value'] = $default_value;
        $options['source'] = $list;
        $options['minLength'] = $this->params['minLength'];
        //$options['showAnim'] = 'fold';
        $options['select'] = "js:function(event, ui) { $('#".get_class($model)."_".$field->varname."').val(ui.item.id);}";
        $options=Json::encode($options);

        /** @var \yii\widgets\ActiveField $form */
        $form = $htmlOptions['formField'];
        unset($htmlOptions['formField']);

        $return = $form->widget(\yii\jui\AutoComplete::classname(), ['clientOptions' => $options]);

        return $return;
    }
} 