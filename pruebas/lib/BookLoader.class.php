<?php
class BookLoader {
       public static function Search($title,$author,$f="books.csv")
       {
              $fields = array("Title","Author","Year","Publisher");
              $results = array();
              $h = fopen($f, 'r');
              $title = trim($title);
              $author = trim($author);

              while ( ($line = fgetcsv($h, 1000)) !== FALSE) {
                     if ( count($line) != 4 ) continue;
                     if ( (@strpos($line[0],$title) !== false || $title=="") &&
                            (@strpos($line[1],$author) !== false || $author=="") ) {
                           $results[] = array_combine($fields,$line);
                     }
              }

              fclose($h);
              return $results;
       }
}
?>