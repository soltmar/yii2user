<?php
/**
 *
 * Project: yii2user
 * Date: 14/11/2015
 * @author Mariusz Soltys.
 * @version 1.0.0
 * @license http://opensource.org/licenses/MIT
 *
 */

namespace mariusz_soltys\yii2user\components;


use mariusz_soltys\yii2user\Module;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;

class UActiveRecord extends ActiveRecord
{
    /**
     * Extends setAttributes to handle active date fields
     *
     * @param $values array
     * @param $safeOnly boolean
     */
    public function setAttributes($values, $safeOnly = true)
    {
        foreach ($this->widgetAttributes() as $fieldName => $className) {
            $className = '\\mariusz_soltys\\yii2user\\components\\'.$className;
            if (isset($values[$fieldName])&&class_exists($className)) {
                $class = new $className;
                $arr = $this->widgetParams($fieldName);
                if ($arr) {
                    $newParams = $class->params;
                    $arr = (array)Json::decode($arr);
                    foreach ($arr as $p => $v) {
                        if (isset($newParams[$p])) {
                            $newParams[$p] = $v;
                        }
                    }
                    $class->params = $newParams;
                }
                if (method_exists($class, 'setAttributes')) {
                    $values[$fieldName] = $class->setAttributes($values[$fieldName], $this, $fieldName);
                }
            }
        }
        parent::setAttributes($values, $safeOnly);
    }

    public function behaviors()
    {
        return Module::getInstance()->getBehaviorsFor(get_class($this));
    }
}