console.log("ready");

$('#register').click(function(){
		console.log("enter");
		var div = document.getElementById("dom-target");
		var myData = div.textContent.toString().trim();
		console.log(myData);


			//	var error = (<?php echo $error ?>).toString();
				//console.log(error );
		$('#createUserMesage').html("started");
	
    if( myData === "failed"){
		alert("User failed to be created");
		// $('#createUserMesage').html("User successfully created");

	}else{
		//$('#createUserMesage').html("User failed to be created");

		alert("User successfully created");
	}
});