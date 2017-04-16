<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Forumtag extends Model
{
    //
    use SearchableTrait;

 	protected $searchable = [
        'columns' => [
        	'forumtags.forum_tag' => 10,
        ],
    ];

}
