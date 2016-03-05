<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \skeeks\cms\models\WidgetConfig */

?>

<?= $form->fieldSet('Основное'); ?>
<?= $form->field($model, 'clientId')->hint('CLIENT ID для доступа к API'); ?>
<?= $form->field($model, 'userName')->hint('Имя пользователя Instagram'); ?>
<?= $form->field($model, 'tag')->hint('Тэг'); ?>
<?= $form->field($model, 'count'); ?>
<?= $form->fieldSetEnd(); ?>

<?= $form->fieldSet('Кэширование'); ?>
<?= $form->fieldCheckboxBoolean($model, 'isCacheEnabled')->hint('Включить кэширование') ?>
<?= $form->field($model, 'cacheTime')->hint('Время кэширования (в секундах)'); ?>
<?= $form->fieldSetEnd(); ?>
