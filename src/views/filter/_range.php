<?php
use yii\helpers\Html;

$options['type'] = 'number';
$options['size'] = 6;
$options['step'] = $parameter->step ?? 'any';
if ($parameter->min !== null && $parameter->max !== null) {
    $options['min'] = $parameter->min;
    $options['max'] = $parameter->max;
    $options['size'] = mb_strlen($parameter->max);
}

$hide = false;
$inputId = str_replace(['[', ']'], '-', $parameter->getInputName());
?>

<div class="list-filter-form-range"><!-- range -->
    <div class="input-group">
        <div class="input-group-addon">от</div>
        <?= Html::input('number', "{$parameter->getInputName()}[from]", $parameter->getSelections()['from'] ?? $parameter->min, $options + ['id' => "{$widgetId}_{$inputId}-from", 'class' => "form-control {$inputId}-from"]) ?>
        <div class="input-group-addon hidden-sm"><?= $parameter->unit ?></div>
    </div>
    <div class="input-group">
        <div class="input-group-addon">до</div>
        <?= Html::input('number', "{$parameter->getInputName()}[to]", $parameter->getSelections()['to'] ?? $parameter->max, $options + ['id' => "{$widgetId}_{$inputId}-to", 'class' => "form-control {$inputId}-to"]) ?>
        <div class="input-group-addon hidden-sm"><?= $parameter->unit ?></div>
    </div>
    <div class="list-filter-form-range-unit input-group-addon visible-sm-* hidden-xs hidden-md hidden-lg"><?= $parameter->unit ?></div>
</div><!-- // range -->
<?php if ($linkLabels && $parameter->getValues()): ?>
    <?= Html::a('Все значения <i class="fa fa-angle-down"></i>', "#{$widgetId}_list-filter-form-all_{$inputId}", ['data-toggle' => 'collapse', 'class' => 'small list-filter-form-all-values-button']) ?>
    <div id="<?= $widgetId ?>_list-filter-form-all_<?= $inputId ?>" class="panel-collapse collapse list-filter-form-all-values">
        <?php foreach ($parameter->getValues(true) as $value): ?>
            <div>
                <?= Html::a("{$value->name} $parameter->unit", $value->url, ['title' => $value->title, 'class' => 'small']) ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
