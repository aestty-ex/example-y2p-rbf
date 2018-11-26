<?php

use app\models\Account;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $balance int */
/* @var $model app\models\Operation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="operation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $this->render('_autocomplete', [
        'model' => $model,
        'form' => $form,
        'field' => 'target_account_id',
        'account' => $model->targetAccount,
    ]); ?>

    <?= $form->field($model, 'amount')->textInput() ?>
    <div class="alert alert-info" role="alert">
        Your balance is <?= $balance; ?>.
    </div>

    <?= $form->field($model, 'datetime')->widget(DateTimePicker::classname(), [
        'type' => DateTimePicker::TYPE_INPUT,
        'convertFormat' => true,
        'pluginOptions' => [
            'format' => 'yyyy-MM-dd H:i',
            'startDate' => (new DateTime())->format('Y-m-d'),
            'todayHighlight' => true
        ]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
