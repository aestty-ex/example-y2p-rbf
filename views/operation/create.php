<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $balance int */
/* @var $model app\models\Operation */

$this->title = 'Create Operation';
$this->params['breadcrumbs'][] = ['label' => 'Operations', 'url' => ['index', 'accountId' => $model->source_account_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'balance' => $balance,
        'model' => $model,
    ]) ?>

</div>
