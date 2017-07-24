<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
