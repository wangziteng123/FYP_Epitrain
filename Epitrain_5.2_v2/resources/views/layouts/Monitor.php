<?php
// Ignore user aborts and allow the script
// to run forever
	use DB;
	use Auth;

	ignore_user_abort(true);
	set_time_limit(0);

	echo connection_aborted();
	$thisId = auth()->user()->id;
	exit(var_dump($thisId));
	while(1)
	{
		echo "Whatever you echo here wont be printed anywhere but it is required in order to work.";
		flush();
		if(connection_aborted())
		{
			break;
			// Breaks only when browser is closed
		}
	}

	/*
	Action you want to take after browser is closed.
	Write your code here
	*/
	DB::table('sessions')->where('user_id', $thisId)
	->update(
	    ['loggedIn' => 0]
	);

?>