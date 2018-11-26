<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Operation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Operations', 'url' => ['index', 'accountId' => $model->source_account_id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="operation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'source_account_id',
            'target_account_id',
            'amount',
            'datetime',
            'status',
        ],
    ]) ?>

</div>
