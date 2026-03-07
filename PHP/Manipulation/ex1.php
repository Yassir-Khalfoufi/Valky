<?php
$handle = fopen("ex1.txt","r");
while(!feof($handle)){//end of file
    $line = fgets($handle);
    echo $line."<br>";
   echo (filesize("ex1.txt"));
}
// $handle = fopen("ex1.txt","w");
// fwrite($handle, "yassor");
// fclose($handle);
if(file_exists("ex1.txt"))
    {
        echo "FIchier existe";
    }
    else
        {
            echo "Ficher n'existe pas";
        }
// $lines=file("ex1.txt");
// foreach($lines as $line){
    // echo $ligne."<br>";
// }
unlink("ex1.txt");//removes the file 
?>