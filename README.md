# yii2-list-filter
ListView with filter for Yii2

Widget for list items using listView or [listViewMore](https://github.com/kr0lik/yii2-list-view-more) or somethimg else with Filter widget.

# Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist kr0lik/yii2-list-filter "dev-master"
```

or add

```
"kr0lik/yii2-list-filter": "dev-master"
```

to the require section of your `composer.json` file.

# Usage
Generate filter parameters and query. And then pass it to FilterDataProvider:
---

In Controller:
```php
<?php
use yii\web\Controller;
use kr0lik\listFilter\{Filter, FilterDataProvider};
use path\to\YourActiveRecord;

class YourController extends Controller
{
    public function actionIndex()
    {
        // Generate Query
        $query = YourActiveRecord::find();
    
        // Init Filter
        $filter = new Filter();

        // Add parameter with value and add select value if need select value in code
        $filter->add('parameter1', 'Parameter Name', 'queryAction', 'Parameter Unit')
            ->addValue('value1', 'Value Name', 'value/url/page', 'Value Url title')
            ->addSelect('value1');

        // Add parameter that has collections of parameters
        $filter->add('parameter2', 'Parameter Name', 'queryAction2')
            ->addCollection('group1', 'Parameter Group Name')
            ->addValue('value', 'Collection Value Name', 'value/url/page', 'Value Url title');

        // Add value to parameter later
        $filter->getParameter('parameter1')
            ->addValue('value2', 'Value Name');

        // Add value to collection later
        $filter->getParameter('parameter2')
            ->getCollection('group1')
            ->addValue('value2', 'Value Name')

        //Range parameter generate example.
        $filter->add('price', 'Price', 'byPrice', '$');
        $filter->getParameter('price')->min = $pricesExtremum['min_price'];
        $filter->getParameter('price')->max = $pricesExtremum['max_price'];
        $filter->getParameter('price')->step = 1;
        
        $dataProvider = new FilterDataProvider([
            'query' => $query,
            'filter' => $filter,
            ...
        ]);
        
        return $this->render('view', ['dataProvider' => $dataProvider]);
    }
}
```
All parameters ouput as checkboxes input.

All parameters with unit - converts to range input. If you not need auto convert all inputs to range - use:

```php
$filter->autoRangeType = false;
```

And in parameter: 

```php
$filter->getParameter('parameter')->autoRangeType = false;
$filter->getParameter('parameter')->type = kr0lik\listFilter\FilterParameter::TYPE_CHECKBOX;
```

Or in collection: 

```php
$filter->getParameter('parameter')->getCollection('group')->autoRangeType = false;
$filter->getParameter('parameter')->getCollection('group')->type = kr0lik\listFilter\FilterParameter::TYPE_CHECKBOX;
```

FilterDataProvider extends ActiveDataProvider.

Pass FilterDataProvider to ListView and generate html for filter:
---

In View:
```php
<?php
use yii\helpers\Html;
use yii\widgets\ListView;

$filter = $dataProvider->filter;
?>

<?= Html::beginForm() ?>
    <?php foreach ($filter->getParameters() as $parameter): ?>
        <?php if (! $parameter->hasValues()) continue; ?>
        <h2><?= $parameter->title ?></h2>

        <?php if ($parameter->isCollection()): ?>
            <?php foreach ($parameter->getCollections() as $parameter): ?>
                <?php if (! $parameter->hasValues()) continue; ?>
                <h4><?= $parameter->title ?></h4>

                <?php if ($parameter->isRange()) :?>
                    <?= Html::input('number', "{$parameter->getInputName()}[from]", $parameter->getSelections()['from'] ?? $parameter->min) ?>
                    -
                    <?= Html::input('number', "{$parameter->getInputName()}[to]", $parameter->getSelections()['to'] ?? $parameter->max) ?>
                <?php else: ?>
                    <?php foreach($parameter->getValues(true) as $value): ?>
                        <?= Html::checkbox("{$parameter->getInputName()}[]", $parameter->isValueSelected($value->key), ['id' => "{$parameter->getId()}-{$value->key}"]) ?>
                        <?= Html::label($value->name, "{$parameter->getId()}-{$value->key}") ?>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php else: ?>
            <?php if (! $parameter->hasValues()) continue; ?>

            <?php if ($parameter->isRange()) :?>
                <?= Html::input('number', "{$parameter->getInputName()}[from]", $parameter->getSelections()['from'] ?? $parameter->min) ?>
                -
                <?= Html::input('number', "{$parameter->getInputName()}[to]", $parameter->getSelections()['to'] ?? $parameter->max) ?>
            <?php else: ?>
                <?php foreach($parameter->getValues(true) as $value): ?>
                    <?= Html::checkbox("{$parameter->getInputName()}[]", $parameter->isValueSelected($value->key), ['id' => "{$parameter->getId()}-{$value->key}"]) ?>
                    <?= Html::label($value->name, "{$parameter->getId()}-{$value->key}") ?>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php endif; ?>
    <?php endforeach; ?>
<?= Html::endForm() ?>

<?= ListView::widget(['dataProvider' => $dataProvider]) ?>
```
