<?php
namespace mariusz_soltys\yii2user\models;

use Yii;
use yii\db\ActiveRecord;
use mariusz_soltys\yii2user\Module;
use yii\helpers\Json;

/**
 * The followings are the available columns in table 'profiles_fields':
 * @property integer id
 * @property string varname
 * @property string title
 * @property string field_type
 * @property integer field_size
 * @property integer field_size_mix
 * @property integer required
 * @property integer match
 * @property string range
 * @property string error_message
 * @property string other_validator
 * @property string default
 * @property integer position
 * @property integer visible
 * @property string widget
 * @property string widgetparams
 */

class ProfileField extends ActiveRecord
{
    const VISIBLE_ALL=3;
    const VISIBLE_REGISTER_USER=2;
    const VISIBLE_ONLY_OWNER=1;
    const VISIBLE_NO=0;

    const REQUIRED_NO = 0;
    const REQUIRED_YES_SHOW_REG = 1;
    const REQUIRED_NO_SHOW_REG = 2;
    const REQUIRED_YES_NOT_SHOW_REG = 3;
    const SENARIO_SEARCH = 'search';



    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return Module::getInstance()->tableProfileFields;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            [['varname', 'title', 'field_type'], 'required'],
            [
                'varname',
                'match',
                'pattern' => '/^[A-Za-z_0-9]+$/u',
                'message' => Module::t("Variable name may consist of A-z, 0-9, underscores, begin with a letter.")
            ],
            ['varname', 'unique', 'message' => Module::t("This field already exists.")],
            [['varname', 'field_type'], 'length', 'max'=>50],
            [['field_size_min', 'required', 'position', 'visible'], 'numerical', 'integerOnly'=>true],
            ['field_size', 'match', 'pattern' => '/^\s*[-+]?[0-9]*\,*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
            [['title', 'match', 'error_message', 'other_validator', 'default', 'widget'], 'length', 'max'=>255],
            [['range', 'widgetparams'], 'length', 'max'=>5000],
            [
                [
                    'id',
                    'varname',
                    'title',
                    'field_type',
                    'field_size',
                    'field_size_min',
                    'required', 'match',
                    'range',
                    'error_message',
                    'other_validator',
                    'default',
                    'widget',
                    'widgetparams',
                    'position',
                    'visible'
                ],
                'safe', 'on'=>self::SENARIO_SEARCH],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Module::t('Id'),
            'varname' => Module::t('Variable name'),
            'title' => Module::t('Title'),
            'field_type' => Module::t('Field Type'),
            'field_size' => Module::t('Field Size'),
            'field_size_min' => Module::t('Field Size min'),
            'required' => Module::t('Required'),
            'match' => Module::t('Match'),
            'range' => Module::t('Range'),
            'error_message' => Module::t('Error Message'),
            'other_validator' => Module::t('Other Validator'),
            'default' => Module::t('Default'),
            'widget' => Module::t('Widget'),
            'widgetparams' => Module::t('Widget parametrs'),
            'position' => Module::t('Position'),
            'visible' => Module::t('Visible'),
        );
    }

    /**
     * @inheritdoc
     * @return ProfileFieldQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProfileFieldQuery(get_called_class());
    }

    /**
     * @param ActiveRecord $model
     * @return string formated value
     */
    public function widgetView($model)
    {
        if ($this->widget && class_exists($this->widget)) {
            $widgetClass = new $this->widget;

            $arr = $this->widgetparams;
            if ($arr) {
                $newParams = $widgetClass->params;
                $arr = Json::decode($arr, true);
                foreach ($arr as $p => $v) {
                    if (isset($newParams[$p])) {
                        $newParams[$p] = $v;
                    }
                }
                $widgetClass->params = $newParams;
            }

            if (method_exists($widgetClass, 'viewAttribute')) {
                return $widgetClass->viewAttribute($model, $this);
            }
        }
        return false;
    }

    public function widgetEdit($model, $params = [])
    {
        if ($this->widget && class_exists($this->widget)) {
            $widgetClass = new $this->widget;

            $arr = $this->widgetparams;
            if ($arr) {
                $newParams = $widgetClass->params;
                $arr = (array)Json::decode($arr);
                foreach ($arr as $p => $v) {
                    if (isset($newParams[$p])) {
                        $newParams[$p] = $v;
                    }
                }
                $widgetClass->params = $newParams;
            }

            if (method_exists($widgetClass, 'editAttribute')) {
                return $widgetClass->editAttribute($model, $this, $params);
            }
        }
        return false;
    }

    /**
     * @param string $type
     * @param null|string $code
     * @return false|string alias or false if alias is not found
     */

    public static function itemAlias($type, $code = null)
    {
        $_items = array(
            'field_type' => array(
                'INTEGER' => Module::t('INTEGER'),
                'VARCHAR' => Module::t('VARCHAR'),
                'TEXT'=> Module::t('TEXT'),
                'DATE'=> Module::t('DATE'),
                'FLOAT'=> Module::t('FLOAT'),
                'DECIMAL'=> Module::t('DECIMAL'),
                'BOOL'=> Module::t('BOOL'),
                'BLOB'=> Module::t('BLOB'),
                'BINARY'=> Module::t('BINARY'),
            ),
            'required' => array(
                self::REQUIRED_NO => Module::t('No'),
                self::REQUIRED_NO_SHOW_REG => Module::t('No, but show on registration form'),
                self::REQUIRED_YES_SHOW_REG => Module::t('Yes and show on registration form'),
                self::REQUIRED_YES_NOT_SHOW_REG => Module::t('Yes'),
            ),
            'visible' => array(
                self::VISIBLE_ALL => Module::t('For all'),
                self::VISIBLE_REGISTER_USER => Module::t('Registered users'),
                self::VISIBLE_ONLY_OWNER => Module::t('Only owner'),
                self::VISIBLE_NO => Module::t('Hidden'),
            ),
        );

        if (isset($code)) {
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        } else {
            return isset($_items[$type]) ? $_items[$type] : false;
        }
    }
}
