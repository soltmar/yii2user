<?php

namespace marsoltys\yii2user\controllers;

use marsoltys\yii2user\assets\ProfileFieldAssets;
use marsoltys\yii2user\models\ProfileFieldSearch;
use yii\base\Exception;
use marsoltys\yii2user\models\Profile;
use marsoltys\yii2user\models\ProfileField;
use marsoltys\yii2user\Module;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class ProfileFieldController extends Controller
{

    /**
     * @var ProfileField the currently loaded data model instance.
     */
    private $model;
    private static $widgets = [];
    public $defaultAction = 'admin';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create','update','view','admin','delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback'=>function () {
                            return Yii::$app->user->isAdmin();
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays a particular model.
     */
    public function actionView()
    {
        return $this->render('view', [
            'model'=>$this->loadModel(),
        ]);
    }

    /**
     * Register Script
     */
    public function registerScript()
    {
        ProfileFieldAssets::register($this->view);

        $widgets = self::getWidgets();

        $wgByTypes = ProfileField::itemAlias('field_type');
        foreach ($wgByTypes as $k => $v) {
            $wgByTypes[$k] = [];
        }

        foreach ($widgets[1] as $widget) {
            if (isset($widget['fieldType'])&&count($widget['fieldType'])) {
                foreach ($widget['fieldType'] as $type) {
                    $c = explode("\\", $widget['name']);
                    array_push($wgByTypes[$type], $c[count($c)-1]);
                }
            }
        }

        /** @var string $js */
        $js = "

	var name = $('#name'),
	value = $('#value'),
	allFields = $([]).add(name).add(value),
	tips = $('.validateTips');



	var listWidgets = jQuery.parseJSON('".str_replace("'", "\'", Json::encode($widgets[0]))."');
	var widgets = jQuery.parseJSON('".str_replace("'", "\'", str_replace("\\", "\\\\", Json::encode($widgets[1])))."');
	var wgByType = jQuery.parseJSON('".str_replace("'", "\'", str_replace("\\", "\\\\", Json::encode($wgByTypes)))."');


	var fieldType = {
			'INTEGER':{
				'hide':['match','other_validator','widgetparams'],
				'val':{
					'field_size':10,
					'default':'0',
					'range':'',
					'widgetparams':''
				}
			},
			'VARCHAR':{
				'hide':['widgetparams'],
				'val':{
					'field_size':255,
					'default':'',
					'range':'',
					'widgetparams':''
				}
			},
			'TEXT':{
				'hide':['field_size','range','widgetparams'],
				'val':{
					'field_size':0,
					'default':'',
					'range':'',
					'widgetparams':''
				}
			},
			'DATE':{
				'hide':['field_size','field_size_min','match','range','widgetparams'],
				'val':{
					'field_size':0,
					'default':'0000-00-00',
					'range':'',
					'widgetparams':''
				}
			},
			'FLOAT':{
				'hide':['match','other_validator','widgetparams'],
				'val':{
					'field_size':'10.2',
					'default':'0.00',
					'range':'',
					'widgetparams':''
				}
			},
			'DECIMAL':{
				'hide':['match','other_validator','widgetparams'],
				'val':{
					'field_size':'10,2',
					'default':'0',
					'range':'',
					'widgetparams':''
				}
			},
			'BOOL':{
				'hide':['field_size','field_size_min','match','widgetparams'],
				'val':{
					'field_size':0,
					'default':0,
					'range':'1==".Module::t('Yes').";0==".Module::t('No')."',
					'widgetparams':''
				}
			},
			'BLOB':{
				'hide':['field_size','field_size_min','match','widgetparams'],
				'val':{
					'field_size':0,
					'default':'',
					'range':'',
					'widgetparams':''
				}
			},
			'BINARY':{
				'hide':['field_size','field_size_min','match','widgetparams'],
				'val':{
					'field_size':0,
					'default':'',
					'range':'',
					'widgetparams':''
				}
			}
		};



	function showWidgetList(type) {
		$('div.widget select').empty();
		$('div.widget select').append('<option value=\"\">".Module::t('No')."</option>');
		if (wgByType[type]) {
			for (var k in wgByType[type]) {
				$('div.widget select')
				    .append('<option value=\"'+wgByType[type][k]+'\">'+widgets[wgByType[type][k]]['label']+'</option>');
			}
		}
	}

	function setFields(type) {
		if (fieldType[type]) {
			if (".((isset($_GET['id']))?0:1).") {
				showWidgetList(type);
				$('#widgetlist option:first').attr('selected', 'selected');
			}

			$('div.form-group').addClass('toshow').removeClass('tohide');
			if (fieldType[type].hide.length) {
			    $('div.'+fieldType[type].hide.join(', div.')).addClass('tohide').removeClass('toshow');
			}
			if ($('div.widget select').val()) {
				$('div.widgetparams').removeClass('tohide');
			}
			$('div.toshow').show(500);
			$('div.tohide').hide(500);
			".((!isset($_GET['id']))?"
			for (var k in fieldType[type].val) {
				$('div.'+k+' input').val(fieldType[type].val[k]);
			}":'')."
		}
	}

	function isArray(obj) {
		if (obj.constructor.toString().indexOf('Array') == -1)
			return false;
		else
			return true;
	}

	$('#dialog-form').dialog({
		autoOpen: false,
		height: 400,
		width: 400,
		modal: true,
		buttons: {
			'".Module::t('Save')."': function() {
				var wparam = {};
				var fparam = {};
				$('#dialog-form fieldset .wparam').each(function(){
					if ($(this).val()) wparam[$(this).attr('name')] = $(this).val();
				});

				var tab = $('#tabs ul li.ui-tabs-active').text();
				fparam[tab] = {};
				$('#dialog-form fieldset .tab-'+tab).each(function(){
					if ($(this).val()) fparam[tab][$(this).attr('name')] = $(this).val();
				});

				if ($.JSON.encode(wparam)!='{}') $('div.widgetparams input').val($.JSON.encode(wparam));
				if ($.JSON.encode(fparam[tab])!='{}') $('div.other_validator input').val($.JSON.encode(fparam));

		    $('#widgetparams').blur();
				$(this).dialog('close');
		    $('#widgetparams').blur();
			},
			'".Module::t('Cancel')."': function() {
		    $('#widgetparams').blur();
				$(this).dialog('close');
		    $('#widgetparams').blur();
			}
		},
		close: function() {
		}
	});


	$('#widgetparams').click(function() {
		var widget = widgets[$('#widgetlist').val()];
		var html = '';
		var wparam = ($('div.widgetparams input').val())?$.JSON.decode($('div.widgetparams input').val()):{};
		var fparam = ($('div.other_validator input').val())?$.JSON.decode($('div.other_validator input').val()):{};

		// Class params
		for (var k in widget.params) {
			html += '<label for=\"name\">'+((widget.paramsLabels[k])?widget.paramsLabels[k]:k)+'</label>';
			html += '<input type=\"text\" name=\"'+k+'\" id=\"widget_'+k+
			'\" class=\"text wparam ui-widget-content ui-corner-all\" value=\"'+
			    ((wparam[k])?wparam[k]:widget.params[k])+'\" />';
		}
		// Validator params
		if (widget.other_validator) {
			var tabs = '';
			var li = '';
			for (var t in widget.other_validator) {
				tabs += '<div id=\"tab-'+t+'\" class=\"tab\">';
				li += '<li'+((fparam[t])?' class=\"ui-tabs-selected\"':'')+'><a href=\"#tab-'+t+'\">'+t+'</a></li>';

				for (var k in widget.other_validator[t]) {
				    var property = widget.other_validator[t][k];
					if (typeof property == 'object' && !isArray(property)) {
					    if(property['label']) {
					        tabs += '<label for=\"name\">'+(property['label'])+'</label>';
					    } else {
					        tabs += '<label for=\"name\">'+((widget.paramsLabels[k])?widget.paramsLabels[k]:k)+'</label>';
					    }

					    if (isArray(property['value'])) {
                            tabs += '<select type=\"text\" name=\"'+k+'\" id=\"filter_'+k+
                            '\" class=\"text fparam ui-widget-content ui-corner-all tab-'+t+'\">';
                            for (var i in property['value']) {
                                tabs += '<option value=\"'+property['value'][i]+'\"'+
                                ((fparam[t]&&fparam[t][k])?' selected=\"selected\"':'')+'>'+
                                property['value'][i]+'</option>';
                            }
                            tabs += '</select>';
						} else {
						    tabs += '<input type=\"text\" name=\"'+k+'\" id=\"filter_'+k+
                            '\" class=\"text fparam ui-widget-content ui-corner-all tab-'+t+'\" value=\"'+
                            ((fparam[t]&&fparam[t][k])?fparam[t][k]:property['value'])+'\" />';
                        }
					} else if (isArray(property)) {
                            tabs += '<select type=\"text\" name=\"'+k+'\" id=\"filter_'+k+
                            '\" class=\"text fparam ui-widget-content ui-corner-all tab-'+t+'\">';
                            for (var i in property) {
                                tabs += '<option value=\"'+property[i]+'\"'+
                                ((fparam[t]&&fparam[t][k])?' selected=\"selected\"':'')+'>'+
                                property['value'][i]+'</option>';
                            }
                            tabs += '</select>';
					} else {
					    tabs += '<label for=\"name\">'+((widget.paramsLabels[k])?widget.paramsLabels[k]:k)+'</label>';
						tabs += '<input type=\"text\" name=\"'+k+'\" id=\"filter_'+k+
						'\" class=\"text fparam ui-widget-content ui-corner-all tab-'+t+'\" value=\"'+
						((fparam[t]&&fparam[t][k])?fparam[t][k]:widget.other_validator[t][k])+'\" />';
					}
				}
				tabs += '</div>';
			}
			html += '<div id=\"tabs\"><ul>'+li+'</ul>'+tabs+'</div>';
		}

		$('#dialog-form fieldset').html(html);

		$('#tabs').tabs();

		// Show form
		$('#dialog-form').dialog('open');
	});

	$('#field_type').change(function() {
		setFields($(this).val());
	});

	$('#widgetlist').change(function() {
		if ($(this).val()) {
			$('div.widgetparams').show(500);
		} else {
			$('div.widgetparams').hide(500);
		}

	});

	// show all function
	$('div.form p.note').append('<br/><a href=\"#\" id=\"showAll\">".Module::t('Show all')."</a>');
 	$('#showAll').click(function(){
		$('div.form-group').show(500);
		return false;
	});

	// init
	setFields($('#field_type').val());

	";
        $this->view->registerJs($js);

    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model=new ProfileField;
        $scheme = get_class(Yii::$app->db->schema);
        if ($model->load(Yii::$app->request->post())) {
            $model->loadDefaultValues(true);
            if ($model->validate()) {
                $sql = 'ALTER TABLE '.Profile::tableName().' ADD `'.$model->varname.'` '
                    .$this->fieldType($model->field_type);
                if (
                    $model->field_type!='TEXT'
                    && $model->field_type!='DATE'
                    && $model->field_type!='BOOL'
                    && $model->field_type!='BLOB'
                    && $model->field_type!='BINARY'
                ) {
                    $sql .= '(' . $model->field_size . ')';
                }
                $sql .= ' NOT NULL';

                if ($model->field_type!='TEXT'&&$model->field_type!='BLOB'||$scheme!='yii\db\mysql\Schema') {
                    if (!empty($model->default)) {
                        $sql .= " DEFAULT '".$model->default."'";
                    } else {
                        $sql .= ((
                            $model->field_type=='TEXT'
                            ||$model->field_type=='VARCHAR'
                            ||$model->field_type=='BLOB'
                            ||$model->field_type=='BINARY'
                        )?" DEFAULT ''":(($model->field_type=='DATE')?" DEFAULT '0000-00-00'":" DEFAULT 0"));
                    }
                }
                $model->getDb()->createCommand($sql)->execute();
                $model->save();
                $this->redirect(['view', 'id' =>$model->id]);
            }
        }

        $this->registerScript();
        return $this->render('create', [
            'model'=>$model,
        ]);
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate()
    {
        $model=$this->loadModel();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $this->redirect(['view', 'id' =>$model->id]);
            }
        }
        $this->registerScript();

        return $this->render('update', [
            'model'=>$model,
        ]);
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     */
    public function actionDelete()
    {
        if (Yii::$app->request->isPost) {
            // we only allow deletion via POST request
            $scheme = get_class(Yii::$app->db->schema);
            $model = $this->loadModel();
            if ($scheme=='yii\db\sqlite\Schema') {
                $attr = Profile::getFields();
                unset($attr[$model->varname]);
                $attr = array_keys($attr);
                $connection=Yii::$app->db;
                $transaction=$connection->beginTransaction();
                $status=true;
                try {
                    $connection->createCommand(
                        "CREATE TEMPORARY TABLE ".Profile::tableName()."_backup (".implode(',', $attr).")"
                    )->execute();

                    $connection->createCommand(
                        "INSERT INTO ".Profile::tableName()."_backup SELECT ".implode(',', $attr).
                        " FROM ".Profile::tableName()
                    )->execute();

                    $connection->createCommand(
                        "DROP TABLE ".Profile::tableName()
                    )->execute();

                    $connection->createCommand(
                        "CREATE TABLE ".Profile::tableName()." (".implode(',', $attr).")"
                    )->execute();

                    $connection->createCommand(
                        "INSERT INTO ".Profile::tableName()." SELECT ".implode(',', $attr).
                        " FROM ".Profile::tableName()."_backup"
                    )->execute();

                    $connection->createCommand(
                        "DROP TABLE ".Profile::tableName()."_backup"
                    )->execute();

                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollBack();
                    $status=false;
                }
                if ($status) {
                    $model->delete();
                }

            } else {
                try {
                    $model->getDb()->createCommand()
                        ->dropColumn(Profile::tableName(), $model->varname)->execute();
                } catch (\yii\db\Exception $e) {
                    Yii::$app->user->setFlash(
                        Module::ALERT_ERROR,
                        Module::t('Error deleteing Profile table column {column}', ['column' => $model->varname])
                    );
                }
                $model->delete();
            }

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!Yii::$app->request->isAjax) {
                $this->redirect(['admin']);
            }
        } else {
            throw new HttpException(400, Module::t('Invalid request.'));
        }
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $searchModel=new ProfileFieldSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     */
    public function loadModel()
    {
        if ($this->model === null) {
            if (Yii::$app->request->get('id')) {
                $this->model=ProfileField::findOne(Yii::$app->request->get('id'));
            }
            if ($this->model === null) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
        return $this->model;
    }

    /**
     * MySQL field type
     * @param $type string
     * @return string
     */
    public function fieldType($type)
    {
        $type = str_replace('UNIX-DATE', 'INTEGER', $type);
        return $type;
    }

    public static function getWidgets($fieldType = '')
    {
        $basePath=Yii::getAlias('@marsoltys/yii2user/components');
        $widgets = [];
        $list = ['' =>Module::t('No')];
        if (self::$widgets) {
            $widgets = self::$widgets;
        } else {
            $d = dir($basePath);
            while (false !== ($file = $d->read())) {
                if (strpos($file, 'UW') === 0) {
                    list($className) = explode('.', $file);
                    if (class_exists('\\marsoltys\\yii2user\\components\\'.$className)) {
                        /**@var \marsoltys\yii2user\components\UWfile $widgetClass - this is to trick IDEs*/
                        $cs = '\\marsoltys\\yii2user\\components\\'.$className;
                        $widgetClass = new $cs;
                        if ($widgetClass->init()) {
                            $widgets[$className] = $widgetClass->init();
                            if ($fieldType) {
                                if (in_array($fieldType, $widgets[$className]['fieldType'])) {
                                    $list[$className] = $widgets[$className]['label'];
                                }
                            } else {
                                $list[$className] = $widgets[$className]['label'];
                            }
                        }
                    }
                }
            }
            $d->close();
        }
        return [$list,$widgets];
    }

    /**
     * Get Values for Dependent DropDownList.
     * @author juan.gaviria@dsotogroup.com | Mariusz Soltys (https://github.com/marsoltys)
     */
    public function actionGetDroDownDepValues()
    {
        $post = $_POST;
        /**@var \yii\db\ActiveRecord $model*/
        $model = new $post['model'];

        $query = $model::findAll([$post['varname'] => $post[$post['varname']]]);

        $data = ArrayHelper::map($query, 'id', $post['optionDestName']);
        echo Html::renderSelectOptions('', $data, $options = ['prompt' => 'Select...']);
    }

    /**
     * Performs the AJAX validation.
     * @param \yii\base\Model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (Yii::$app->request->isAjax && $model) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            echo ActiveForm::validate($model);
            Yii::$app->end();
        }
    }
}
