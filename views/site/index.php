<?php

use app\models\Account;
use app\models\Operation;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Home';
?>
<div class="site-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'name',
                'value' => function (Account $model) {
                    return Html::a(Html::encode($model->name), Url::toRoute(['operation/index', 'accountId' => $model->id]));
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'Last Operation',
                'value' => function (Account $model) {
                    $operation = $model->getLastOperation();
                    if (!$operation instanceof Operation)
                        return null;
                    return Html::a(Html::encode(
                        $operation->amount . ' to ' .
                        $operation->targetAccount->name .' (' . $operation->target_account_id . ')'
                    ), Url::toRoute(['operation/view', 'id' => $operation->id]));
                },
                'format' => 'raw',
            ],
        ],
    ]); ?>

</div>
