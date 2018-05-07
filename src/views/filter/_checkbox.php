<?php
use yii\helpers\Html;

$countValues = count($parameter->getValues());
$hide = false;
?>

<?php foreach ($parameter->getValues(true) as $i => $value): ?>
    <?php if ($i > $maxShown-1 && $countValues > $maxShown + 1 && ! $parameter->isSelected()) $hide = true; ?>
    <?php if ($i == $maxShown && $countValues > $maxShown + 1): ?>
        <div id="<?= $widgetId ?>_list-filter-form-more_<?= $parameter->getId() ?>" class="panel-collapse collapse<?= !$hide ? ' in' : '' ?> list-filter-form-more">
    <?php endif; ?>
    <div class="checkbox">
        <?php $label = $linkLabels && $value->url ? Html::a($value->name, $value->url, ['title' => $value->title]) : $value->name; ?>
        <?= Html::label(Html::checkbox("{$parameter->getInputName()}[]", (in_array($value->key, $parameter->getSelections())), ['id' => "{$widgetId}_{$parameter->getId()}" . "-{$value->key}", 'value' => $value->key, 'class' => "checkbox {$parameter->getId()}-{$value->key}"]) . $label, "{$widgetId}_{$parameter->getId()}-{$value->key}") ?>
    </div>
<?php endforeach; ?>
<?php if ($i == $countValues-1 && $countValues > $maxShown + 1): ?>
    </div>
    <?= Html::button('<i class="fa fa-angle-double-' . ($hide ? 'down' : 'up') . '"></i>', ['data-toggle' => 'collapse', 'data-target'  => "#{$widgetId}_list-filter-form-more_{$parameter->getId()}", 'class' => 'btn btn-default btn-xs btn-block list-filter-form-more-button']) ?>
<?php endif; ?>
