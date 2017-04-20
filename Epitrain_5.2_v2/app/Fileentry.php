<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;
 /**
  * Fileentry object to query database table Fileentries. Inherit from EloquentModel.
  */
class Fileentry extends Model
{

    use SearchableTrait;

 	protected $searchable = [
        'columns' => [
        	'fileentries.original_filename' => 10,
        ],
    ];

}
