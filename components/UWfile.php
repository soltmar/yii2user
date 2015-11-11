<?php

namespace mariusz_soltys\yii2user\components;

use mariusz_soltys\yii2user\Module;
use Yii;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\UploadedFile;

class UWfile
{
    
    /**
     * @var array
     * @desc widget parameters
     */
    public $params = array('path'=>'assets');

    /** @var \yii\web\UploadedFile */
    private $file_instance;

    private $old_file_path = '';
    private $new_file_path = '';
    
    /**
     * Widget initialization
     * @return array
     */
    public function init()
    {
        return array(
            'name'=>__CLASS__,
            'label'=>Module::t('File field'),
            'fieldType'=>array('VARCHAR'),
            'params'=>$this->params,
            'paramsLabels' => array(
                'path'=>Module::t('Upload path'),
            ),
            'other_validator'=>array(
                'file'=>array(
                    'allowEmpty'=>array('','false','true'),
                    'maxFiles'=>'',
                    'maxSize'=>'',
                    'minSize'=>'',
                    'tooLarge'=>'',
                    'tooMany'=>'',
                    'tooSmall'=>'',
                    'types'=>'',
                    'wrongType'=>'',
                    'safe'=>array('true','false'),
                ),
            ),
        );
    }
    
    /**
     * @param $value
     * @param ActiveRecord $model
     * @param $field_varname
     * @return string
     */
    public function setAttributes($value, $model, $field_varname)
    {
        $this->new_file_path = $this->old_file_path = $model->getAttribute($field_varname);
        
        if ($this->file_instance = UploadedFile::getInstance($model, $field_varname)) {
            $model->on(ActiveRecord::EVENT_AFTER_INSERT, 'processFile', null, false);
            $model->on(ActiveRecord::EVENT_AFTER_UPDATE, 'processFile', null, false);

            $file_name = str_replace(' ', '-', $this->file_instance->name);
            $this->new_file_path = $this->params['path'].'/';
            
            if ($this->old_file_path) {
                $this->new_file_path = pathinfo($this->old_file_path, PATHINFO_DIRNAME).'/';
            } else {
                $this->new_file_path .= $this->uniqueDir($this->new_file_path).'/';
            }
            
            $this->new_file_path .= $file_name;
            
        } else {
            if (isset($_POST[get_class($model)]['uwfdel'][$field_varname])&&$_POST[get_class($model)]['uwfdel'][$field_varname]) {
                $model->on(ActiveRecord::EVENT_AFTER_INSERT, 'processFile', null, false);
                $model->on(ActiveRecord::EVENT_AFTER_UPDATE, 'processFile', null, false);
                $path = '';
            }
        }
        
        return $this->new_file_path;
    }
        
    /**
     * @param ActiveRecord $model
     * @return string
     */
    public function viewAttribute($model, $field)
    {
        $file = $model->getAttribute($field->varname);
        if ($file) {
            $file = Yii::$app->baseUrl.'/'.$file;
            return Html::a(pathinfo($file, PATHINFO_FILENAME), $file);
        }

        return '';
    }
        
    /**
     * @param ActiveRecord $model
     * @return string
     */
    public function editAttribute($model, $field, $params = array())
    {
        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        $options = $params['options'];
        unset($params['options']);
        
        return Html::activeFileInput($model, $field->varname, $params)
        .(($model->getAttribute($field->varname))?'<br/>'.Html::activeCheckBox($model, '[uwfdel]'.$field->varname, $params)
        .' '.Html::activeLabel($model, '[uwfdel]'.$field->varname, ['label'=>Module::t('Delete file'), 'style'=>'display:inline;']):'')
        ;
    }

    /**
     * @param  Event $event
     * @return void
     */
    
    public function processFile($event)
    {
            
        $model = $event->sender;
        
        if ($this->old_file_path && file_exists($this->old_file_path)) {
            unlink($this->old_file_path);
            $files = scandir(pathinfo($this->old_file_path, PATHINFO_DIRNAME));
            if (empty($files[2])) {
                //No files in directory left
                rmdir(pathinfo($this->old_file_path, PATHINFO_DIRNAME));
            }
            
        }
        if ($this->file_instance) {
            if (!is_dir(pathinfo($this->new_file_path, PATHINFO_DIRNAME))) {
                mkdir(pathinfo($this->new_file_path, PATHINFO_DIRNAME), 0777, true);
            }
            $this->file_instance->saveAs($this->new_file_path);
        }
    }
    
    private function uniqueDir($base_path = '')
    {
        $unique_dir = $this->randomString();
        
        while (is_dir($base_path . $unique_dir)) {
            $unique_dir = $this->randomString();
        }
        
        return $unique_dir;
    }
    
    private function randomString($max = 20)
    {
        $string = '';
        $chars = "abcdefghijklmnopqrstuvwxwz0123456789_-ABCDEGFHIJKLMNOPQRSTUVW";
        for ($i = 0; $i < $max; $i++) {
            $rand_key = mt_rand(0, strlen($chars));
            $string  .= substr($chars, $rand_key, 1);
        }
        return str_shuffle($string);
    }
    
}