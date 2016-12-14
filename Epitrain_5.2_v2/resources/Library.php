<?php
    class library{
        var $user;
        var $books;

        function $set_user($user){
            $this->user = $user;
        }
        function $set_books($books){
            $this->books = $books;
        }

        function $get_user(){
            return $this->user;
        }
        function $get_books(){
            return $this->books;
        }

        function $add_book($book){
            array_push($this->books, $book);
            return $book;
        }

        function $get_book($book_name){
            foreach($this->books as $a_book){
                if ($a_book->namestrpos($book_name) > 0){
                    return $a_book;
                }
            }
        }
				
				/**delete a book from the user's collection
						return true if the book is found and deleted
						false otherwise
						Note: the privilege for downloading the book again should still be saved elsewhere
				*/
				function $delete($resource_id)(
						$book_index = array_search($resource_id, $this->books);
						if ($book_index === false){
							return false;
						}
						unset($this->books[$book_index]);
						array_values($this->books);
						return true;
				)
    }
?>