<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Cache;
use Carbon\Carbon;
use Decoda\Decoda;
use App\Shoutbox;
use App\User;
use \Toastr;

class ShoutboxController extends Controller
{
  /**
  * Send Shout
  *
  *
  */
  public function send()
  {
    $checkSendRate = Shoutbox::where('user', '=', Auth::user()->id)->where('created_at', '>=', Carbon::now()->subSeconds(2))->first();
    if($checkSendRate)
      {
        return 'Wait 2 Seconds Between Posts Please';
      }
    $v = Validator::make(Request::all(), [
          'message'=>'required|min:1|max:500|regex:/^[(a-zA-Z\-)]+$/u'
    ]);
    if ($v->fails()) {
        Toastr::error('There was a error with your input!', 'Error!', ['options']);
      }
    if(Request::ajax())
    {
      preg_match_all('/(@\w+)/', Request::get('message'), $mentions);
      $mentionIDs = [];
      foreach($mentions[0] as $mention)
      {
        $findUser = User::where('username', 'LIKE', '%' . str_replace('@', '', $mention) . '%')->first();
      if(!empty($findUser->id))
      {
        $mentionIDs[] = $findUser['id'];
      }
    }
    $mentions = implode(',', $mentionIDs);
    if(count($mentions) > 0)
      {
        $insertMessage = Shoutbox::create(['user' => Auth::user()->id, 'message' => Request::get('message'), 'mentions' => $mentions]);
      } else {
        $insertMessage = Shoutbox::create(['user' => Auth::user()->id, 'message' => Request::get('message')]);
      }

      $data = '<li class="list-group-item">
      <div class="profile-avatar tiny pull-left" style="background-image: url(/img/profil.png);"></div>
      <h5 class="list-group-item-heading"><a href="#">' . Auth::user()->username . '</a></h5><p class="message-content"><time>' . date("H:i", time()) . '</time>' . e(Request::get('message')) . '</p>
      </li>';

      Cache::forget('shoutbox_messages');
      return Response::json(['success' => true, 'data' => $data]);
    }
 }

  /**
  * Fetch Shout
  *
  *
  */
  public function fetch()
  {
    if(Request::ajax())
    {
      $getData = Cache::remember('shoutbox_messages', 60, function()
      //$getData = Shoutbox::orderBy('created_at', 'desc')->take(25)->get();
    {
      return Shoutbox::orderBy('created_at', 'desc')->take(150)->get();
    });
    $getData = $getData->reverse();
    $data = [];
      foreach($getData as $messages)
      {
        $class = '';
      if(in_array(Auth::user()->id, explode(',', $messages->mentions)))
      {
        $class = 'mentioned';
      }
        $data[] = '<li class="list-group-item ' . $class . '">
        <div class="profile-avatar tiny pull-left" style="background-image: url(/img/profil.png);"></div>
        <h5 class="list-group-item-heading"><a href="{{ route("profil", array("username" => '. e($messages->poster->username) .', "id" => '. e($messages->poster->id) .')) }}">' . e($messages->poster->username) . '</a><span class="badge-extra text-bold" style="color:' . ($messages->poster->group->color) . '">' . ($messages->poster->group->name) . '</span></h5>
        <p class="message-content"><time>' . ($messages->created_at->diffForHumans()) . '</time>' . \LaravelEmojiOne::toImage(e($messages->message->getMessageHtml())) . '</p>
        </li>';
      }
      return Response::json(['success' => true, 'data' => $data]);
    }
  }
}
