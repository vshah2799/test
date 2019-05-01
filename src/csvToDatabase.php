<?php
/**
 * Created by PhpStorm.
 * User: gabe3
 * Date: 4/24/2019
 * Time: 9:53 PM
 */
require __DIR__ . '/../vendor/autoload.php';
class csvToDatabase
{
    public function __construct($x)
    {

        $table = sqliteFunctions::generateRandomTableName(10); //Creates random has value for name of database

        //change so we put the file that the person uploads

        $arrayObjects = csv::getRecords($x);

        $numOfObjects = arrayFunctions::arrayCount($arrayObjects);

        $keys = arrayFunctions::arrayKeys((array)$arrayObjects[0]);

        $columnStatement = sqliteFunctions::createColumnsString($keys);

        $headerInsert = sqliteFunctions::createInsertHeadersString($keys);

        $valuesInsert = sqliteFunctions::createInsertValues($keys);


        try {
            $pdo = (new SQLiteConnection())->connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Error Handling
            $stmt = "CREATE table $table(ID INTEGER PRIMARY KEY AUTOINCREMENT, $columnStatement);";
            $pdo->exec($stmt);
            $stmt = "INSERT INTO $table ($headerInsert) VALUES ($valuesInsert)";
            $stmt = $pdo->prepare($stmt);
            for($x = 0 ; $x<$numOfObjects; $x++)
            {
                $data = (array) $arrayObjects[$x];
                $stmt->execute($data);
            }
            //Selecting Data Needs word must be different function maybe? Also must be dynamic
            $stmt = $pdo->prepare("SELECT * FROM $table");
            $stmt->execute();
            $user = $stmt->fetch();


            print("Created $table Table.\n");

        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }
}