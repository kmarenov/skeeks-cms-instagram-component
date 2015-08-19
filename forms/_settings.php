<?php
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab as ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \skeeks\cms\models\WidgetConfig */

?>
<?php $form = ActiveForm::begin(); ?>


<?= $form->fieldSet('Основное'); ?>
<?= $form->field($model, 'clientId')->hint('CLIENT ID для доступа к API'); ?>
<?= $form->field($model, 'userName')->hint('Имя пользователя, фотографии которого показывать'); ?>
<?= $form->fieldSetEnd(); ?>

<?= $form->buttonsCreateOrUpdate($model); ?>
<?php ActiveForm::end(); ?>


