<?php

namespace App\Models;

use App\Lib\BulkDataQuery;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class ApplicationMenuItem extends Model
{
    use Filterable, HasJsonRelationships, BulkDataQuery;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
    'name',
    'title',
    'menu_order',
    'show_in_menu',
    'description',
    'application_menu_id',
    'permission_ids',
    'parent_id'
   ];
     /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
   protected $casts = [
    'name' => 'string',
    'title' => 'string',
    'menu_order' => 'int',
    'show_in_menu' => 'string',
    'description' => 'string',
    'application_menu_id' => 'int',
    'permission_ids' => 'json',
    'parent_id' => 'int'
   ];

   public function permissions()
   {
        return $this->belongsToJson(Permission::class, 'permission_ids', 'id');
   }
   public function parent()
   {
        return $this->belongsTo(ApplicationMenuItem::class, 'parent_id', 'id');
   }
   public function applicationMenu()
   {
        return $this->belongsTo(ApplicationMenu::class, 'application_menu_id', 'id');
   }
}
