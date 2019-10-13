<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;
use Illuminate\Http\Request;

class UserFilter extends ModelFilter
{
    use SortByHelper;

    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function setup()
    {
    }

    public function search($data)
    {
        $this->where(function ($q) use ($data) {
            $q->orwhere('email', 'LIKE', '%'.$data.'%')
            ->orWhere('mobile_no', 'LIKE', '%'.$data.'%')
            ->orWhere('name', 'LIKE', '%'.$data.'%');
        });
    }

    public function onlyDeletedAt()
    {
        $this->onlyTrashed();
    }

    public function status($val)
    {
        switch ($val) {
            case 'Active':
                $this->where('users.status', 'active');
                break;
            case 'In-active':
                $this->where('users.status', 'inactive');
                break;

            default:
                // code...
                break;
        }
    }

    public function myAppUser()
    {
        $client_id = app()->make(Request::class)->client()->id;
        $this->whereHas(
            'roles', function ($q) use ($client_id) {
                $q->whereJsonContains('client_ids', $client_id);
            }
        );
    }

    public function createdAtRange($dateRange)
    {
        if (isset($dateRange['start']) && $dateRange['start']) {
            $this->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') >= '{$dateRange['start']}'");
            $this->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') <= '{$dateRange['end']}'");
        }
    }

    public function deletedAtRange($dateRange)
    {
        if (isset($dateRange['start']) && $dateRange['start']) {
            $this->whereRaw("DATE_FORMAT(users.deleted_at, '%Y-%m-%d') >= '{$dateRange['start']}'");
            $this->whereRaw("DATE_FORMAT(users.deleted_at, '%Y-%m-%d') <= '{$dateRange['end']}'");
        }
    }
}
