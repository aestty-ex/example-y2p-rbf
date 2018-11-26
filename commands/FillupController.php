<?php

namespace app\commands;

use app\models\Account;
use app\models\Operation;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Expression;

class FillupController extends Controller
{
    /**
     * @param int $amount
     * @return int Exit code
     */
    public function actionAddAccount($amount = 1)
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < $amount; $i++) {
            $account = new Account();
            $account->name = $faker->name();
            $account->save();
        }

        return ExitCode::OK;
    }

    /**
     * @param int $amount
     * @return int Exit code
     */
    public function actionAddOperation($amount = 1)
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < $amount; $i++) {
            $sourceAccount = Account::find()
                ->orderBy(new Expression('random()'))
                ->one();
            if (!$sourceAccount instanceof Account) {
                continue;
            }
            $targetAccount = Account::find()
                ->where(['!=', 'id', $sourceAccount->id])
                ->orderBy(new Expression('random()'))
                ->one();
            if (!$targetAccount instanceof Account) {
                continue;
            }

            $operation = new Operation();
            $operation->source_account_id = $sourceAccount->id;
            $operation->target_account_id = $targetAccount->id;
            $operation->datetime = $faker->dateTime()->format('Y-m-d H:i');
            $operation->amount = $faker->numberBetween(1, 10000);
            $operation->save();
        }

        return ExitCode::OK;
    }

    /**
     * @return int Exit code
     */
    public function actionAddOperationFromInternal()
    {
        $sourceAccount = Account::find()
            ->where(['is_internal' => true])
            ->orderBy(new Expression('random()'))
            ->one();
        if (!$sourceAccount instanceof Account) {
            return ExitCode::OK;
        }

        $targetAccounts = Account::find()
            ->where(['is_internal' => false])
            ->all();

        $faker = \Faker\Factory::create();
        foreach ($targetAccounts as $targetAccount) {
            $operation = new Operation();
            $operation->source_account_id = $sourceAccount->id;
            $operation->target_account_id = $targetAccount->id;
            $operation->datetime = '1970-01-01 00:00';
            $operation->amount = $faker->numberBetween(1, 1000000);
            $operation->status = Operation::STATUS_COMPLETED;
            $operation->save();
        }

        return ExitCode::OK;
    }
}
