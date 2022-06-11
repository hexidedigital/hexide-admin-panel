<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use Illuminate\Translation\Translator;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

abstract class DefaultTable extends DataTableComponent
{
    public string $defaultSortColumn = 'id';
    public string $defaultSortDirection = 'desc';
    public ?int $searchFilterDebounce = 500;
    public $refresh = 20000; // every 20 seconds
    public array $perPageAccepted = [10, 25, 50, 100];

    public array $sortDirectionNames = [
        'id' => [
            'asc' => '1-9',
            'desc' => '9-1',
        ],
    ];

    abstract public function getModuleName(): string;

    public function setTableClass(): string
    {
        return 'table table-striped table-hover';
    }

    protected function getIdColumn(string $field = 'id', string $label = null): Column
    {
        return $this->makeColumn($field, $label)
            ->addAttributes(['style' => 'width: 50px;'])
            ->sortable()
            ->searchable();
    }

    protected function getSlugColumn(string $field = 'slug', string $label = null): Column
    {
        return $this->makeColumn($field, $label)
            ->sortable()
            ->searchable();
    }

    protected function getTitleColumn(string $field = 'title', string $label = null): Column
    {
        return $this->makeColumn($field, $label)
            ->sortable()
            ->searchable();
    }

    protected function getPositionColumn(string $field = 'position', string $label = null): Column
    {
        return $this->ajaxNumberColumn($field, $label);
    }

    protected function getPriorityColumn(string $field = 'priority', string $label = null): Column
    {
        return $this->ajaxNumberColumn($field, $label);
    }

    protected function getStatusColumn(string $field = 'status', string $label = null): Column
    {
        return $this->ajaxToggleColumn($field, $label);
    }

    protected function getActionsColumn(array $options = []): Column
    {
        return Column::make(__("hexide-admin::buttons.actions"))
            ->addAttributes(['style' => 'width: 95px'])
            ->format(fn($value, $column, $row) => view(
                'hexide-admin::partials.control_buttons',
                array_merge(\Arr::except($options, ['model', 'module']), [
                    'model' => $row,
                    'module' => $this->getModuleName(),
                ])))
            ->asHtml();
    }

    protected function getImageColumn(string $field = 'image', string $label = null): Column
    {
        return $this->makeColumn($field, $label)
            ->format(fn($value, $column, $row) => view('hexide-admin::partials.image', [
                'src' => $row->getAttribute($field),
                'classes' => 'img-thumbnail',
            ]))
            ->asHtml();
    }

    /**
     * @param string $field
     * @param string|null $label
     * @param \Closure|string $list
     * @return Column
     */
    protected function badgesColumn(string $field, string $label = null, $list = 'title'): Column
    {
        return $this->makeColumn($field, $label)
            ->format(function ($value, $column, $row) use ($list) {
                $res = [];

                if (is_callable($list)) {
                    $res = $list($value, $column, $row);
                } elseif (is_string($list)) {
                    $res = $value ? $value->pluck($list) : [];
                }

                return view('hexide-admin::partials.badges', [
                    'list' => $res,
                ]);
            });
    }

    protected function ajaxNumberColumn(string $field, string $label = null): Column
    {
        return $this->makeColumn($field, $label)
            ->sortable()
            ->format(fn($value, $column, $row) => view('hexide-admin::admin.partials.ajax.input', [
                'model' => $row,
                'module' => $this->getModuleName(),
                'field' => $field,
                'type' => 'number',
            ]))
            ->asHtml();
    }

    protected function ajaxToggleColumn(string $field, string $label = null): Column
    {
        return $this->makeColumn($field, $label)
            ->addAttributes(['style' => 'width: 75px'])
            ->sortable()
            ->format(fn($value, $column, $row) => view('hexide-admin::admin.partials.ajax.toggler', [
                'model' => $row,
                'module' => $this->getModuleName(),
                'field' => $field,
            ]))
            ->asHtml();
    }

    protected function booleanColumn(string $field, string $label = null, \Closure $closure = null): Column
    {
        return $this->makeColumn($field, $label)
            ->sortable()
            ->format(function ($value, $col, $row) use ($field, $closure) {
                $result = is_null($closure)
                    ? $value
                    : $closure($value, $col, $row);

                $icon = $result ? 'fas fa-check' : 'fas fa-times';
                $color = $result ? 'text-success' : 'text-danger';

                return <<<HTML
                        <div class="row"><span class="col-12 text-center $color"><i class="$icon"></i></span></div>
                    HTML;
            })
            ->asHtml();
    }

    protected function makeColumn(string $field, string $label = null): Column
    {
        $label = $this->translateLabel($field, $label);

        return Column::make($label, $field);
    }

    protected function translateLabel(string $field, string $label = null): string
    {
        /** @var Translator $translator */
        $translator = trans();
        $module = $this->getModuleName();

        if (!is_null($label)) {
            if ($translator->has($label)) {
                return $translator->get($label);
            }

            return $label;
        }

        $trans = $translator->get($key = "admin_labels.$module.attributes.$field");
        if ($trans !== $key) {
            return $trans;
        }

        $trans = $translator->get($key = "models.$module.attributes.$field");
        if ($trans !== $key) {
            return $trans;
        }

        $trans = $translator->get($key = "admin_labels.attributes.$field");
        if ($trans !== $key) {
            return $trans;
        }

        $trans = $translator->get($key = $field);
        if ($trans !== $key) {
            return $trans;
        }

        return \Str::ucfirst($field);
    }
}
