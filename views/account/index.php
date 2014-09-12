<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\AccountForm $model
 */
$this->title = Yii::t('app', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
$hint = Yii::t('app', 'If you change your e-mail address, you will need to confirm your account again after automatic logout.');
?>
<div class="account-index">
    
    <h1><?= Html::encode($this->title) ?></h1>
    
    <!-- Nav tabs -->
    <ul id="settings-tabs" class="nav nav-tabs" role="tablist">
      <li class="active"><a href="#account" role="tab" data-toggle="tab"><?= Html::encode(Yii::t('app', 'Account')) ?></a></li>
      <li><a href="<?= Url::to(['/account/password']) ?>" role="tab"><?= Html::encode(Yii::t('app', 'Password')) ?></a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <p><?= Html::encode(Yii::t('app', 'Change your basic account and language settings.')) ?></p>
        <?php $form = ActiveForm::begin([
            'id' => 'account-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}\n{hint}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]); ?>

        <?= $form->field($model, 'username') ?>
        <?= $form->field($model, 'mail')
                 ->hint($hint) ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton(Yii::t('app', 'Save changes'), [
                    'class' => 'btn btn-primary col-lg-3', 
                    'name'  => 'save-button',
                    'id'    => 'save',
                ]) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    
    <!-- Small modal -->
    
    <div id="confirm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="mySmallModalLabel"><?= Yii::t('app', 'Remember') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= $hint ?></p>
                    <button class="btn btn-primary" id="continue"><?= Yii::t('app', 'Continue') ?></button>
                    <button class="btn" data-dismiss="modal"><?= Yii::t('app', 'Cancel') ?></button>
                </div>
            </div>
        </div>
    </div>
    
    <?php $this->registerJs(
        'var confirmed = false;'
        . '$("#account-form").submit(function(e){'
            . 'var oldMail = "' . $model->mail . '";'
            . 'var newMail = this.elements["AccountForm[mail]"].value;'
            . 'if (oldMail !== newMail && !confirmed) {'
                . 'var self = this;'
                . 'e.preventDefault();'
                . '$("#confirm").modal("show")'
                . '.one("click", "#continue", function(e){'
                    . 'confirmed = true;'
                    . 'self.submit();'
                . '});'
            . '}'
        . '})'
    , View::POS_END) ?>
    
</div>