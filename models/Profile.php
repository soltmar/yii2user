<?php

namespace mariusz_soltys\yii2user\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\BaseJson;

use mariusz_soltys\yii2user\UserModule;

class Profile extends ActiveRecord
{
	/**
	 * The followings are the available columns in table 'profiles':
	 * @var integer $user_id
	 * @var boolean $regMode
	 */
	public static $regMode = false;
	
	private static $_model;
	private static $_modelReg;
	private static $_rules = array();

	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return Yii::$app->getModule('user')->tableProfiles;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		if (!self::$_rules) {
			$required = array();
			$numerical = array();
			$float = array();		
			$decimal = array();
			$rules = array();
			
			$model=self::getFields();
			
			foreach ($model as $field) {
				$field_rule = array();
				if ($field->required==ProfileField::REQUIRED_YES_NOT_SHOW_REG||$field->required==ProfileField::REQUIRED_YES_SHOW_REG)
					array_push($required,$field->varname);
				if ($field->field_type=='FLOAT')
					array_push($float,$field->varname);
				if ($field->field_type=='DECIMAL')
					array_push($decimal,$field->varname);
				if ($field->field_type=='INTEGER')
					array_push($numerical,$field->varname);
				if ($field->field_type=='VARCHAR'||$field->field_type=='TEXT') {
					$field_rule = array($field->varname, 'length', 'max'=>$field->field_size, 'min' => $field->field_size_min);
					if ($field->error_message) $field_rule['message'] = UserModule::t($field->error_message);
					array_push($rules,$field_rule);
				}
				if ($field->other_validator) {
					if (strpos($field->other_validator,'{')===0) {
						$validator = BaseJson::decode($field->other_validator, true);
						foreach ($validator as $name=>$val) {
							$field_rule = array($field->varname, $name);
							$field_rule = array_merge($field_rule,(array)$validator[$name]);
							if ($field->error_message) $field_rule['message'] = UserModule::t($field->error_message);
							array_push($rules,$field_rule);
						}
					} else {
						$field_rule = array($field->varname, $field->other_validator);
						if ($field->error_message) $field_rule['message'] = UserModule::t($field->error_message);
						array_push($rules,$field_rule);
					}
				} elseif ($field->field_type=='DATE') {
                    if ($field->required)
                        $field_rule = array($field->varname, 'date', 'format' => array('yyyy-mm-dd'));
                    else
                        $field_rule = array($field->varname, 'date', 'format' => array('yyyy-mm-dd','0000-00-00'), 'allowEmpty'=>true);

					if ($field->error_message) $field_rule['message'] = UserModule::t($field->error_message);
					array_push($rules,$field_rule);
				}
				if ($field->match) {
					$field_rule = array($field->varname, 'match', 'pattern' => $field->match);
					if ($field->error_message) $field_rule['message'] = UserModule::t($field->error_message);
					array_push($rules,$field_rule);
				}
				if ($field->range) {
					$field_rule = array($field->varname, 'in', 'range' => self::rangeRules($field->range));
					if ($field->error_message) $field_rule['message'] = UserModule::t($field->error_message);
					array_push($rules,$field_rule);
				}
			}
			
			array_push($rules,array(implode(',',$required), 'required'));
			array_push($rules,array(implode(',',$numerical), 'numerical', 'integerOnly'=>true));
			array_push($rules,array(implode(',',$float), 'type', 'type'=>'float'));
			array_push($rules,array(implode(',',$decimal), 'match', 'pattern' => '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/'));
			self::$_rules = $rules;
		}
		return self::$_rules;
	}

    /*  Relations    */

	public function getUser() {
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$labels = array(
			'user_id' => UserModule::t('User ID'),
		);
		$model=self::getFields();
		
		foreach ($model as $field)
			$labels[$field->varname] = ((Yii::$app->getModule('user')->fieldsMessage)?UserModule::t($field->title,array(),Yii::$app->getModule('user')->fieldsMessage):UserModule::t($field->title));
			
		return $labels;
	}
	
	private function rangeRules($str) {
		$rules = explode(';',$str);
		for ($i=0;$i<count($rules);$i++)
			$rules[$i] = current(explode("==",$rules[$i]));
		return $rules;
	}
	
	static public function range($str,$fieldValue=NULL) {
		$rules = explode(';',$str);
		$array = array();
		for ($i=0;$i<count($rules);$i++) {
			$item = explode("==",$rules[$i]);
			if (isset($item[0])) $array[$item[0]] = ((isset($item[1]))?$item[1]:$item[0]);
		}
		if (isset($fieldValue)) 
			if (isset($array[$fieldValue])) return $array[$fieldValue]; else return '';
		else
			return $array;
	}
	
	public function widgetAttributes() {
		$data = array();
		$model=self::getFields();
		
		foreach ($model as $field) {
			if ($field->widget) $data[$field->varname]=$field->widget;
		}
		return $data;
	}
	
	public function widgetParams($fieldName) {
		$data = array();
		$model=self::getFields();
		
		foreach ($model as $field) {
			if ($field->widget) $data[$field->varname]=$field->widgetparams;
		}
		return $data[$fieldName];
	}
	
	public static function getFields() {
		if (self::$regMode) {
			if (!self::$_modelReg)
				self::$_modelReg=ProfileField::forRegistration()->findAll();
			return self::$_modelReg;
		} else {
			if (!self::$_model)
				self::$_model=ProfileField::forOwner()->findAll();
			return self::$_model;
		}
	}

    public function afterSave($insert, $changedAttributes) {
        if (get_class(Yii::$app)=='CWebApplication'&&Profile::$regMode==false) {
            Yii::$app->user->updateSession();
        }
        return parent::afterSave($insert, $changedAttributes);
    }
}