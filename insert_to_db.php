<?php
    date_default_timezone_set("America/New_York");

    function connDB() {
        $servername = "sql5.freemysqlhosting.net";
        $username = "sql5388638";
        $password = "8ZgiXn8gTA";
        $database = "sql5388638";
        try { 
            $conn = new PDO("mysql:host=".$servername.";dbname=".$database, $username, $password);
            $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) { return $e; }
        return $conn;
    }



    function rand_float($st_num=0,$end_num=1,$mul=1000000)
    {
        if ($st_num>$end_num) return false;
        return mt_rand($st_num*$mul,$end_num*$mul)/$mul;
    }


    function insert_stock($stocks) {
        $c = connDB();
        $valueTracker = [];
        foreach($stocks as $key => $value) {
            //get the ID
            $sql = "SELECT MAX(ID)+1 FROM Stocks;";
            $s = $c -> prepare($sql);
            $s -> execute();
            if ($max = $s -> fetchColumn()) $id = $max;
            else $id = 1; 

            //create initial values array 
            array_push($valueTracker, $value);

            // INSERT THE STOCK
            try {
                $sql = "INSERT INTO Stocks (ID, Name, IPO_Date) VALUES (".$id.", '".$key."', NOW());";
                $sql .= "INSERT INTO Records (TimeStamp, CurrentValue, ValueChange, Stocks_ID) VALUES (NOW(), ".$value.", 0, ".$id.");";
                $c -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $c -> exec($sql);
                echo "--- ".strval($id)."\t".strval($key)."\t\t".strval($value)."\t\t".date('h:i:s')."\n";
            } catch(PDOException $e) {echo $e;}
        }
        echo " \n ============================================================================== \n";
        sleep(1);
        $counter = 0;
        while($counter < 201) {
            for($i = 0; $i < count($valueTracker); $i++) {
                $value = $valueTracker[$i];
                $newValue = round((rand($value*(rand(89,99)*10), $value*(rand(101,111)*10)) / 1000), 2);
                if($newValue < 0.50) $newValue = round((rand($value*(101*10), $value*(rand(105,111)*10)) / 1000), 2);
                $difference = $newValue - $value;
                try {
                    $sql = "INSERT INTO Records (TimeStamp, CurrentValue, ValueChange, Stocks_ID) VALUES (NOW(), ".$newValue.", ".$difference.", ".($i + 1).");";
                    $c -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $c -> exec($sql);
                } catch(PDOException $e) {echo $e; /* WILL BE MOST LIKELY SERVER DISCONNECTION */ $c = connDB();}
                echo "--- ".strval($i+1)."\t\t".strval($newValue)."\t\t".strval($difference)."\t\t".date('h:i:s')."\n";
                $valueTracker[$i] = $newValue;
            }
            echo " ................................................................................\n";
            $counter++;
            sleep(120);
        }
        $c = null; //close connection
    }

    $stocks = array(
        'FinTech Club' => 1000.00, 
        'TAMID Group' => 50.00,
        'Film Society' => 315.00,
        'Knitting Club' => 27.50,
        'Photography' => 460.95,
        'StuVi RHC' => 10.00,
        'BU Smash Bros' => 91.70,
        'Badminton Club' => 35.40,
        'Business Law' => 46.25,
        'Student Gov.' => 120.00,
        'Greek Life Org.' => 230.00,
        'UPE CS Society' => 46.25
    );

    insert_stock($stocks);

?>