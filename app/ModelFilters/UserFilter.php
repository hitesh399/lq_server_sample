<?php
namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

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

    function setup() {

    }

    public function service($service)
    {
        $this->whereHas('services', function($query) use($service){
            $query->where('services.id','=',$service);
        });
    }

    public function search($data)
    {
        $this->where(function ($q) use($data) {
            $q->orwhere('email','LIKE', "%".$data."%")
            ->orWhere('contact_number', 'LIKE', "%".$data."%")
            ->orWhere('name', 'LIKE', "%".$data."%");
        });
    }

    public function onlyDeletedAt() {
        $this->onlyTrashed();
    }

    public function onlyAvailable($available) {
        $date = date('Y-m-d');
        $after_one_month_date = date('Y-m-d', strtotime($date. ' + 30 days'));
        // dd($date, $after_one_month_date);
        $this->join('user_availablities','user_availablities.user_id','=','users.id')
            ->whereRaw("(
                (user_availablities.slot_date >= '{$date}' AND user_availablities.slot_date <= '{$after_one_month_date}' )
            )");
    }

    public function checkCharge($service_id) {
        $this->join('service_users','service_users.user_id','=','users.id')
            ->where('service_users.service_id', $service_id)
            ->whereRaw("(
                (service_users.base_charge <> 'null' AND
                service_users.per_minute_rate <> 'null' )
            )");
    }
    public function status ($val)
    {
        switch ($val) {
            case 'Active':
                $this->where('users.status', 'active');
                break;
            case 'In-active':
                $this->where('users.status', 'inactive');
                break;

            default:
                # code...
                break;
        }
    }
    public function createdAtRange ($dateRange)
    {
        if (isset($dateRange['start']) && $dateRange['start']) {
            $this->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') >= '{$dateRange['start']}'");
            $this->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') <= '{$dateRange['end']}'");
        }
    }
    public function deletedAtRange ($dateRange)
    {
        if (isset($dateRange['start']) && $dateRange['start']) {
            $this->whereRaw("DATE_FORMAT(users.deleted_at, '%Y-%m-%d') >= '{$dateRange['start']}'");
            $this->whereRaw("DATE_FORMAT(users.deleted_at, '%Y-%m-%d') <= '{$dateRange['end']}'");
        }
    }
}
