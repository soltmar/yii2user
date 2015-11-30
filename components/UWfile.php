<?php

namespace marsoltys\yii2user\components;

use marsoltys\yii2user\Module;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

class UWfile
{

    /**
     * @var array
     * @desc widget parameters
     */
    public $params = ['path' =>'assets'];

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
        return [
            'name'=>__CLASS__,
            'label'=>Module::t('File field'),
            'fieldType'=> ['VARCHAR'],
            'params'=>$this->params,
            'paramsLabels' => [
                'path'=>Module::t('Upload path'),
            ],
            'other_validator'=> [
                'file'=> [
                    'skipOnEmpty' => [
                        'label' => Module::t('Allow empty?'),
                        'value' => ['','false','true']
                    ],
                    'maxFiles' => '',
                    'maxSize' => [
                        'label' => Module::t('Max file size (bytes)'),
                        'value' => ''
                    ],
                    'minSize' => '',
                    //TODO create groups like "documents", "images", "video", "media"
                    'extensions' => '', //TODO Provide list of extensions
                    'mimeTypes' => '', //TODO Provide list of mime types
                    'checkExtensionByMimeType' => [
                        'value' => ['true','false']
                    ],
                    'message' => '',
                    'tooBig' => '',
                    'tooMany' => '',
                    'tooSmall' => '',
                    'uploadRequired' => '',
                    'wrongExtension' => '',
                    'wrongMimeType' => '',
                ],
            ],
        ];
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
            $model->on(ActiveRecord::EVENT_AFTER_INSERT, [$this, 'processFile'], null, false);
            $model->on(ActiveRecord::EVENT_AFTER_UPDATE, [$this, 'processFile'], null, false);

            $file_name = str_replace(' ', '-', $this->file_instance->name);
            $this->new_file_path = $this->params['path'].'/';

            if ($this->old_file_path) {
                $this->new_file_path = pathinfo($this->old_file_path, PATHINFO_DIRNAME).'/';
            } else {
                $this->new_file_path .= $this->uniqueDir($this->new_file_path).'/';
            }

            $this->new_file_path .= $file_name;

        } else {
            $c = join('', array_slice(explode('\\', get_class($model)), -1));
            if (isset($_POST[$c]['uwfdel'][$field_varname])&&$_POST[$c]['uwfdel'][$field_varname]) {
                $model->on(ActiveRecord::EVENT_AFTER_INSERT, [$this, 'processFile'], null, false);
                $model->on(ActiveRecord::EVENT_AFTER_UPDATE, [$this, 'processFile'], null, false);
                $this->new_file_path = '';
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
            $path = Url::base().'/'.$file;
            if (exif_imagetype($file)) {
                return Html::img($path);
            } else {
                return Html::a(pathinfo($path, PATHINFO_FILENAME), $path);
            }
        }

        return '';
    }

    /**
     * @param ActiveRecord $model
     * @return string
     */
    public function editAttribute($model, $field, $params = [])
    {
        if (!isset($params['options'])) {
            $params['options'] = [];
        }
        $options = $params['options'];
        unset($params['options']);

        /** @var \yii\widgets\ActiveField $form */
        $form = $params['formField'];
        unset($params['formField']);

        $return = $form->fileInput($options);

        $file = $model->getAttribute($field->varname);

        if ($file) {
            $return .= "<div class='form-group'><fieldset>";
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $root = Yii::getAlias('@webroot/'.$file);
            $type = explode("/", finfo_file($finfo, $root));

            $return .= Html::activeCheckBox($model, '[uwfdel]'.$field->varname, ['label'=>Module::t('Delete file')])."<br>";

            if ($type[0] == 'image') {
                $return .= Html::img(Url::base()."/".$file, ['class'=>'UWfile-image-preview']);
            } else {
                $assetsPath = Yii::$app->assetManager->getBundle('marsoltys\yii2user\assets\UserAssets')->basePath;
                $assetsUrl = Yii::$app->assetManager->getBundle('marsoltys\yii2user\assets\UserAssets')->baseUrl;
                $img = "/img/".pathinfo($file, PATHINFO_EXTENSION).".png";
                if (file_exists($assetsPath.$img)) {
                    $return .= Html::img($assetsUrl.$img, ['class'=>'UWfile-image-preview']);
                } else {
                    $return .= Html::img($assetsUrl."/img/file.png", ['class'=>'UWfile-image-preview']);
                }

                $return .= Html::a(pathinfo($file, PATHINFO_BASENAME)." ", Url::base()."/".$file);
            }

            $return .= "</fieldset></div>";
        }

        return $return;
    }

    /**
     * @return void
     */

    public function processFile()
    {
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
        $unique_dir = Yii::$app->security->generateRandomString(20);

        while (is_dir($base_path . $unique_dir)) {
            $unique_dir = Yii::$app->security->generateRandomString(20);
        }

        return $unique_dir;
    }
}
