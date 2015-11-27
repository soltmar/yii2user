<?php

use marsoltys\yii2user\models\User;
use marsoltys\yii2user\Module;
use yii\db\Migration;

class m110805_153437_installYiiUser extends Migration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    private $model;

    public function safeUp()
    {
        if (!Yii::$app->getModule('user')) {
            echo "\n\nAdd to console.php :\n"
                ."'modules'=>array(\n"
                ."...\n"
                ."    'user'=>array(\n"
                ."        ... # copy settings from main config\n"
                ."    ),\n"
                ."...\n"
                ."),\n"
                ."\n";
            return false;
        }

        //*
        switch ($this->dbType()) {
            case "mysql":
                $this->createTable(Module::getInstance()->tableUsers, [
                    "id" => "pk",
                    "username" => "varchar(20) NOT NULL DEFAULT ''",
                    "password" => "varchar(128) NOT NULL DEFAULT ''",
                    "email" => "varchar(128) NOT NULL DEFAULT ''",
                    "activkey" => "varchar(128) NOT NULL DEFAULT ''",
                    "createtime" => "int(10) NOT NULL DEFAULT 0",
                    "lastvisit" => "int(10) NOT NULL DEFAULT 0",
                    "superuser" => "int(1) NOT NULL DEFAULT 0",
                    "status" => "int(1) NOT NULL DEFAULT 0",
                ], $this->MySqlOptions);
                $this->createIndex('user_username', Module::getInstance()->tableUsers, 'username', true);
                $this->createIndex('user_email', Module::getInstance()->tableUsers, 'email', true);
                $this->createTable(Module::getInstance()->tableProfiles, [
                    'user_id' => 'pk',
                    'first_name' => 'string',
                    'last_name' => 'string',
                ], $this->MySqlOptions);
                $this->addForeignKey(
                    'user_profile_id',
                    Module::getInstance()->tableProfiles,
                    'user_id',
                    Module::getInstance()->tableUsers,
                    'id',
                    'CASCADE',
                    'RESTRICT'
                );
                $this->createTable(Module::getInstance()->tableProfileFields, [
                    "id" => "pk",
                    "varname" => "varchar(50) NOT NULL DEFAULT ''",
                    "title" => "varchar(255) NOT NULL DEFAULT ''",
                    "field_type" => "varchar(50) NOT NULL DEFAULT ''",
                    "field_size" => "int(3) NOT NULL DEFAULT 0",
                    "field_size_min" => "int(3) NOT NULL DEFAULT 0",
                    "required" => "int(1) NOT NULL DEFAULT 0",
                    "match" => "varchar(255) NOT NULL DEFAULT ''",
                    "range" => "varchar(255) NOT NULL DEFAULT ''",
                    "error_message" => "varchar(255) NOT NULL DEFAULT ''",
                    "other_validator" => "text",
                    "default" => "varchar(255) NOT NULL DEFAULT ''",
                    "widget" => "varchar(255) NOT NULL DEFAULT ''",
                    "widgetparams" => "text",
                    "position" => "int(3) NOT NULL DEFAULT 0",
                    "visible" => "int(1) NOT NULL DEFAULT 0",
                ], $this->MySqlOptions);
                break;

            case "pgsql":
                $this->createTable(Module::getInstance()->tableUsers, [
                    "id" => "pk",
                    "username" => "varchar(20) NOT NULL DEFAULT ''",
                    "password" => "varchar(128) NOT NULL DEFAULT ''",
                    "email" => "varchar(128) NOT NULL DEFAULT ''",
                    "activkey" => "varchar(128) NOT NULL DEFAULT ''",
                    "createtime" => "int NOT NULL DEFAULT 0",
                    "lastvisit" => "int NOT NULL DEFAULT 0",
                    "superuser" => "int NOT NULL DEFAULT 0",
                    "status" => "int NOT NULL DEFAULT 0",
                ]);
                // Since the admin user will be added with id = 1 we need to fix the sequence counter
                $this->execute("select setval('".Module::getInstance()->tableUsers."_id_seq',1);");
                $this->createIndex('user_username', Module::getInstance()->tableUsers, 'username', true);
                $this->createIndex('user_email', Module::getInstance()->tableUsers, 'email', true);
                $this->createTable(Module::getInstance()->tableProfiles, [
                    'user_id' => 'pk',
                    'first_name' => 'string',
                    'last_name' => 'string',
                ]);
                $this->addForeignKey(
                    'user_profile_id',
                    Module::getInstance()->tableProfiles,
                    'user_id',
                    Module::getInstance()->tableUsers,
                    'id',
                    'CASCADE',
                    'RESTRICT'
                );
                $this->createTable(Module::getInstance()->tableProfileFields, [
                    "id" => "pk",
                    "varname" => "varchar(50) NOT NULL DEFAULT ''",
                    "title" => "varchar(255) NOT NULL DEFAULT ''",
                    "field_type" => "varchar(50) NOT NULL DEFAULT ''",
                    "field_size" => "int NOT NULL DEFAULT 0",
                    "field_size_min" => "int NOT NULL DEFAULT 0",
                    "required" => "int NOT NULL DEFAULT 0",
                    "match" => "varchar(255) NOT NULL DEFAULT ''",
                    "range" => "varchar(255) NOT NULL DEFAULT ''",
                    "error_message" => "varchar(255) NOT NULL DEFAULT ''",
                    "other_validator" => "text",
                    "default" => "varchar(255) NOT NULL DEFAULT ''",
                    "widget" => "varchar(255) NOT NULL DEFAULT ''",
                    "widgetparams" => "text",
                    "position" => "int NOT NULL DEFAULT 0",
                    "visible" => "int NOT NULL DEFAULT 0",
                ]);
                break;

            case "sqlite":
            default:
                $this->createTable(Module::getInstance()->tableUsers, [
                    "id" => "pk",
                    "username" => "varchar(20) NOT NULL",
                    "password" => "varchar(128) NOT NULL",
                    "email" => "varchar(128) NOT NULL",
                    "activkey" => "varchar(128) NOT NULL",
                    "createtime" => "int(10) NOT NULL",
                    "lastvisit" => "int(10) NOT NULL",
                    "superuser" => "int(1) NOT NULL",
                    "status" => "int(1) NOT NULL",
                ]);
                $this->createIndex('user_username', Module::getInstance()->tableUsers, 'username', true);
                $this->createIndex('user_email', Module::getInstance()->tableUsers, 'email', true);
                $this->createTable(Module::getInstance()->tableProfiles, [
                    'user_id' => 'pk',
                    'first_name' => 'string',
                    'last_name' => 'string',
                ]);
                $this->createTable(Module::getInstance()->tableProfileFields, [
                    "id" => "pk",
                    "varname" => "varchar(50) NOT NULL",
                    "title" => "varchar(255) NOT NULL",
                    "field_type" => "varchar(50) NOT NULL",
                    "field_size" => "int(3) NOT NULL",
                    "field_size_min" => "int(3) NOT NULL",
                    "required" => "int(1) NOT NULL",
                    "match" => "varchar(255) NOT NULL",
                    "range" => "varchar(255) NOT NULL",
                    "error_message" => "varchar(255) NOT NULL",
                    "other_validator" => "text NOT NULL",
                    "default" => "varchar(255) NOT NULL",
                    "widget" => "varchar(255) NOT NULL",
                    "widgetparams" => "text NOT NULL",
                    "position" => "int(3) NOT NULL",
                    "visible" => "int(1) NOT NULL",
                ]);

                break;
        }//*/

        if (in_array('--interactive=0', $_SERVER['argv'])) {
            if (!$this->model) {
                $this->model = new User;
            }
            $this->model->username = 'admin';
            $this->model->email = 'webmaster@example.com';
            $this->model->password = 'admin';
        } else {
            $this->readStdinUser('Admin login', 'username', 'admin');
            $this->readStdinUser('Admin email', 'email', 'webmaster@example.com');
            $this->readStdinUser('Admin password', 'password', 'admin');
        }

        $this->insert(Module::getInstance()->tableUsers, [
            "id" => "1",
            "username" => $this->model->username,
            "password" => Module::getInstance()->encrypting($this->model->password),
            "email" => $this->model->email,
            "activkey" => Module::getInstance()->encrypting(microtime()),
            "createtime" => time(),
            "lastvisit" => "0",
            "superuser" => "1",
            "status" => "1",
        ]);

        $this->insert(Module::getInstance()->tableProfiles, [
            "user_id" => "1",
            "first_name" => "Administrator",
            "last_name" => "Admin",
        ]);

        $this->insert(Module::getInstance()->tableProfileFields, [
            "id" => "1",
            "varname" => "first_name",
            "title" => "First Name",
            "field_type" => "VARCHAR",
            "field_size" => "255",
            "field_size_min" => "3",
            "required" => "2",
            "match" => "",
            "range" => "",
            "error_message" => "Incorrect First Name (length between 3 and 50 characters).",
            "other_validator" => "",
            "default" => "",
            "widget" => "",
            "widgetparams" => "",
            "position" => "1",
            "visible" => "3",
        ]);
        $this->insert(Module::getInstance()->tableProfileFields, [
            "id" => "2",
            "varname" => "last_name",
            "title" => "Last Name",
            "field_type" => "VARCHAR",
            "field_size" => "255",
            "field_size_min" => "3",
            "required" => "2",
            "match" => "",
            "range" => "",
            "error_message" => "Incorrect Last Name (length between 3 and 50 characters).",
            "other_validator" => "",
            "default" => "",
            "widget" => "",
            "widgetparams" => "",
            "position" => "2",
            "visible" => "3",
        ]);

        return true;

    }

    public function safeDown()
    {
        $this->dropTable(Module::getInstance()->tableProfileFields);
        $this->dropTable(Module::getInstance()->tableProfiles);
        $this->dropTable(Module::getInstance()->tableUsers);
    }

    public function dbType()
    {
        echo "type db: ".Yii::$app->db->driverName."\n";
        return Yii::$app->db->driverName;
    }

    private function readStdin($prompt, $valid_inputs, $default = '')
    {

        $input = "";
        $cond = !isset($input)
            || (is_array($valid_inputs) && !in_array($input, $valid_inputs))
            || ($valid_inputs == 'is_file' && !is_file($input));

        while ($cond) {
            echo $prompt;
            $input = strtolower(trim(fgets(STDIN)));
            if (empty($input) && !empty($default)) {
                $input = $default;
            }
        }
        return $input;
    }

    private function readStdinUser($prompt, $field, $default = '')
    {
        if (!$this->model) {
            $this->model = new User;
        }

        while (!isset($input) || !$this->model->validate([$field])) {
            echo $prompt.(($default)?" [$default]":'').': ';
            $input = (trim(fgets(STDIN)));
            if (empty($input) && !empty($default)) {
                $input = $default;
            }
            $this->model->setAttribute($field, $input);
        }
        return $input;
    }
}