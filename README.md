# yii2-list-filter
Simple filter for Yii2 wicth appply filter values to ActivQuery

# Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist kr0lik/yii2-list-filter "*"
```

or add

```
"kr0lik/yii2-list-filter": "*"
```

to the require section of your `composer.json` file.

# Usage

Create filter in controller:

```php
<?php
use yii\web\Controller;
use kr0lik\listFilter\Filter;
use app\models\YourModel;

class YourController extends Controller
{
    public function actionIndex()
    {
	$query = YourModel::find();
		
	$filter = new Filter();
		
	$filter->add('default')->setTitle('Parameter Default')->setScope('byParameter');
        $filter->add('collection', Filter::PARAMETER_COLLECTION)->setScope('byCollectionParameter');
        $filter->add('price', Filter::PARAMETER_RANGE)->setTitle('Range Parameter')->setScope('byPrice')->setUnit('$');
        $filter->add('hasPrice', Filter::PARAMETER_BOOLEAN)->setScope(function($query, $select) {
            return $select ? $query->hasPrice() : $query;
        })->addValue(true, 'Checked by default')->addSelect(true);
		
	$filter->getParameter('default')->addValue('id', 'checkbox', 'Url', 'title');
		
	$filter->getParameter('price')
	    ->setMinKey(0)
            ->setMaxKey(100)
	    ->setStep(1);
		
	$filter->getParameter('collection')
            ->add('collection1')
            ->setTitle('Collection 1')
	    ->addValue(true, 'Checked by default')->addSelect(true);
			
	$filter->getParameter('collection')
            ->add('collection2', Filter::PARAMETER_RANGE)
            ->setTitle('Collection 2');	
				
		
	$filter->getParameter('collection')->getParameter('collection2')
	    ->setMinKey(0)
            ->setMaxKey(12.5)
	    ->setStep(0.5);
		
	$dataProvider = new ActiveDataProvider([
            'query' => $query->byFilter($filter),
	    ...
	]);
		
	return $this->render('index', ['filter' => $filter, 'dataProvider' => $dataProvider]);
    }
}
```

Add FilterQueryTrait in query class of your model:

```php
<?php
use yii\db\ActiveQuery;
use kr0lik\listFilter\FilterQieryTrait;

class YourModelQuery extends ActiveQuery
{
    use FilterQieryTrait;
}
```

Create filter in view:

```php
<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use kr0lik\listFilter\Filter;
?>

<?php foreach ($filter->getSelectedValues() as $value): ?>
	<?= $value->value ?><br />
<?php endforeach; ?>

<?= Html::beginForm() ?>
    <?php foreach ($filter->getParameters() as $parameter): ?>
        <?php if (! $parameter->hasValues()) continue; ?>
        <h2><?= $parameter->getTitle() ?></h2>

        <?php if ($parameter->getType() == Filter::PARAMETER_COLLECTION): ?>
            <?php foreach ($parameter->getParameters() as $parameter): ?>
                <?php if (! $parameter->hasValues()) continue; ?>
                <h4><?= $parameter->getTitle() ?></h4>

                <?php if ($parameter->getType() == Filter::PARAMETER_RANGE) :?>
			<?= Html::input('number', $parameter->getInputNameFrom(), $parameter->getValueFrom()) ?>
			-
			<?= Html::input('number', $parameter->getInputNameTo(), $parameter->getValueTo()) ?>
                <?php else: ?>
			<?php if ($parameter->getType() == Filter::PARAMETER_BOOLEAN) :?>
				<?= Html::hiddenInput($parameter->getInputName(), false); ?>
			<?php endif; ?>
				
			<?php foreach($parameter->getValues() as $value): ?>
				<?= Html::checkbox($parameter->getInputName(), $value->isSelected(), ['id' => $value->getId(), 'value' => $value->getKey()]) ?>
				<?= Html::label($value->getValue(), $value->getId()) ?>
			<?php endforeach; ?>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php else: ?>
            <?php if (! $parameter->hasValues()) continue; ?>

            <?php if ($parameter->getType() == Filter::PARAMETER_RANGE) :?>
			<?= Html::input('number', $parameter->getInputNameFrom(), $parameter->getValueFrom()) ?>
			-
			<?= Html::input('number', $parameter->getInputNameTo(), $parameter->getValueTo()) ?>
            <?php else: ?>
			<?php if ($parameter->getType() == Filter::PARAMETER_BOOLEAN) :?>
				<?= Html::hiddenInput($parameter->getInputName(), false); ?>
			<?php endif; ?>
				
			<?php foreach($parameter->getValues() as $value): ?>
				<?= Html::checkbox($parameter->getInputName(), $value->isSelected(), ['id' => $value->getId(), 'value' => $value->getKey()]) ?>
                <?= Html::label($value->getValue(), $value->getId()) ?>
			<?php endforeach; ?>
            <?php endif; ?>

        <?php endif; ?>
    <?php endforeach; ?>
    
    <?= Html::submitButton('Submit') ?>
<?= Html::endForm() ?>

<?= ListView::widget(['dataProvider' => $dataProvider]) ?>
```
