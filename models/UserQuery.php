<?php

namespace marsoltys\yii2user\models;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @see User
 */
class UserQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return UserQuery
    */
    public function active()
    {
        return $this->andWhere(['status' => User::STATUS_ACTIVE]);
    }

    /**
     * @return UserQuery
     */
    public function notactive()
    {
        return $this->andWhere(['status' => User::STATUS_NOACTIVE]);
    }

    /**
     * @return UserQuery
     */
    public function banned()
    {
        return $this->andWhere(['status' => User::STATUS_BANNED]);
    }

    /**
     * @return UserQuery
     */
    public function superuser()
    {
        return $this->andWhere(['superuser' => 1]);
    }

    /**
     * @return UserQuery
     */
    public function notsafe()
    {
        return $this->select(['id', 'username', 'password', 'email', 'activkey', 'create_at', 'lastvisit_at', 'superuser', 'status']);
    }

    /**
     * @return UserQuery
     */
    public function findbyPk($condition)
    {
        $primaryKey = User::primaryKey();
        if (isset($primaryKey[0])) {
            $condition = [$primaryKey[0] => $condition];
        }
        return $this->andWhere($condition);
    }
}