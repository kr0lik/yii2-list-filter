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

# Description

Extension will install [kr0lik/yii2-jui-touch-punch-asset](https://github.com/kr0lik/yii2-jui-touch-punch-asset). It used for range parameters in filter.

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

        //Range parameter generate example.
        $filter->add('price', 'Price', 'byPrice', '$');
        $filter->getParameter('price')->min = $pricesExtremum['min_price'];
        $filter->getParameter('price')->max = $pricesExtremum['max_price'];
        $filter->getParameter('price')->step = 1;
    }
}
```

All parameters with unit specified - converts to range parameters.If you not nedd auto convert this parameters to range - use:

```php
$filter->autoRangeType = false;
```

And in parameter: 

```php
$filter->->getParameter('parameter')->autoRangeType = false;
$filter->->getParameter('parameter')->type = kr0lik\listFilter\FilterParameter::TYPE_CHECKBOX;
```


ListWidget:
---

In View:
```php
<?php
use kr0lik\listFilter\ListWidget;
?>

<?= ListWidget::widget(['query' => $query, 'filter' => $filter]); ?>
```

Available options:
- filter - Filter.
- query - ActiveQuery. If null - models and total must be specified.
- models - Array of models to output. If no query specified.
- total - Total items for ActiveDataProvider.
- limit:50 - Default number of items per page.
- sortAttributes - Sort attributes for ActiveDataProvider.
- sortDefault - Default sort for ActiveDataProvider. Example: ['id' => SORT_ASC]
- sortParameterName - Name of query parameter fro sort.
- limitParameterName - Name of query parameter fro limit.
- availablePerPageLimits - Available per page item list limits. Example: [20, 40, 60].
- listViewWidget - Class of listView widget.
- listViewOptions - Options for listView widget.
- listView - Path to view for output list. Passed in view variables: $listViewWidget, $listViewOptions.
- toolbarView - Path to toolbar view. If false - toolbar not shown. Passed in view variables: $formId, $dataProvider, $limit, $availableLimits, $limitParameterName, $sort, $availableSorts, $sortParameterName.

FilterWidget:
---

In View:
```php
<?php
use kr0lik\listFilter\FilterWidget;
?>

<?= FilterWidget::widget(['filter' => $filter]); ?>
```

Available options:
- filter - Filter.
- action - Path to action script.
- linkLabels:true - Show labels of options as link.
- limit:50 - Default number of items per page.
- sort - Default sort.
- useNoindex:true - Noindex tag on filter active.
- sortParameterName - Name of query parameter fro sort.
- limitParameterName - Name of query parameter fro limit.
- hiddenParameters - Parameters not shown, but mast be passed at query: [key => value].
- formOptions - Options from filter form.
- pathToViewFilter - Path to view for output filter. Passed in view variables: $filter, $limit, $limitName, $sort, $sortName, $linkLabels, $formOptions, $collapseMoreThen, $hiddenParameters.
- collapseMoreThen:5 - Collapse values if thew count more then inputed there.
