<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\AccountForm $modelA
 * @var app\models\PasswordForm $modelP
 */
$this->title = Yii::t('app', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-index">
    
    <h1><?= Html::encode($this->title) ?></h1>
    
    <!-- Nav tabs -->
    <ul id="settings-tabs" class="nav nav-tabs" role="tablist">
      <li class="active"><a href="#account" role="tab" data-toggle="tab"><?= Html::encode(Yii::t('app', 'Account')) ?></a></li>
      <li><a href="#password" role="tab" data-toggle="tab"><?= Html::encode(Yii::t('app', 'Password')) ?></a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="account">
            <p><?= Html::encode(Yii::t('app', 'Change your basic account and language settings.')) ?></p>
            <?php $form = ActiveForm::begin([
                'id' => 'account-form',
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                    'labelOptions' => ['class' => 'col-lg-1 control-label'],
                ],
            ]); ?>

            <?= $form->field($modelA, 'username') ?>
            <?= $form->field($modelA, 'mail') ?>

            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::submitButton(Yii::t('app', 'Save changes'), [
                        'class' => 'btn btn-primary col-lg-3', 
                        'name' => 'login-button'
                    ]) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="tab-pane" id="password">
            <p><?= Html::encode(Yii::t('app', 'Change your password or recover your current one.')) ?></p>
            <?php $form = ActiveForm::begin([
                'id' => 'password-form',
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                    'labelOptions' => ['class' => 'col-lg-1 control-label'],
                ],
            ]); ?>

            <?= $form->field($modelP, 'old_password')->passwordInput() ?>
            <div class="form-group field-description">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::a(Yii::t('app', 'Forgot password?'), ['/account/forgot']); ?>
                </div>
            </div>
            <?= $form->field($modelP, 'password')->passwordInput() ?>
            <?= $form->field($modelP, 'repeat_password')->passwordInput() ?>

            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::submitButton(Yii::t('app', 'Change password'), [
                        'class' => 'btn btn-primary col-lg-3', 
                        'name' => 'login-button'
                    ]) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    
    <?php $this->registerJs(
        "$('#settings-tabs a[href=\"#" . Html::encode($tab) ."\"]').tab('show')"
    , View::POS_END) ?>
    
</div>