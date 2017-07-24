<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Decoda\Decoda;

class Shoutbox extends Model
{

 protected $table = 'shoutbox';
 protected $fillable = ['user', 'message', 'mentions'];

 /**
 * Get The Poster
 *
 * @access public
 * @return
 */
 public function poster()
 {
   return $this->belongsTo('App\User', 'user');
 }

 /**
 * Parse content and return valid HTML
 *
 */
 public function getMessageHtml()
 {
    $code = new Decoda($this->message);
    $code->defaults();
    $code->setXhtml(false);
    $code->setStrict(false);
    $code->setLineBreaks(true);
    return $code->parse();
  }
}
