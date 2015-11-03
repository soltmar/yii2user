<?php

namespace mariusz_soltys\yii2user\components;

use Yii;
use yii\widgets\ActiveField;

class UActiveField extends ActiveField
{

    protected $disableAjaxValidation = false;

    protected function getClientOptions()
    {
        if ($this->disableAjaxValidation) {
            return [];
        }

        return parent::getClientOptions();
    }

    public function disableAjaxValidation()
    {
        $this->disableAjaxValidation = true;
        return $this;
    }

//    public function run()
//    {
//        if (is_array($this->focus)) {
//            $this->focus="#".Html::getInputId($this->focus[0], $this->focus[1]);
//        }
//
//        echo Html::endForm();
//        $cs=Yii::$app->clientScript;
//        if (!$this->enableAjaxValidation && !$this->enableClientValidation || empty($this->attributes)) {
//            if ($this->focus!==null) {
//                $cs->registerCoreScript('jquery');
//                $cs->registerScript(
//                    'CActiveForm#focus',
//                    "if(!window.location.hash)
//						$('".$this->focus."').focus();"
//                );
//            }
//            return;
//        }
//
//        $options=$this->clientOptions;
//        if (isset($this->clientOptions['validationUrl']) && is_array($this->clientOptions['validationUrl'])) {
//            $options['validationUrl']=Url::toRoute($this->clientOptions['validationUrl']);
//        }
//
//        $options['attributes']=array();
//        foreach ($this->attributes as $attr => $item) {
//            if (in_array($attr, $this->disableAjaxValidationAttributes)===false) {
//                array_push($options['attributes'], $item);
//            }
//        }
//
//        if ($this->summaryID!==null) {
//            $options['summaryID']=$this->summaryID;
//        }
//
//        if ($this->focus!==null) {
//            $options['focus']=$this->focus;
//        }
//
//        $options=\yii\helpers\Json::encode($options);
//        $cs->registerCoreScript('yiiactiveform');
//        $id=$this->id;
//        $cs->registerScript(__CLASS__.'#'.$id, "\$('#$id').yiiactiveform($options);");
//
//        /*
//        parent::run();
//        $cs = Yii::$app->getClientScript();
//        $js = "// UActiveForm\n$('".'#'.implode(', #',$this->disableAjaxValidationAttributes)."').die('focusout','');";
//        //echo '<pre>'; print_r(); die();
//        $cs->registerScript(__CLASS__.'#dialog', $js);
//
//        if(is_array($this->focus))
//            $this->focus="#".CHtml::activeId($this->focus[0],$this->focus[1]);
//
//        echo CHtml::endForm();
//        $cs=Yii::$app->clientScript;
//        if(!$this->enableAjaxValidation && !$this->enableClientValidation || empty($this->attributes))
//        {
//            if($this->focus!==null)
//            {
//                $cs->registerCoreScript('jquery');
//                $cs->registerScript('CActiveForm#focus',"
//                if(!window.location.hash)
//                $('".$this->focus."').focus();
//                ");
//            }
//            return;
//        }
//
//        $options=$this->clientOptions;
//        if(isset($this->clientOptions['validationUrl']) && is_array($this->clientOptions['validationUrl']))
//            $options['validationUrl']=CHtml::normalizeUrl($this->clientOptions['validationUrl']);
//
//        $options['attributes']=array();
//        foreach ($this->attributes as $attr => $item) {
//            if (in_array($attr,$this->disableAjaxValidationAttributes)===false) {
//                array_push($options['attributes'],$item);
//            }
//        }
//
//        if($this->summaryID!==null)
//        $options['summaryID']=$this->summaryID;
//
//        if($this->focus!==null)
//            $options['focus']=$this->focus;
//
//        $options=CJavaScript::encode($options);
//        $cs->registerCoreScript('yiiactiveform');
//        $id=$this->id;
//        $cs->registerScript(__CLASS__.'#'.$id,"\$('#$id').yiiactiveform($options);");
//    //*/
//    }
}

