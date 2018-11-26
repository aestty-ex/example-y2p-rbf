<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property string $name
 * @property bool $is_internal
 *
 * @property Operation[] $operations
 * @property Operation[] $operations0
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['is_internal'], 'boolean'],
            [['is_internal'], 'default', 'value' => false],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'is_internal' => 'Is Internal',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperations()
    {
        return $this->hasMany(Operation::className(), ['source_account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperations0()
    {
        return $this->hasMany(Operation::className(), ['target_account_id' => 'id']);
    }

    /**
     * @return Operation|null
     */
    public function getLastOperation()
    {
        return $this->hasMany(Operation::className(), ['source_account_id' => 'id'])
            ->where(['status' => Operation::STATUS_COMPLETED])
            ->orderBy(['id' => SORT_DESC])
            ->one();
    }

    /**
     * @return array
     */
    public static function getAutocomplete()
    {
        return Account::find()
            ->select(['id AS value', 'name AS label'])
            ->asArray()
            ->all();
    }

    /**
     * @param Account $account
     * @return int
     */
    public static function getCurrentBalance($account)
    {
        $command = Yii::$app->getDb()->createCommand("
            SELECT SUM(amount) AS balance FROM (
                SELECT 0 AS amount

                UNION

                SELECT SUM(amount * -1) AS amount
                FROM Operation
                WHERE source_account_id = :accountId
                AND status = :completed

                UNION

                SELECT SUM(amount) AS amount
                FROM operation
                WHERE target_account_id = :accountId
                AND status = :completed
            ) AS o
        ", [
            'accountId' => $account->id,
            'completed' => Operation::STATUS_COMPLETED,
        ]);
        $result = $command->queryOne();

        return (int)$result['balance'];
    }

    /**
     * @param Account $account
     * @return int
     */
    public static function getPlannedBalance($account)
    {
        $command = Yii::$app->getDb()->createCommand("
            SELECT SUM(amount) AS balance FROM (
                SELECT 0 AS amount

                UNION

                SELECT SUM(amount * -1) AS amount
                FROM Operation
                WHERE source_account_id = :accountId
                AND status IN (:planned, :completed)

                UNION

                SELECT SUM(amount) AS amount
                FROM operation
                WHERE target_account_id = :accountId
                AND status = :completed
            ) AS o
        ", [
            'accountId' => $account->id,
            'planned' => Operation::STATUS_PLANNED,
            'completed' => Operation::STATUS_COMPLETED,
        ]);
        $result = $command->queryOne();

        return (int)$result['balance'];
    }
}
