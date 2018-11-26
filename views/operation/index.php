<?php

use app\models\Operation;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $accountId int */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Operations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Operation', ['create', 'accountId' => $accountId], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'source_account_id',
                'value' => function (Operation $model) {
                    return $model->sourceAccount->name .' (' . $model->source_account_id . ')';
                },
            ],
            [
                'attribute' => 'target_account_id',
                'value' => function (Operation $model) {
                    return $model->targetAccount->name .' (' . $model->target_account_id . ')';
                },
            ],
            'amount',
            'datetime',
            'status',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
