@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <ul class="breadcrumb pull-left" style="margin-bottom: 5px;font-size:20px">
            <li style="font-size:16px"><a href="/">Home</a></li>
            <li style="font-size:16px" class="active">View All Users</li>
        </ul>
    </div>
</div>

@if (Auth::user()->isAdmin)
    <div>
    <script>

    var users = '<?php echo $users->toJson(); ?>';
    var usersRawArray = JSON.parse(users);
    var usersArray = usersRawArray.data;

    var noOfClickName = 0;
    var noOfClickEmail = 0;

    //sorting name ascending order
    function sortName() {
        noOfClickName = noOfClickName +1;

        // checking if the onclick event is clicked before hw many times
        if( noOfClickName% 2 != 0){

         for(count = 1; count<usersArray.length;count++){
                for(num=count; num>0; num--){
                var prevElement = usersArray[num-1];
                var currentElement = usersArray[num];

                var n = usersArray[num].name.localeCompare(usersArray[num-1].name);
                if(n == -1){
                    usersArray[num] = prevElement;
                    usersArray[num-1]= currentElement;

                }else{
                    num = 0;
                }

                }

            }


            //print out the sorted data into html table
            var arrayLength= usersArray.length;
            alert(usersArray[0].name);
            for(i=0; i<arrayLength;i++){

                var idName = i + "name";
                var idEmail = i + "email";
                var isSubscribe = i +"admin";

                document.getElementById(idEmail).innerHTML = usersArray[i].email;
                document.getElementById(idName).innerHTML = usersArray[i].name;
                if(usersArray[i].subscribe == "Yes"){
                      document.getElementById(isSubscribe).innerHTML = "Yes";
                }
                else{
                    document.getElementById(isSubscribe).innerHTML = "No";
                }

             }

        }
        else{

            //print out the sorted data in descending order into html table
            var arrayLength= usersArray.length;
            var j = 0; //keep track of the id

            for(i=arrayLength-1; i>=0;i--){
                var idName = j + "name";
                var idEmail = j + "email";
                var isSubscribe = j +"admin";

                document.getElementById(idEmail).innerHTML = usersArray[i].email;
                document.getElementById(idName).innerHTML = usersArray[i].name;
                if(usersArray[i].subscribe == "Yes"){
                      document.getElementById(isSubscribe).innerHTML = "Yes";
                }
                else{
                    document.getElementById(isSubscribe).innerHTML = "No";
                }
                j = j + +1;
             }

        }

    }

    //sorting email
    function sortEmail() {

        noOfClickEmail = noOfClickEmail +1;
        if( noOfClickEmail% 2 != 0){
            for(count = 1; count<usersArray.length;count++){
                for(num=count; num>0; num--){
                var prevElement = usersArray[num-1];
                var currentElement = usersArray[num];

                var n = usersArray[num].email.localeCompare(usersArray[num-1].email);
                if(n == -1){
                    usersArray[num] = prevElement;
                    usersArray[num-1]= currentElement;

                }else{
                    num = 0;
                }

                }

            }


            //print out the sorted data into html table
            var arrayLength= usersArray.length;

            for(i=0; i<arrayLength;i++){

                var idName = i + "name";
                var idEmail = i + "email";
                var isSubscribe = i +"admin";

                document.getElementById(idEmail).innerHTML = usersArray[i].email;
                document.getElementById(idName).innerHTML = usersArray[i].name;
                if(usersArray[i].subscribe == "Yes"){
                      document.getElementById(isSubscribe).innerHTML = "Yes";
                }
                else{
                    document.getElementById(isSubscribe).innerHTML = "No";
                }

             }

        }
        else{
            //print out the sorted data in descending order into html table
            var arrayLength= usersArray.length;
            var j = 0; //keep track of the id

            for(i=arrayLength-1; i>=0;i--){
                var idName = j + "name";
                var idEmail = j + "email";
                var isSubscribe = j +"admin";

                document.getElementById(idEmail).innerHTML = usersArray[i].email;
                document.getElementById(idName).innerHTML = usersArray[i].name;
                if(usersArray[i].subscribe == "Yes"){
                    document.getElementById(isSubscribe).innerHTML = "Yes";
                }
                else{
                    document.getElementById(isSubscribe).innerHTML = "No";
                }
                j = j + +1;
            }



        }

    }
    </script>
    @if ($users->count())
    <div class="container">
        <h1>All Users</h1>
        <table class="table table-bordered" style="background-color:white; font-size: 18px">
            <thead>
                <tr>

            <th onclick="sortEmail()" style="color:black;text-align:center">Email</th>
            <th onclick="sortName()" style="color:black;text-align:center">Name</th>
            <th style="color:black;text-align:center">Subscriber</th>
                </tr>
            </thead>

           <tbody>
               <script>


                   for(i=0; i<usersArray.length;i++){
                       document.write(" <tr>");
                       var idEmail = i + "email";
                       var sentence =  "<td style='color:black' id=" + idEmail + ">"+ usersArray[i].email +"</td>"
                       document.write(sentence);
                       var idName = i + "name";
                       sentence = "<td style='color:black' id=" + idName + ">"+ usersArray[i].name +"</td>"
                       document.write(sentence);
                       var isSubscribe = i +"admin";
                       if(usersArray[i].subscribe == "Yes"){
                           document.write("  <td style='color:black' id=" + isSubscribe+ ">Yes</td>");
                       }else{
                       document.write("  <td style='color:black'id=" + isSubscribe+ ">No</td>");
                       }
                       document.write(" </tr>");

                   }

               </script>

           </tbody>

        </table>
    </div>
    {{ $users->links() }}
    @else
        There are no users
    @endif

    </div>

@endif


@endsection
