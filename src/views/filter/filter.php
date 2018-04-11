<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$widgetId = $formOptions['id'];
$listId = $formOptions['options']['data-list-id'];
$action = $formOptions['action'];
?>

<?php $form = ActiveForm::begin($formOptions); ?>
    <?php foreach ($filter->getParameters() as $parameter): ?>
        <?php if (! $parameter->hasValues()) continue; ?>

        <div class="h4"><?= $parameter->title ?></div>

        <?php if ($parameter->isCollection()): ?>
            <?php foreach ($parameter->getCollections() as $parameter): ?>
                <?php if ($parameter->hasValues()): ?>
                    <div class="h5"><?= $parameter->title ?></div>

                    <div class="clearfix">
                        <?= $this->render($parameter->isRange() ? '_range' : '_checkbox', [
                            'widgetId' => $widgetId,
                            'parameter' => $parameter,
                            'linkLabels' => $linkLabels,
                            'maxShown' => $collapseMoreThen,
                            'linkLabels' => $linkLabels
                        ]) ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <?php if ($parameter->hasValues()): ?>
                <div class="clearfix">
                    <?= $this->render($parameter->isRange() ? '_range' : '_checkbox', [
                        'widgetId' => $widgetId,
                        'parameter' => $parameter,
                        'linkLabels' => $linkLabels,
                        'maxShown' => $collapseMoreThen,
                        'linkLabels' => $linkLabels
                    ]) ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>

    <?= Html::hiddenInput($sortName, $sort, ['class' => "form-control list-filter-form-$sortName-input"]) ?>
    <?= Html::hiddenInput($limitName, $limit, ['class' => "form-control list-filter-form-$limitName-input"]) ?>
    <?php foreach ($hiddenParameters as $key => $value): ?>
        <?= Html::hiddenInput($key, $value, ['class' => "form-control list-filter-form-$key-input"]) ?>
    <?php endforeach; ?>

    <br />
    <?= Html::a('Сбросить фильтр', "$action#$listId", ['class' => 'btn btn-primary btn-xs list-filter-form-reset']) ?>
    <?= Html::button('Пересобрать', ['type' => 'submit', 'class' => 'btn btn-success btn-xs list-filter-form-send']) ?>
<?php ActiveForm::end(); ?>