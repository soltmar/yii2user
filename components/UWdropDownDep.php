<?php

namespace mariusz_soltys\yii2user\components;

use mariusz_soltys\yii2user\Module;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * UWdropDownDep Widget
 *
 * @author Juan Fernando Gaviria <juan.gaviria@dsotogroup.com>
 * @link http://www.dsotogroup.com/
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @version $Id: UWdropDownDep.php 123 2013-01-26 10:04:33Z juan.gaviria $
 */

class UWdropDownDep {
  
	public $params = [
		'modelName'=>'',
		'optionName'=>'',
		'emptyField'=>'',
		'relationName'=>'',
		'modelDestName'=>'',
		'destField'=>'',
		'optionDestName'=>'',
	];
	
	/**
	 * Widget initialization
	 * @return array
	 */
	public function init() {
		return [
			'name'=>__CLASS__,
			'label'=>Module::t('DropDown List Dependent',[],__CLASS__),
			'fieldType'=>['INTEGER'],
			'params'=>$this->params,
			'paramsLabels' => [
				'modelName'=>Module::t('Model Name',[],__CLASS__),
				'optionName'=>Module::t('Lable field name',[],__CLASS__),
				'emptyField'=>Module::t('Empty item name',[],__CLASS__),
				'relationName'=>Module::t('Profile model relation name',[],__CLASS__),
				'modelDestName'=>Module::t('Model Dest Name',[],__CLASS__),
				'destField'=>Module::t('Dest Field',[],__CLASS__),
				'optionDestName'=>Module::t('Label Dest field name',[],__CLASS__),
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
	 * @param ActiveRecord $model - profile model
	 * @param $field - profile fields model item
     *
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
			return (($this->params['optionName'])?$m->getAttribute($this->params['optionName']):$m->getAttribute($m->tableSchema->primaryKey));
		else
			return $this->params['emptyField'];
		
	}
	
	/**
	 * @param ActiveRecord $model - profile model
	 * @param $field - profile fields model item
	 * @param array $htmlOptions - htmlOptions
	 * @return string
	 */
	public function editAttribute($model,$field,$htmlOptions=[]) {

        /** @var ActiveRecord $modelName */
        $modelName = $this->params['modelName'];

		$list = array();
		if ($this->params['emptyField']) $list[0] = $this->params['emptyField'];
		
		$models =$modelName::find()->all();
		foreach ($models as $m)
			$list[$m->getAttribute($m->tableSchema->primaryKey)] = 
                                (($this->params['optionName'])?$m->getAttribute($this->params['optionName']):$m->getAttribute($m->tableSchema->primaryKey));
		return Html::activeDropDownList($model,$field->varname,$list,ArrayHelper::merge($htmlOptions, [
				'ajax'=>[
						'type'=>'POST',
						'url'=>Url::to('/user/profileField/getDroDownDepValues'),
						'data'=>['model'=>$this->params['modelDestName'], 'field_dest'=>$this->params['destField'], 'varname'=>$field->varname, $field->varname=>'js:this.value', 'optionDestName'=>$this->params['optionDestName']],
						'success'=>'function(data){
        						$("#ajax_loader").hide();
        						$("#Profile_'.$this->params['destField'].'").html(data)
        				}',
						'beforeSend'=>'function(){
	        					$("#ajax_loader").fadeIn();
	        			}',
				]
				]));
	}
	
}
