<?php

use app\models\Account;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Operation */
/* @var $form yii\widgets\ActiveForm */
/* @var $field string */
/* @var $account Account */
?>

<?= $form->field($model, $field)->begin(); ?>
<?= Html::activeLabel($model, $field); ?>
<?= Html::activeHiddenInput($model, $field); ?>
<?= AutoComplete::widget([
        'value' => !empty($account) ? $account->name : '',
        'clientOptions' => [
            'source' => array_map(function($item) {
                    $item['label'] = $item['label'] . ' (' . $item['value'] . ')';
                    return $item;
                }, Account::getAutocomplete()),
            'select' => new JsExpression("function(event, ui) {
                $('#operation-$field').val(ui.item.value);
                this.value = ui.item.label;
                return false;
            }"),
        ],
        'options' => [
            'class'=>'form-control'
        ]
    ]);
?>
<?= Html::error($model, $field, ['class' => 'help-block']); ?>
<?= $form->field($model, $field)->end(); ?>
