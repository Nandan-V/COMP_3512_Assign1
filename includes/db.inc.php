   <?php
       require_once('config.inc.php');
       function getData($sql, $parameters) {
        try {
            $pdo = new PDO(DBCONNSTRING);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            if (!is_array($parameters)) {
                $parameters = [$parameters];
            }
    
            if (count($parameters) > 0) {
                $statement = $pdo->prepare($sql);
                for ($i = 0; $i < count($parameters); $i++) {
                    $statement->bindValue($i + 1, $parameters[$i]);
                }
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC); // Fetch only associative arrays
            } else {
                $result = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC); // Fetch only associative arrays
            }
    
            $pdo = null; // close db connection
            return $result; // Return the result
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
       

   
   ?>