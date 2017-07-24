# Laravel-Shoutbox
![alt text](https://github.com/adam-p/markdown-here/raw/master/src/common/images/icon48.png "Logo") A Basic Laravel Shoutbox Where Members Can Chat

**Routes:**
```
// Shoutbox
Route::group(['prefix' => 'shoutbox'], function () {
    Route::get('/', ['as' => 'shoutbox-home', 'uses' => 'HomeController@home']);
    Route::post('messages', ['as' => 'shoutbox-fetch', 'uses' => 'ShoutboxController@fetch']);
    Route::post('send', ['as' => 'shoutbox-send', 'uses' => 'ShoutboxController@send']);
});
```

**Add To HomeController:**
```
//ShoutBox Block
$shoutboxItems = Cache::remember('shoutbox_messages', 60, function () {
    return Shoutbox::orderBy('created_at', 'desc')->take(100)->get();
});
$shoutboxItems = $shoutboxItems->reverse();
```

**View:**
```
<!-- ShoutBox -->
<div class="col-md-10 col-sm-10 col-md-offset-1">
  <div class="clearfix visible-sm-block"></div>
    <div class="panel panel-chat shoutbox">
      <div class="panel-heading">
        <h4>Chat Box</h4>
      </div>
      <div class="chat-messages">
        <ul class="list-group">
          @foreach($shoutboxItems as $messages)
          <?php
$class = '';
if (in_array(\Auth::user()->id, explode(',', $messages->mentions))) {
    $class = 'mentioned';
}
?>
            <li class="list-group-item {{ $class }}">
              <div class="profile-avatar tiny pull-left" style="background-image: url(/img/profil.png);"></div>
              <h5 class="list-group-item-heading"><a href="{{ route('profil', array('username' => $messages->poster->username, 'id' => $messages->poster->id)) }}">{{ $messages->poster->username }}</a><span class="badge-extra text-bold" style="color:{{ $messages->poster->group->color }}">{{ $messages->poster->group->name }}</span></h5>
              <p class="message-content">
                <time>{{ $messages->created_at->diffForHumans() }}</time>@emojione($messages->message)</p>
            </li>
            @endforeach
        </ul>
      </div>
      <div class="panel-footer ">
      <span class="badge-extra">Type <strong>:</strong> for emoji</span>
        <div class="form-group">
          <textarea class="form-control" id="chat-message"></textarea>
          <p id="chat-error" class="hidden text-danger"></p>
        </div>
      </div>
    </div>
  </div>
<!-- /ShoutBox -->
```

**CSS:**
```
.shoutbox {
  width: 100%;
  bottom: -1px;
  margin-bottom: 10px;
  border-radius: 0;
}

.profile-avatar {
  position: relative;
  z-index: 20;
  background-size: cover;
  background-position: center center;
  border-radius: 100%;
  -webkit-transition: opacity 0.3s ease-in;
  transition: opacity 0.3s ease-in;
}

.profile-avatar.tiny {
  width: 32px;
  height: 32px;
  border: 3px solid #7289da;
  margin-top: 5px;
}

.shoutbox .panel-heading {
  border-radius: 0;
}

.shoutbox .profile-avatar {
  box-shadow: none;
}

.shoutbox .list-group {
  height: 400px;
  overflow-y: auto;
}

.shoutbox .list-group-item-heading, .shoutbox p {
  margin-left: 48px;
  padding-right: 30px;
}

.shoutbox .list-group-item .message-content {
  position: relative;
}

.shoutbox .list-group-item time {
  position: absolute;
  top: 0;
  right: 0;
  color: #777777;
  font-size: 12px;
}

.shoutbox .form-group {
  width: 100%;
}

.shoutbox input.form-control {
  width: 100%;
  resize: none;
}

.mentioned {
  background-color: #C6FFFA;
}

.panel-chat {
  border-color: #ddd;
}

.panel-chat > .panel-heading {
  color: #fff;
  background-color: #373d43;
  border-color: #ddd;
}

.panel-chat > .panel-heading + .panel-collapse > .panel-body {
  border-top-color: #ddd;
}

.panel-chat > .panel-heading .badge {
  color: #373d43;
  background-color: #fff;
}

.panel-chat > .panel-footer + .panel-collapse > .panel-body {
  border-bottom-color: #ddd;
}

.panel-chat .close {
  color: #fff;
  position: absolute;
  top: 8px;
  right: 16px;
}

.panel-heading h3 {
  margin-top: 15px;
}

#chat-error {
  margin-left: 0px;
  margin-top: 5px;
}
```
