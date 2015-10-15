<?php

namespace mariusz_soltys\yii2user\models;

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

    public function active()
    {
        return $this->andWhere(['status' => User::STATUS_ACTIVE]);
    }

    public function  notactive()
    {
        return $this->andWhere(['status' => User::STATUS_NOACTIVE]);
    }

    public function  banned()
    {
        return $this->andWhere(['status' => User::STATUS_BANNED]);
    }

    public function  superuser()
    {
        return $this->andWhere(['superuser' => 1]);
    }

    public function  notsafe()
    {
        return $this->addSelect(['id', 'username', 'password', 'email', 'activkey', 'create_at', 'lastvisit_at', 'superuser', 'status']);
    }
}