<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "operation".
 *
 * @property int $id
 * @property int $source_account_id
 * @property int $target_account_id
 * @property int $amount
 * @property string $datetime
 * @property string $status
 *
 * @property Account $sourceAccount
 * @property Account $targetAccount
 */
class Operation extends \yii\db\ActiveRecord
{
    const SCENARIO_INTERNAL = 'internal';

    const STATUS_REJECTED = 'rejected';
    const STATUS_PLANNED = 'planned';
    const STATUS_COMPLETED = 'completed';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'operation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['source_account_id', 'target_account_id', 'amount'], 'required'],
            [['source_account_id', 'target_account_id', 'amount'], 'default', 'value' => null],
            [['source_account_id', 'target_account_id', 'amount'], 'integer'],
            ['target_account_id', 'compare', 'compareAttribute' => 'source_account_id', 'operator' => '!='],
            [['amount'], 'integer', 'min' => 1],
            [['amount'], 'integer', 'max' => 1000000000],
            ['amount', 'validateAmount', 'except' => self::SCENARIO_INTERNAL],
            [['datetime'], 'required'],
            [['status'], 'string'],
            [['source_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['source_account_id' => 'id']],
            [['target_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['target_account_id' => 'id']],
        ];
    }

    /**
     * Validates amount.
     * 
     * @param string $attribute
     * @return boolean
     */
    public function validateAmount($attribute)
    {
        $amount = (int)$this->$attribute;
        $account = Account::findOne($this->source_account_id);
        if (!$account instanceof Account) {
            return false;
        }
        if ($account->is_internal) {
            // Do not check balance for internal accounts
            return true;
        }
        $balance = Account::getPlannedBalance($account);
        if ($balance == 0) {
            $this->addError($attribute, "You do not have funds in your account.");
            return false;
        }
        if ($amount > $balance) {
            $this->addError($attribute, "You have insufficient funds in your account. The amount must be less or equal to $balance.");
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source_account_id' => 'Source Account',
            'target_account_id' => 'Target Account',
            'amount' => 'Amount',
            'datetime' => 'Datetime',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSourceAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'source_account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTargetAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'target_account_id']);
    }
}
