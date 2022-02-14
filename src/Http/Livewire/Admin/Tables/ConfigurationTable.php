<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use HexideDigital\HexideAdmin\Models\AdminConfiguration;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ConfigurationTable extends DefaultTable
{
    public function columns(): array
    {
        return [
            $this->getIdColumn(),

            Column::make(__('admin_labels.attributes.name'), 'name')
                ->sortable()
                ->searchable(),

            Column::make(__('admin_labels.attributes.type'), 'type')
                ->sortable()
                ->format(fn($value, $col, $row) => __('models.admin_configurations.type.' . $row->type ?? ''))
            ,
            Column::make(__('admin_labels.attributes.key'), 'key')
                ->sortable()
                ->searchable(),

            Column::make(__('admin_labels.attributes.translatable'), 'translatable')
                ->sortable()
                ->format(function ($value, $col, $row) {
                    /** @var AdminConfiguration $row */
                    $icon = $row->translatable ? 'fas fa-check' : 'fas fa-times';
                    $color = $row->translatable ? 'text-success' : 'text-danger';

                    return <<<HTML
                        <div class="row"><span class="col-12 text-center $color"><i class="$icon"></i></span></div>
                    HTML;
                })
                ->asHtml(),

            Column::make(__('admin_labels.attributes.group'), 'group')
                ->sortable()
                ->searchable(),

            Column::make(__('admin_labels.attributes.in_group_position'), 'in_group_position')
                ->sortable()
                ->format(fn($value, $column, $row) => view('hexide-admin::admin.partials.ajax.input', [
                    'model' => $row, 'module' => $this->getModuleName(),
                    'field' => 'in_group_position', 'type' => 'number',
                ]))
                ->asHtml(),

            $this->getActionsColumn(),
        ];
    }

    public function getModuleName(): string
    {
        return module_name_from_model(new AdminConfiguration);
    }

    public function query(): Builder
    {
        return AdminConfiguration::joinTranslations()
            ->with('translations')
            ->select([
                'admin_configurations.*',
                'admin_configuration_translations.text as text',
            ]);
    }
}
