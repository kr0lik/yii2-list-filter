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
First generate filter parameters and query:
---

In Controller:
```php
<?php
use yii\web\Controller;
use kr0lik\listFilter\Filter;
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
            ->addCollection('parameter2_1', 'Parameter Group Name')
            ->addValue('value', 'Collection Value Name', 'value/url/page', 'Value Url title');

        // Add value to parameter later
        $filter->getParameter('parameter1')
            ->addValue('value2', 'Value Name');

        // Add value to collection later
        $filter->getParameter('parameter2')
            ->getCollection('parameter2_1')
            ->addValue('value2', 'Value Name')

        //Price parameter generate example
        $filter->add('price', 'Price', 'byPrice', '$');
        $filter->getParameter('price')->min = $pricesExtremum['min_price'];
        $filter->getParameter('price')->max = $pricesExtremum['max_price'];
        $filter->getParameter('price')->step = 1;
    }
}
```

Add filter and query to ListWidget:
---

In View:
```php
<?php
use kr0lik\listFilter\ListWidget;
?>

<?= ListWidget::widget(['query' => $query, 'filter' => $filter]); ?>
```

Add filter to FilterWidget:
---

In View:
```php
<?php
use kr0lik\listFilter\FilterWidget;
?>

<?= FilterWidget::widget(['filter' => $filter]); ?>
```
