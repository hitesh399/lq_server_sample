<?php

namespace App\Http\Controllers\Api;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Lib\DatabaseNotificationCompiler;

class MyNotificationController extends Controller
{
    /**
     * To get List of notification of login User.
     *
     * @param Class $request Illuminate\Http\Request
     *
     * @return Json Illuminate\Http\Response
     */
    public function index() {
        $notifications = Auth::User()->notifications()
            ->lqPaginate();
        $notifications['data']->map(function ($notification) {
            $template = new DatabaseNotificationCompiler(
                $notification->type,
                $notification->data
            );
            $notification->title = $template->get();
            return $notification;
        });
        $notifications['unread_total'] = Auth::User()->unreadNotifications()->count();
        return $this->setData($notifications)->response();
    }

    /**
     * To Read notification of login User.
     *
     * @param Class $request Illuminate\Http\Request
     *
     * @return Json Illuminate\Http\Response
     */
    public function read(Request $request, $id = null)
    {
        $notification = Auth::User()->unreadNotifications();
        $message = 'Read All Message';
        if ($id) {
            $notification->where('id', $id);
            $message = 'Read Successfully.';
        }
        $notification->update(
            [
                'read_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]
        );
        return $this->setMessage($message)->response();
    }

    /**
     * To Unread notification of login User.
     *
     * @param Class $request Illuminate\Http\Request
     *
     * @return Json Illuminate\Http\Response
     */
    public function unRead(Request $request, $id = null)
    {
        $unread_count = Auth::User()->unreadNotifications()->count();

        return $this->setData(
            [
                'count' => $unread_count
            ])->response();
    }


    /**
     * To delete notification of login User.
     *
     * @param Class $request Illuminate\Http\Request
     *
     * @return Json Illuminate\Http\Response
     */

    public function delete($id = null) {
        $notification = Auth::User()->notifications();
        $message = 'All notification are deleted successfully';
        if ($id) {
            $notification->where('id', $id);
            $message = 'Deleted Successfully.';
        }
        $notification->delete();
        return $this->setMessage($message)->response();
    }
}
