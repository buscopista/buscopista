<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\LoginForm $model
 */
$this->title = Yii::t('app', 'Password reset');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-forgot">
    
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Yii::t('app', 'Please fill out your email address:') ?></p>
    
    <?php $form = ActiveForm::begin([
        'id' => 'forgot-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}\n{hint}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>
    
    <?= $form->field($model, 'mail') ?>
    
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton(Yii::t('app', 'Reset password'), ['class' => 'btn btn-primary', 'name' => 'forgot-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
