<?php

namespace HexideDigital\HexideAdmin\Http\Traits;

use Event;
use Response;

/**
 * url for controller: (post) controller_name/ajax_field/{id}
 *
 * Class AjaxFieldsChangerTrait
 * @package HexideDigital\HexideAdmin\Http\Traits
 */
trait AjaxFieldsChangerTrait
{
    /**
     * change field = $field of record with id = $id
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxFieldChange($id): \Illuminate\Http\JsonResponse
    {
        $class_name = $this->get_model_by_controller(__CLASS__);
        $class = '\App\Models\\'.$class_name;
        $model = new $class();

        $model = $model::find($id);

        if ($model) {
            $field = request('field', null);
            $value = request('value', null);

            if (!empty($field)) {
                $model->{$field} = $value;

                if ($model->save()) {
                    $event = '\App\Events\Backend\\'.$class_name.'Edit';
                    if (class_exists($event)) {
                        Event::fire(new $event($model));
                    }

                    return Response::json(
                        [
                            "error"   => 0,
                            'message' => __('hexide_admin::messages.success.action'),
                        ],
                    );
                }
            }
        }

        return Response::json(
            [
                "error"   => 1,
                'message' => __('hexide_admin::messages.error.action'),
            ],
            422
        );
    }

    private function get_model_by_controller($class)
    {
        $class = explode('\\', str_replace('Controller', '', $class));

        return array_pop($class);
    }
}
