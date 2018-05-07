<?php
namespace kr0lik\listFilter;

use Yii;
use yii\base\{Widget, InvalidConfigException};
use yii\helpers\Url;

class FilterWidget extends Widget
{
    /**
     * Path to action script
     *
     * @var string
     */
    public $action;

    /**
     * Show labels of options as link
     *
     * @var bool
     */
    public $linkLabels = true;

    /**
     * Filter class
     *
     * @var Filter
     */
    public $filter;

    /**
     * Default number of items per page
     *
     * @var int
     */
    public $limit = 50;

    /**
     * Default sort
     *
     * @var string
     */
    public $sort;

    /**
     * Noindex tag on filter active
     *
     * @var bool
     */
    public $useNoindex = true;

    /**
     * Name of query parameter fro sort
     *
     * @var string
     */
    public $sortParameterName = 'sort';

    /**
     * Name of query parameter fro limit
     *
     * @var string
     */
    public $limitParameterName = 'per-page';

    /**
     * Parameters not shown, but mast be passed at query
     *
     * @var array [key => value]
     */
    public $hiddenParameters = [];

    /**
     * Options from filter form
     *
     * @var array
     */
    public $formOptions = [];

    /**
     * Path to view for output filter
     * Passed in view variables:
     *  - filter
     *  - limit
     *  - limitName
     *  - sort
     *  - sortName
     *  - linkLabels
     *  - formOptions
     *  - collapseMoreThen
     *  - hiddenParameters
     *
     * @var string
     */
    public $pathToViewFilter = 'filter/filter';

    /**
     * Collapse values if thew count more then inputed there
     *
     * @var int
     */
    public $collapseMoreThen = 5;

    public function init()
    {
        $this->checkConfiguration();

        parent::init();

        $this->prepare();
    }

    protected function checkConfiguration()
    {
        if (! $this->filter) {
            throw new InvalidConfigException('Filter is required');
        } elseif (! $this->filter instanceof Filter) {
            throw new InvalidConfigException('Filter must be instanceof class' . Filter::class);
        }

        if (! $this->sortParameterName) {
            throw new InvalidConfigException('SortParameterName must be specified');
        }
        if (! $this->limitParameterName) {
            throw new InvalidConfigException('limitParameterName must be specified');
        }
    }

    protected function prepare()
    {
        $noindex = false;

        if ($this->filter && $this->filter->isFiltered()) {
            $noindex = $this->useNoindex;
        }

        /*if ($param = Yii::$app->request->getQueryParam($this->viewParameterName)) {
            $noindex = $this->useNoindex;
            $this->view = $param ?: $this->view;
        }*/
        if ($param = Yii::$app->request->getQueryParam($this->limitParameterName)) {
            $noindex = $this->useNoindex;
            $this->limit = $param ?: $this->limit;
        }
        if ($param = Yii::$app->request->getQueryParam($this->sortParameterName)) {
            $noindex = $this->useNoindex;
            $this->sort = $param ?: $this->sort;
        }

        foreach ($this->hiddenParameters as $Key => $value) {
            if ($param = Yii::$app->request->getQueryParam($Key)) {
                $noindex = $this->useNoindex;
                $this->hiddenParameters[$Key] = $param ?: $value;
            }
        }

        if ($noindex) {
            $this->getView()->registerMetaTag([
                'name' => 'robots',
                'content' => 'noindex,nofollow'
            ], 'robots');
        }

        $this->id = "list-view-form-{$this->filter->id}-{$this->id}";
    }

    public function run()
    {
        if (! $this->filter->hasValues()) return;

        ListFilterAsset::register($this->getView());

        $this->formOptions['id'] = $this->id;
        $this->formOptions['action'] = $this->formOptions['action'] ?? Url::to();
        $this->formOptions['method'] = $this->formOptions['method'] ?? 'get';
        $this->formOptions['options']['class'] = "list-filter-form list-view-form-{$this->filter->id} no-js" . (isset($this->formOptions['options']['class']) ? " {$this->formOptions['options']['class']}" : '');
        $this->formOptions['options']['data-list-id'] = "list-view-data-{$this->filter->id}";
        $this->formOptions['enableClientValidation'] = $this->formOptions['enableClientValidation'] ?? false;
        $this->formOptions['enableAjaxValidation'] = $this->formOptions['enableAjaxValidation'] ?? false;
        $this->formOptions['enableClientScript'] = $this->formOptions['enableClientScript'] ?? false;

        return $this->render($this->pathToViewFilter, [
            'filter' => $this->filter,
            //'view' => $this->view,
            //'viewName' => $this->viewParameterName,
            'limit' => $this->limit,
            'limitName' => $this->limitParameterName,
            'sort' => $this->sort,
            'sortName' => $this->sortParameterName,
            'linkLabels' => $this->linkLabels,
            'formOptions' => $this->formOptions,
            'collapseMoreThen' => $this->collapseMoreThen,
            'hiddenParameters' => $this->hiddenParameters
        ]);
    }
}
