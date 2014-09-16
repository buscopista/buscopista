<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\PasswordForm $model
 */
$this->title = Yii::t('app', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = Yii::t('app', 'Change password');
?>
<div class="account-password">
    
    <h1><?= Html::encode($this->title) ?></h1>
    
    <!-- Nav tabs -->
    <ul id="settings-tabs" class="nav nav-tabs" role="tablist">
      <li><a href="<?= Url::to(['/account']) ?>" role="tab"><?= Html::encode(Yii::t('app', 'Account')) ?></a></li>
      <li class="active"><a href="#password" role="tab" data-toggle="tab"><?= Html::encode(Yii::t('app', 'Password')) ?></a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <p><?= Html::encode(Yii::t('app', 'Change your password or recover your current one.')) ?></p>
        <?php $form = ActiveForm::begin([
            'id' => 'password-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}\n{hint}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]); ?>

        <?= $form->field($model, 'old_password')->passwordInput()
                 ->hint(Html::a(Yii::t('app', 'Forgot password?'), ['/account/forgot'])) ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'repeat_password')->passwordInput() ?>

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