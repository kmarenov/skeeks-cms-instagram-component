<?php
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab as ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \skeeks\cms\models\WidgetConfig */

?>
<?php $form = ActiveForm::begin(); ?>


<?= $form->fieldSet('Основное'); ?>
<?= $form->field($model, 'accessToken')->hint('Ваш Access Token для доступа к API Instagram'); ?>
<?= $form->fieldInputInt($model, 'userId')->hint('ID пользователя Instagram, фотографии которого нужно показывать'); ?>
<?= $form->fieldSetEnd(); ?>

<?= $form->buttonsCreateOrUpdate($model); ?>
<?php ActiveForm::end(); ?>


