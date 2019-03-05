<?php
namespace kr0lik\listFilter;

use Yii;

class FilterParameterBase implements FilterParameterInterface
{
    protected $name = '';
    protected $title = '';

    protected $scope;

    protected $values = [];
    protected $selections = [];

    protected $prepared = false;

    protected $type = Filter::PARAMETER_DEFAULT;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setTitle(string $title): FilterParameterInterface
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setScope($scope): FilterParameterInterface
    {
        $this->scope = $scope;

        return $this;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function addValue($key, $name, string $url = null, string $title = null): FilterParameterInterface
    {
        $inputName = trim($this->getInputName(), '[]');
        
        $object = new \stdClass();
        $object->id = "{$inputName}-{$key}";
        $object->key = $key;
        $object->name = $name;
        $object->url = $url;
        $object->title = $title;

        $this->values[] = $object;

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function hasValues(): bool
    {
        return (bool) $this->getValues();
    }

    public function addSelect($select): FilterParameterInterface
    {
        if (is_array($select)) {
            $this->selections = array_merge($this->selections, $select);
        } else {
            $this->selections[] = $select;
        }

        $this->selections = array_unique($this->selections);

        return $this;
    }

    public function getSelections(): array
    {
        return $this->selections;
    }

    public function hasSelections(): bool
    {
        return (bool) $this->getSelections();
    }

    public function isSelected($key): bool
    {
        return in_array($key, $this->getSelections());
    }

    public function getSelectedValues(): array
    {
        $values = [];

        if ($this->hasSelections()) {
            foreach ($this->getValues() as $value) {
                if (in_array($value->key, $this->getSelections())) {
                    $values[] = $value;
                }
            }
        }

        return $values;
    }

    public function getInputName(): string
    {
        return "{$this->name}[]";
    }

    public function prepare(): void
    {
        if (! $this->prepared) {
            $select = Yii::$app->request->getQueryParam($this->name);

            if ($select != null) {
                $this->addSelect($select);
            }

            $this->prepared = true;
        }
    }
}
