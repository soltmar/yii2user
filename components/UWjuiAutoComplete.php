<?php

namespace mariusz_soltys\yii2user\components;

use mariusz_soltys\yii2user\Module;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Json;

class UWjuiAutoComplete {
	
	public $params = array(
		'ui-theme'=>'base',
		'modelName'=>'',
		'optionName'=>'',
		'emptyFieldLabel'=>'Not found',
		'emptyFieldValue'=>0,
		'relationName'=>'',
		'minLength'=>'',
	);
	
	/**
	 * Widget initialization
	 * @return array
	 */
	public function init() {
		return array(
			'name'=>__CLASS__,
			'label'=>Module::t('jQueryUI autocomplete',array(),__CLASS__),
			'fieldType'=>array('INTEGER'),
			'params'=>$this->params,
			'paramsLabels' => array(
				'modelName'=>Module::t('Model Name',array(),__CLASS__),
				'optionName'=>Module::t('Lable field name',array(),__CLASS__),
				'emptyFieldLabel'=>Module::t('Empty item name',array(),__CLASS__),
				'emptyFieldValue'=>Module::t('Empty item value',array(),__CLASS__),
				'relationName'=>Module::t('Profile model relation name',array(),__CLASS__),
				'minLength'=>Module::t('minimal start research length',array(),__CLASS__),
			),
		);
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
	 * @param ActiveRecord $model - profile model
	 * @param $field - profile fields model item
	 * @return string
	 */
	public function viewAttribute($model,$field) {
        $relation = $this->params['relationName'];

        /** @var ActiveRecord $modelName */
        $modelName = $this->params['modelName'];

        if ($relation&&method_exists($model, $relation)) {
            $m = $model->__get($this->params['relationName']);
        } else {
            $m = $modelName::findOne($model->getAttribute($field->varname));
        }
		
		if ($m)
			return (($this->params['optionName'])?$m->getAttribute($this->params['optionName']):$m->id);
		else
			return $this->params['emptyFieldLabel'];
	}
	
	/**
	 * @param ActiveRecord $model - profile model
	 * @param $field - profile fields model item
	 * @param $htmlOptions - htmlOptions
	 * @return string
	 */
	public function editAttribute($model,$field,$htmlOptions=array()) {

        /** @var ActiveRecord $modelName */
        $modelName = $this->params['modelName'];

		$list = array();
		if (isset($this->params['emptyFieldValue'])) $list[]=array('id'=>$this->params['emptyFieldValue'],'label'=>$this->params['emptyFieldLabel']);
			$models = $modelName->find()->all();
		foreach ($models as $m)
			$list[] = (($this->params['optionName'])?array('label'=>$m->getAttribute($this->params['optionName']),'id'=>$m->id):array('label'=>$m->id,'id'=>$m->id));
		
		if (!isset($htmlOptions['id'])) $htmlOptions['id'] = $field->varname;
		$id = $htmlOptions['id'];

        $relation = $this->params['relationName'];

        /** @var ActiveRecord $modelName */
        $modelName = $this->params['modelName'];

        if ($relation&&method_exists($model, $relation)) {
            $m = $model->__get($this->params['relationName']);
        } else {
            $m = $modelName::findOne($model->getAttribute($field->varname));
        }
		
		if ($m)
			$default_value = (($this->params['optionName'])?$m->getAttribute($this->params['optionName']):$m->id);
		else
			$default_value = '';
		
		$htmlOptions['value'] = $default_value;
		$options['source'] = $list;
		$options['minLength'] = $this->params['minLength'];
		$options['showAnim'] = 'fold';
		$options['select'] = "js:function(event, ui) { $('#".get_class($model)."_".$field->varname."').val(ui.item.id);}";
		$options=Json::encode($options);

//		//$basePath=Yii::getPathOfAlias('application.views.asset');
//		$basePath=Yii::getAlias('@mariusz_soltys/yii2user/views/asset');
//		$baseUrl=Yii::$app->getAssetManager()->publish($basePath);
//		$cs = Yii::$app->getClientScript();
//		$cs->registerCssFile($baseUrl.'/css/'.$this->params['ui-theme'].'/jquery-ui.css');
//		$cs->registerScriptFile($baseUrl.'/js/jquery-ui.min.js');

		$js = "jQuery('#{$id}').autocomplete({$options});";
		$cs->registerScript('Autocomplete'.'#'.$id, $js);
		
		return Html::activeInput($model,$field->varname,$htmlOptions).Html::activeHiddenInput($model,$field->varname);
	}
} 