<?php

namespace app\commands;

use app\models\Account;
use app\models\Operation;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Expression;

class OperationController extends Controller
{
    /**
     * @param int $limit
     * @return int Exit code
     */
    public function actionPerform($limit = 1000)
    {
        $operations = Operation::find()
            ->where(['status' => Operation::STATUS_PLANNED])
            ->andWhere(['<=', 'datetime', new Expression('now()')])
            ->orderBy(['id' => SORT_ASC])
            ->limit($limit)
            ->all();

        foreach ($operations as $operation) {
            $operation->scenario = Operation::SCENARIO_INTERNAL;
            $sourceAccount = $operation->sourceAccount;
            if ($sourceAccount->is_internal) {
                $status = Operation::STATUS_COMPLETED;
            } else {
                $balance = Account::getCurrentBalance($sourceAccount);
                if ($operation->amount <= $balance) {
                    $status = Operation::STATUS_COMPLETED;
                } else {
                    $status = Operation::STATUS_REJECTED;
                }
            }
            $operation->status = $status;
            $operation->save();
        }

        return ExitCode::OK;
    }
}
