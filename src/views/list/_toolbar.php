<?php
use yii\helpers\{Html, Url};

$numberOfItems = '<span itemprop="numberOfItems">{totalCount}</span>';
$schemaOrder = '<link itemprop="itemListOrder" href="https://schema.org/ItemListUnordered" />';
?>

<div class="product-list-toolbar">
    <div class="col-xs-12">
        <div class="pull-left">
            <?= Html::label("Показано {begin} - {end} из $numberOfItems") ?>
        </div>
        <?php if ($availableLimits): ?>
            <div class="pull-right hidden-xs">
                <?= Html::label('Показывать по') ?>
                <?php foreach ($availableLimits as $val): ?>
                    <?php if ($dataProvider->pagination->limit == $val): ?>
                        <?= Html::tag('strong', $val) ?>
                    <?php else: ?>
                        <?= Html::a($val, Url::current([$limitParameterName => $val, 'page' => 1]), ['data-name' => $limitParameterName, 'data-value' => $val, 'rel' => 'nofollow']) ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="clearfix"></div>
        <hr class="no-margin">
    </div>
    <div class="col-xs-12">
        <?php if ($availableViews): ?>
            <div class="pull-left hidden-xs">
                <?= Html::label('Вид') ?>
                <?php foreach ($availableViews as $key => $data): ?>
                    <?php if ($view == $key): ?>
                        &nbsp;&nbsp;<?= Html::tag('strong', $data['label'] ?? '', $data['options'] ?? []) ?>
                    <?php else: ?>
                        <?php
                        $url = $data['url'] ?? Url::current([$viewParameterName => $key]);
                        $options = $data['options'] ?? [];
                        $options['data-name'] = $viewParameterName;
                        $options['data-value'] = $key;
                        ?>
                        &nbsp;&nbsp;<?= Html::a($data['label'] ?? '', $url, $options) ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <!--div class="pull-left hidden visible-xs">
            <?= Html::a('<i class="fa fa-sliders fa-lg"></i>', '#product-list-filter-container', ['id' => 'product-list-filter-goto', 'rel' => 'nofollow']) ?>
        </div-->
        <div class="pull-right">
            <?= Html::label('Сортировать по', null, ['class' => 'hidden-xs']) ?>
            <?php foreach ($dataProvider->sort->attributes as $key => $attributes): ?>
                <?php
                    $title = 'Сделать по возратсанию';
                    $icon = '';
                    $newVal = $key;
                    $hasDescSort = false;
                ?>
                <?php if ($key == key($dataProvider->sort->attributeOrders)): ?>
                    <?php
                        if ($dataProvider->sort->attributeOrders[$key] == SORT_DESC) {
                            $hasDescSort = true;
                            $schemaOrder = '<link itemprop="itemListOrder" href="https://schema.org/ItemListOrderDescending" />';
                        } elseif ($dataProvider->sort->attributeOrders[$key] == SORT_ASC && ! $hasDescSort) {
                            $schemaOrder = '<link itemprop="itemListOrder" href="https://schema.org/ItemListOrderAscending" />';
                        }

                        if ($dataProvider->sort->attributeOrders[$key] == SORT_DESC) {
                            $title = 'Сделать по возратсанию';
                            $icon = '<i class="fa fa-arrow-down"></i>';
                            $newVal = $key;
                        } else {
                            $title = 'Сделать по убыванию';
                            $icon = '<i class="fa fa-arrow-up"></i>';
                            $newVal = "-$key";
                        }
                    ?>
                <?php endif; ?>
                <?= $dataProvider->sort->link($key, ['label' => $attributes['label'] . $icon, 'class' => 'product-list-sort', 'title' => $title, 'data-name' => $dataProvider->sort->sortParam, 'data-value' => $newVal, 'rel' => 'nofollow']) ?>
            <?php endforeach; ?>
            <?= $schemaOrder ?>
        </div>
    </div>
</div>