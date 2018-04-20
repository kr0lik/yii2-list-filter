<?php
use yii\helpers\{Html, Url};
?>

<div class="list-filter-toolbar">
    <div class="list-filter-toolbar-main">
        <?= Html::label(Yii::t('yii', 'Showing <b>{begin, number}-{end, number}</b> of <b>{totalCount, number}</b> {totalCount, plural, one{item} other{items}}.')) ?>
    </div>
    <?php if ($availableLimits): ?>
        <div class="list-filter-toolbar-limit pull-right">
            <?= Html::label('Показывать по') ?>
            &nbsp;
            <?php foreach ($availableLimits as $val): ?>
                <?php if ($val == $limit): ?>
                    <?= Html::tag('strong', $val) ?>
                    &nbsp;
                <?php else: ?>
                    <?= Html::a($val, Url::current([$limitParameterName => $val, 'page' => 1]), ['data-name' => $limitParameterName, 'data-value' => $val, 'rel' => 'nofollow']) ?>
                    &nbsp;
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($availableSorts): ?>
        <div class="list-filter-toolbar-sort pull-right">
            <?= Html::label('Сортировать по', null, ['class' => 'hidden-xs']) ?>
            <?php foreach ($availableSorts as $key => $attributes): ?>
                <?php
                $title = 'Сделать по возратсанию';
                $label = $attributes['label'];
                $icon = '';
                $newVal = $key;
                $isDescSort = false;
                ?>
                <?php if (trim($sort, '-') == $key): ?>
                    <?php
                    if (substr($sort, 0, 1) == '-') {
                        $isDescSort = true;
                    }

                    if ($isDescSort) {
                        $title = 'Сделать по возратсанию';
                        $newVal = $key;
                    } else {
                        $title = 'Сделать по убыванию';
                        $newVal = "-$key";
                    }
                    ?>
                <?php endif; ?>
                <?= Html::a("$label", Url::current([$sortParameterName => $newVal]), ['class' => 'product-list-sort', 'title' => $title, 'data-name' => $sortParameterName, 'data-value' => $newVal, 'rel' => 'nofollow']) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>