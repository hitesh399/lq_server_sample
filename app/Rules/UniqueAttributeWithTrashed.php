<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
//use Illuminate\Validation\Rules\Unique;
use App\Models\User;

class UniqueAttributeWithTrashed implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $ignoreId = null;
    private $data = null;
    private $attribute = null;
    private $table = null;

    public function __construct($ignore_id = null, $table = null)
    {
        //
        $this->ignoreId = $ignore_id;

        $this->table = $table;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        $db = $this->table::where($attribute, $value)->withTrashed();

        if ($this->ignoreId) {
            $db->where('id', '<>', $this->ignoreId);
        }

        $this->data = $db->first();
        return !$this->data;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.unique');
    }
}
