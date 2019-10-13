<?php

namespace App\Http\Controllers\Api;

use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\ModelFilters\NotificationTemplateFilter;

/**
 * To User Regiatration.
 *
 * @category Auth
 *
 * @author  Hitesh Kumar <live2hitesh@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT
 *
 * @see https://github.com/hitesh399
 */
class NotificationTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request [All request data]
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $template = NotificationTemplate::filter(
            $request->all(),
            NotificationTemplateFilter::class
        )->lqPaginate();

        return $this->setData($template)
            ->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request [All request data]
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validation($request);

        $template = NotificationTemplate::create($request->all());

        return $this->setData($template)
            ->setMessage('Notification template has been created.')
            ->response(200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id [Table primary key]
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $template = NotificationTemplate::findOrFail($id);

        return $this->setData(['template' => $template])->response(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request [All request data]
     * @param int                      $id      [Table primary key]
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validation($request, $id);

        $template = NotificationTemplate::findOrFail($id);
        $template->update(
            $request->all()
        );

        \Cache::forget('notification_template.'.$template->name);

        return $this->setData($template)
            ->setMessage('Notification template has been updated.')
            ->response(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id [To Delete email template]
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        NotificationTemplate::where('id', $id)->delete();

        return $this->setMessage(
            'Notification Template deleted successfully.'
        )->response(200);
    }

    /**
     * Get Email Body.
     *
     * @return \Illuminate\Http\Response
     */
    public function emailBody()
    {
        $config = config::whereIn('name', ['EMAIL_BODY'])->get()->keyBy('name');

        return $this->setData($config)->response(200);
    }

    /**
     * Validate  the given request.
     *
     * @param \Illuminate\Http\Request $request [all Request data]
     * @param int                      $id      [Table primary key]
     *
     * @return void|
     */
    protected function validation(Request $request, $id = null)
    {
        $request->validate(
            [
                'name' => ['required', 'unique:notification_templates,name,'.$id],
                'subject' => ['required'],
                'body' => ['required_if:type,sms,email,push'],
                'type' => ['required', Rule::in('sms', 'email', 'database', 'push')],
                'options.variables' => ['required', 'array'],
            ]
        );
    }
}
