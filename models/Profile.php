<?php

namespace mariusz_soltys\yii2user\models;

use Yii;
use yii\db\ActiveRecord;

use mariusz_soltys\yii2user\Module;
use yii\helpers\Json;

class Profile extends ActiveRecord
{
    /**
     * The followings are the available columns in table 'profiles':
     * @var integer $user_id
     * @var boolean $regMode
     */
    public static $regMode = false;

    private static $model;
    private static $modelReg;
    private static $rules = array();

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return Module::getInstance()->tableProfiles;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        if (!self::$rules) {
            $required = array();
            $numerical = array();
            $float = array();
            $decimal = array();
            $rules = array();

            $model=self::getFields();

            foreach ($model as $field) {
                $field_rule = array();

                $cond = $field->required == ProfileField::REQUIRED_YES_NOT_SHOW_REG
                    || $field->required == ProfileField::REQUIRED_YES_SHOW_REG;
                if ($cond) {
                    array_push($required, $field->varname);
                }
                if ($field->field_type=='FLOAT') {
                    array_push($float, $field->varname);
                }
                if ($field->field_type=='DECIMAL') {
                    array_push($decimal, $field->varname);
                }
                if ($field->field_type=='INTEGER') {
                    array_push($numerical, $field->varname);
                }
                if ($field->field_type=='VARCHAR'||$field->field_type=='TEXT') {
                    $field_rule = [
                        $field->varname,
                        'length',
                        'max'=>$field->field_size,
                        'min' => $field->field_size_min
                    ];
                    if ($field->error_message) {
                        $field_rule['message'] = Module::t($field->error_message);
                    }
                    array_push($rules, $field_rule);
                }
                if ($field->other_validator) {
                    if (strpos($field->other_validator, '{') === 0) {
                        $validator = Json::decode($field->other_validator, true);
                        foreach ($validator as $name => $val) {
                            $field_rule = array($field->varname, $name);
                            $field_rule = array_merge($field_rule, (array)$validator[$name]);
                            if ($field->error_message) {
                                $field_rule['message'] = Module::t($field->error_message);
                            }
                            array_push($rules, $field_rule);
                        }
                    } else {
                        $field_rule = array($field->varname, $field->other_validator);
                        if ($field->error_message) {
                            $field_rule['message'] = Module::t($field->error_message);
                        }
                        array_push($rules, $field_rule);
                    }
                } elseif ($field->field_type=='DATE') {
                    if ($field->required) {
                        $field_rule = array($field->varname, 'date', 'format' => array('yyyy-mm-dd'));
                    } else {
                        $field_rule = [
                            $field->varname,
                            'date',
                            'format' => ['yyyy-mm-dd','0000-00-00'],
                            'allowEmpty'=>true];
                    }

                    if ($field->error_message) {
                        $field_rule['message'] = Module::t($field->error_message);
                    }
                    array_push($rules, $field_rule);
                }
                if ($field->match) {
                    $field_rule = array($field->varname, 'match', 'pattern' => $field->match);
                    if ($field->error_message) {
                        $field_rule['message'] = Module::t($field->error_message);
                    }
                    array_push($rules, $field_rule);
                }
                if ($field->range) {
                    $field_rule = array($field->varname, 'in', 'range' => self::rangeRules($field->range));
                    if ($field->error_message) {
                        $field_rule['message'] = Module::t($field->error_message);
                    }
                    array_push($rules, $field_rule);
                }
            }

            array_push($rules, [$required, 'required']);
            array_push($rules, [$numerical, 'numerical', 'integerOnly'=>true]);
            array_push($rules, [$float, 'type', 'type'=>'float']);
            array_push($rules, [$decimal, 'match', 'pattern' => '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/'
            ]);
            self::$rules = $rules;
        }
        return self::$rules;
    }

    /*  Relations    */

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = array(
            'user_id' => Module::t('User ID'),
        );
        $model=self::getFields();

        foreach ($model as $field) {
            if (Module::getInstance()->fieldsMessage) {
                $l = Module::t($field->title, array(), Module::getInstance()->fieldsMessage);
            } else {
                $l = Module::t($field->title);
            }
            $labels[$field->varname] = $l;
        }

        return $labels;
    }

    private function rangeRules($str)
    {
        $rules = explode(';', $str);
        for ($i=0; $i < count($rules); $i++) {
            $rules[$i] = current(explode("==", $rules[$i]));
        }
        return $rules;
    }

    public static function range($str, $fieldValue = null)
    {
        $rules = explode(';', $str);
        $array = array();
        for ($i=0; $i < count($rules); $i++) {
            $item = explode("==", $rules[$i]);
            if (isset($item[0])) {
                $array[$item[0]] = ((isset($item[1]))?$item[1]:$item[0]);
            }
        }
        if (isset($fieldValue)) {
            if (isset($array[$fieldValue])) {
                return $array[$fieldValue];
            } else {
                return '';
            }
        } else {
            return $array;
        }
    }

    public function widgetAttributes()
    {
        $data = array();
        $model=self::getFields();

        foreach ($model as $field) {
            if ($field->widget) {
                $data[$field->varname]=$field->widget;
            }
        }
        return $data;
    }

    public function widgetParams($fieldName)
    {
        $data = array();
        $model=self::getFields();

        foreach ($model as $field) {
            if ($field->widget) {
                $data[$field->varname]=$field->widgetparams;
            }
        }
        return $data[$fieldName];
    }

    /**
     * @return ProfileField[]
     */
    public static function getFields()
    {
        if (self::$regMode) {
            if (!self::$modelReg) {
                self::$modelReg=ProfileField::find()->forRegistration()->all();
            }
            return self::$modelReg;
        } else {
            if (!self::$model) {
                self::$model=ProfileField::find()->forOwner()->all();
            }
            return self::$model;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (get_class(Yii::$app)=='yii\web\Application'&&Profile::$regMode==false) {
            Yii::$app->user->updateSession();
        }
        return parent::afterSave($insert, $changedAttributes);
    }
}