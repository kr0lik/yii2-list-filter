<?php
namespace kr0lik\listFilter;

use Yii;
use yii\db\ActiveQuery;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\widgets\ListView;

class ListWidget extends FilterWidget
{
    /**
     * ActiveQuery. If null - models and total must be specified
     *
     * @var ActiveQuery | null
     */
    public $query;

    /**
     * Array of models to output. If no query specified.
     *
     * @var array | null
     */
    public $models;

    /**
     * Total items for ActiveDataProvider.
     *
     * @var int | null
     */
    public $total;

    /**
     * Sort attributes for ActiveDataProvider
     *
     * @var array
     */
    public $sortAttributes = [];

    /**
     * Default sort for ActiveDataProvider
     *
     * Example: ['id' => SORT_ASC]
     *
     * @var array | null
     */
    public $sortDefault;

    /**
     * Available per page item list limits
     *
     * Example: [20, 40, 60]
     *
     * @var array
     */
    public $availablePerPageLimits = [];

    /**
     * Class of listView widget
     * Required if you use default view for output list
     *
     * @var string | null
     */
    public $listViewWidget = ListView::class;

    /**
     * Options for listView widget
     *
     * @var array
     */
    public $listViewOptions = [];

    /**
     * Path to view for output list
     * Passed variables:
     *  - listViewWidget
     *  - listViewOptions
     *
     * @var string
     */
    public $listView;

    /**
     * Path to toolbar view
     * Passed variables:
     *   - formId
     *   - dataProvider
     *   - limit
     *   - availableLimits
     *   - limitParameterName
     *   - sort
     *   - availableSorts
     *   - sortParameterName
     *
     * If false - toolbar not shown
     *
     * @var string | false
     */
    public $toolbarView = 'list/_toolbar';

    protected function checkConfiguration()
    {
        parent::checkConfiguration();

        if ($this->availablePerPageLimits && ! in_array($this->limit, $this->availablePerPageLimits)) {
            throw new InvalidConfigException('Limit must be ' . join(' or ', $this->availablePerPageLimits));
        }

        if (! $this->query && $this->models === null) {
            throw new InvalidConfigException('Query or models must be specified');
        } else {
            if ($this->models) {
                if (! is_array($this->models)) {
                    throw new InvalidConfigException('Models must be array of models');
                }
            }
            if ($this->query && ! $this->query instanceof ActiveQuery) {
                throw new InvalidConfigException('Query must instanceof ActiveQuery');
            }
        }

        if (! $this->listView && ! $this->listViewWidget) {
            throw new InvalidConfigException('ListViewWidget OR listView must be specified');
        }
    }

    protected function prepare()
    {
        parent::prepare();

        if ($this->query) {
            if ($this->filter) {
                foreach ($this->filter->getParameters() as $parameter) {
                    if ($select = $parameter->getSelections()) {
                        $this->query->{$parameter->scope}($select);
                    }
                }
            }

            if (! $this->total) {
                $this->total = (clone $this->query)->count();
            }
        } else {
            if (! $this->total) {
                $this->total = count($this->models);
            }
        }

        if (! $this->sort && $this->sortDefault) {
            $key = key($this->sortDefault);
            $this->sort = ($this->sortDefault[$key] == SORT_DESC ? '-' : '') . $key;
        }

        $this->id = "list-view-data-{$this->filter->id}";
    }

    public function run()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->query ? $this->query : null,
            'pagination' => [
                'defaultPageSize' => $this->limit,
                'pageSizeLimit' => [0, $this->limit],
                'totalCount' => $this->total,
                'forcePageParam' => false,
                'validatePage' => false
            ],
            'sort' => [
                'defaultOrder' => $this->sortDefault,
                'attributes' => $this->sortAttributes
            ],
            'models' => $this->models,
            'totalCount' => $this->total,
        ]);


        if (($page = Yii::$app->request->get('page')) && ($page * $this->limit) - $this->limit > $dataProvider->totalCount) {
            //$this->getView()->registerMetaTag([
            //    'name' => 'robots',
            //    'content' => 'noindex,nofollow'
            //], 'robots');

            throw new NotFoundHttpException();
        }

        $formId = "list-view-form-{$this->filter->id}";

        $this->listViewOptions['id'] = $this->id;
        $this->listViewOptions['dataProvider'] = $dataProvider;
        $this->listViewOptions['options']['data-filter-id'] = $formId;
        if (! isset($this->listViewOptions['summary'])) {
            $this->listViewOptions['summary'] = $this->render($this->toolbarView, [
                'dataProvider' => $dataProvider,
                'limit' => $this->limit,
                'availableLimits' => $this->availablePerPageLimits,
                'limitParameterName' => $this->limitParameterName,
                'sort' => $this->sort,
                'availableSorts' => $this->sortAttributes,
                'sortParameterName' => $this->sortParameterName,
                'formId' => $formId
            ]);
        }

        if ($this->listView) {
            return $this->render($this->listView, [
                'listViewWidget' => $this->listViewWidget,
                'listViewOptions' => $this->listViewOptions
            ]);
        } else {
            echo $this->listViewWidget::widget($this->listViewOptions);
        }
    }
}
