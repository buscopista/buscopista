<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
use app\models\User;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\RegisterForm $model
 */
$this->title = Yii::t('app', 'Register');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-register">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to sign up:</p>
    
    <?php $form = ActiveForm::begin([
        'id' => 'register-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'username') ?>
    <?= $form->field($model, 'mail') ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'repeat_password')->passwordInput() ?>
    
    <?php if (empty($this->role)): ?>
        <?= $form->field($model, 'role')->dropDownList(array(
            User::ROLE_SPORT   => Yii::t('app', 'I just want to do sport'),
            User::ROLE_MANAGER => Yii::t('app', 'I am a sport facility manager')
        )) ?>
    <?php else: ?>
        <?= $form->field($model, 'role')->hiddenInput() ?>
    <?php endif; ?>
    <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
        'template' => '<div class="row"><div class="col-lg-6">{image}</div><div class="col-lg-6">{input}</div></div>',
    ]) ?>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Sign up', ['class' => 'btn btn-primary col-lg-3', 'name' => 'login-button']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    
</div>