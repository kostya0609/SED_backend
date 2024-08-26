<?php
namespace SED\Documents\Common\Services;

use Illuminate\Database\Eloquent\Model;

class FilterService
{
    public function filter(array $filters, $model)
    {
        foreach ($filters as $key => $value) {
            switch ($value['type']) {
                case 'number': {
                    if ($value['min'] && $value['max'] && $value['operation'] == '><') {
                        $model = $model->where([
                            [
                                $key,
                                '>=',
                                $value['min'],
                            ],
                            [
                                $key,
                                '<=',
                                $value['max'],
                            ],
                        ]);
                    } elseif ($value['min'] && $value['operation'] == '>') {
                        $model = $model->where($key, '>=', $value['min']);
                    } elseif ($value['min'] && $value['operation'] == '<') {
                        $model = $model->where($key, '<=', $value['min']);
                    } elseif ($value['min'] && $value['operation'] == '=') {
                        $model = $model->where($key, '=', $value['min']);
                    }
                    break;
                }
                case 'date': {
                    if ($value['min'] && $value['max'] && $value['operation'] == '><') {
                        $model = $model->where([
                            [$key, '>=', date('Y-m-d H:i:s', strtotime($value['min'] . ' ' . '00:00:00'))],
                            [$key, '<=', date('Y-m-d H:i:s', strtotime($value['max'] . ' ' . '00:00:00'))],
                        ]);
                    } elseif ($value['min'] && $value['operation'] == '>') {
                        $model = $model->where($key, '>=', date('Y-m-d H:i:s', strtotime($value['min'] . ' ' . '00:00:00')));
                    } elseif ($value['min'] && $value['operation'] == '<') {
                        $model = $model->where($key, '<=', date('Y-m-d H:i:s', strtotime($value['min'] . ' ' . '00:00:00')));
                    } elseif ($value['min'] && $value['operation'] == '=') {
                        $model = $model->where([
                            [$key, '>=', date('Y-m-d H:i:s', strtotime($value['min'] . ' ' . '00:00:00'))],
                            [$key, '<=', date('Y-m-d H:i:s', strtotime($value['min'] . ' ' . '23:59:59'))],
                        ]);
                    }
                    break;
                }
                case 'list' || 'searchList': {
                    $model = $model->whereIn($key, is_array($value['value']) ? $value['value'] : [$value['value']]);
                    break;
                }
            }
        }
        return $model;
    }
}