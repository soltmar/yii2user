<?php

use mariusz_soltys\yii2user\Module;
use yii\db\Migration;

class m110810_162301_userTimestampFix extends Migration
{
    public function safeUp()
    {
        if (!Module::getInstance()) {
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

        switch ($this->dbType()) {
            case "mysql":
                $this->addColumn(
                    Module::getInstance()->tableUsers,
                    'create_at',
                    "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP"
                );
                $this->addColumn(
                    Module::getInstance()->tableUsers,
                    'lastvisit_at',
                    "TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'"
                );
                $this->execute(
                    "UPDATE ".Module::getInstance()->tableUsers." "
                    ."SET create_at = FROM_UNIXTIME(createtime), "
                    ."lastvisit_at = IF(lastvisit,FROM_UNIXTIME(lastvisit),'0000-00-00 00:00:00')"
                );
                $this->dropColumn(Module::getInstance()->tableUsers, 'createtime');
                $this->dropColumn(Module::getInstance()->tableUsers, 'lastvisit');
                break;
            case "pgsql":
                $this->addColumn(
                    Module::getInstance()->tableUsers,
                    'create_at',
                    "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP"
                );
                $this->addColumn(
                    Module::getInstance()->tableUsers,
                    'lastvisit_at',
                    "TIMESTAMP NOT NULL DEFAULT '1970-01-01 01:00:00'"
                );
                $this->execute(
                    "UPDATE ".Module::getInstance()->tableUsers
                    ." SET create_at = to_timestamp(createtime), lastvisit_at = CASE"
                    ."WHEN lastvisit is NOT NULL THEN to_timestamp(lastvisit) ELSE '1970-01-01 01:00:00' END"
                );
                $this->dropColumn(Module::getInstance()->tableUsers, 'createtime');
                $this->dropColumn(Module::getInstance()->tableUsers, 'lastvisit');
                break;
            case "sqlite":
            default:
                $this->addColumn(Module::getInstance()->tableUsers, 'create_at', "TIMESTAMP");
                $this->addColumn(Module::getInstance()->tableUsers, 'lastvisit_at', "TIMESTAMP");
                $this->execute(
                    "UPDATE `".Module::getInstance()->tableUsers."` "
                    ."SET create_at = datetime(createtime, 'unixepoch'), "
                    ."lastvisit_at = datetime(lastvisit, 'unixepoch')"
                );
                $this->execute("ALTER TABLE '".Module::getInstance()->tableUsers."' "
                ."RENAME TO '".__CLASS__."_".Module::getInstance()->tableUsers."'");
                $this->createTable(Module::getInstance()->tableUsers, array(
                    "id" => "pk",
                    "username" => "varchar(20) NOT NULL",
                    "password" => "varchar(128) NOT NULL",
                    "email" => "varchar(128) NOT NULL",
                    "activkey" => "varchar(128) NOT NULL",
                    "superuser" => "int(1) NOT NULL",
                    "status" => "int(1) NOT NULL",
                    "create_at" => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
                    "lastvisit_at" => "TIMESTAMP",
                ));
                $this->execute(
                    'INSERT INTO "'.Module::getInstance()->tableUsers.'" "'
                    .'SELECT "id","username","password","email","activkey","superuser","status","create_at","lastvisit_at" FROM "'.__CLASS__.'_'.Module::getInstance()->tableUsers.'"'
                );
                $this->dropTable(__CLASS__.'_'.Module::getInstance()->tableUsers);
                break;
        }
    }

    public function safeDown()
    {

        switch ($this->dbType()) {
            case "mysql":
                $this->addColumn(Module::getInstance()->tableUsers, 'createtime', "int(10) NOT NULL");
                $this->addColumn(Module::getInstance()->tableUsers, 'lastvisit', "int(10) NOT NULL");
                $this->execute("UPDATE ".Module::getInstance()->tableUsers." SET createtime = UNIX_TIMESTAMP(create_at), lastvisit = UNIX_TIMESTAMP(lastvisit_at)");
                $this->dropColumn(Module::getInstance()->tableUsers, 'create_at');
                $this->dropColumn(Module::getInstance()->tableUsers, 'lastvisit_at');
                break;
            case "pgsql":
                $this->addColumn(Module::getInstance()->tableUsers, 'createtime', "int NOT NULL default 0");
                $this->addColumn(Module::getInstance()->tableUsers, 'lastvisit', "int NOT NULL default 0");
                $this->execute("UPDATE ".Module::getInstance()->tableUsers." SET createtime = extract(epoch from create_at), lastvisit = extract(epoch from lastvisit_at)");
                $this->dropColumn(Module::getInstance()->tableUsers, 'create_at');
                $this->dropColumn(Module::getInstance()->tableUsers, 'lastvisit_at');
                break;
            case "sqlite":
            default:
                $this->addColumn(Module::getInstance()->tableUsers, 'createtime', "int(10)");
                $this->addColumn(Module::getInstance()->tableUsers, 'lastvisit', "int(10)");
                $this->execute("UPDATE ".Module::getInstance()->tableUsers." SET createtime = strftime('%s',create_at), lastvisit = strftime('%s',lastvisit_at)");
                $this->execute('ALTER TABLE "'.Module::getInstance()->tableUsers.'" RENAME TO "'.__CLASS__.'_'.Module::getInstance()->tableUsers.'"');
                $this->createTable(Module::getInstance()->tableUsers, array(
                    "id" => "pk",
                    "username" => "varchar(20) NOT NULL",
                    "password" => "varchar(128) NOT NULL",
                    "email" => "varchar(128) NOT NULL",
                    "activkey" => "varchar(128) NOT NULL",
                    "createtime" => "int(10) NOT NULL",
                    "lastvisit" => "int(10) NOT NULL",
                    "superuser" => "int(1) NOT NULL",
                    "status" => "int(1) NOT NULL",
                ));
                $this->execute('INSERT INTO "'.Module::getInstance()->tableUsers.'" SELECT "id","username","password","email","activkey","createtime","lastvisit","superuser","status" FROM "'.__CLASS__.'_'.Module::getInstance()->tableUsers.'"');
                $this->execute('DROP TABLE "'.__CLASS__.'_'.Module::getInstance()->tableUsers.'"');
                break;
        }
    }

    public function dbType()
    {
        echo "type db: ".Yii::$app->db->driverName."\n";
        return Yii::$app->db->driverName;
    }
}
