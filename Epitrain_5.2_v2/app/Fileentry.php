<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Fileentry extends Model
{
    //
    use SearchableTrait;

 	protected $searchable = [
        'columns' => [
        	'fileentries.original_filename' => 10,
        ],
    ];

}
