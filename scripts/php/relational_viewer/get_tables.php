<?php

// sources: https://stackoverflow.com/questions/25759056/creating-an-html-form-depending-on-database-fields
// https://dev.mysql.com/doc/refman/8.0/en/describe.html
// https://dev.mysql.com/doc/refman/8.0/en/explain.html ***

require('../config.php');
require('../functions.php');

//test connection - if fail then die
if ($dbconn->connect_error) die("Fatal Error: db connection error");

// convert $TableData to json
// $TableData = json_encode($TableData);

function getColumnDetails($column)
{
    global $dbconn;
    try {
        $query = "DESCRIBE `$column`";

        $result = $dbconn->query($query);
        if (!$result) die("Fatal error occured while executing query." . $dbconn->error);

        return $result;

        // $fields =
        //     $type =
        //     $isNull =
        //     $default =
        //     $extraInfo = array();

        // $rows = $result->num_rows;

        // for ($j = 0; $j < $rows; ++$j) {
        //     $row = $result->fetch_array(MYSQLI_ASSOC);

        //     $key[] = $row['Key'];
        //     $fields[] = $row['Field'];
        //     $type[] = $row['Type'];
        //     $isNull[] = $row['Null'];
        //     $default[] = $row['Default'];
        //     $extraInfo[] = $row['Extra'];
        // }

        // while ($row = $result->fetch_assoc()) {
        //     $fields[] = $row['Field'];
        //     $type[] = $row['Type'];
        //     $isNull[] = $row['Null'];
        //     $default[] = $row['Default'];
        //     $extraInfo[] = $row['Extra'];
        // }


        // $compile = $jquery_submit = $fieldName = $visibilityState = $requiredAttribute = $inputType = $defaultValue = $defaultValueString = $maxLength = $form_control_class = null;

        // foreach ($fields as $key => $field) {
        //     # code...
        //     $fieldName = ucfirst(str_replace("_", " ", $field));
        //     $inputType = $type[$key];
        //     $requiredAttribute = $isNull[$key];
        //     $defaultValue = $default[$key];
        //     $autoIncrement = $extraInfo[$key];

        //     switch ($inputType) {
        //         case strpos($inputType, 'varchar'):
        //             # set input type to text and then set the max length
        //             $maxLength = get_string_between($inputType, "(", ")");
        //             // $maxLength = <<<_END
        //             // maxlength="$maxLength"
        //             // _END;
        //             // $inputType = "text";
        //             // $form_control_class = "form-control";
        //             break;

        //         case strpos($inputType, 'text'):
        //             # set input type to text and then set the max length
        //             $maxLength = get_string_between($inputType, "(", ")");
        //             // $maxLength = <<<_END
        //             // maxlength="$maxLength" rows="10"
        //             // _END;
        //             // $inputType = "text";
        //             // $form_control_class = "form-control";
        //             break;

        //         case strpos($inputType, 'int'):
        //             # set input type to text and then set the max length
        //             $maxLength = get_string_between($inputType, "(", ")");
        //             // $maxLength = <<<_END
        //             // maxlength="$maxLength" 
        //             // _END;
        //             // $inputType = "number";
        //             // $form_control_class = "form-control";
        //             break;

        //         case strpos($inputType, 'tinyint'):
        //             # set input type to text and then set the max length
        //             $maxLength = get_string_between($inputType, "(", ")");
        //             // $maxLength = <<<_END
        //             // maxlength="$maxLength" checked=false
        //             // _END;
        //             // $inputType = "checkbox";
        //             // $form_control_class = "form-check-input";
        //             break;

        //         case strpos($inputType, 'float'):
        //             # set input type to text and then set the max length
        //             $maxLength = get_string_between($inputType, "(", ")");
        //             // $maxLength = <<<_END
        //             // maxlength="$maxLength" 
        //             // _END;
        //             // $inputType = "number";
        //             // $form_control_class = "form-control";
        //             break;

        //         case strpos($inputType, 'date'):
        //             # set input type to text and then set the max length
        //             $maxLength = get_string_between($inputType, "(", ")");
        //             // $maxLength = <<<_END
        //             // maxlength="$maxLength" 
        //             // _END;
        //             // $inputType = "date";
        //             // $form_control_class = "form-control";
        //             break;

        //         case strpos($inputType, 'datetime'):
        //             # set input type to text and then set the max length
        //             $maxLength = get_string_between($inputType, "(", ")");
        //             // $maxLength = <<<_END
        //             //     maxlength="$maxLength" 
        //             //     _END;
        //             // $inputType = "datetime-local";
        //             // $form_control_class = "form-control";
        //             break;


        //         default:
        //             # set input type to text and then set the max length
        //             $maxLength = get_string_between($inputType, "(", ")");
        //             // $maxLength = 'maxlength="255"';
        //             // $inputType = "text";
        //             // $form_control_class = "form-control";
        //             break;
        //     }

        //     // if ($requiredAttribute == "YES") {
        //     //     $requiredAttribute = "required";
        //     // }

        //     // if ($defaultValue != "") {
        //     //     $defaultValueString = $defaultValue;
        //     // }

        //     // if ($autoIncrement == "auto_increment") {
        //     //     $visibilityState = "hidden";
        //     //     $defaultValueString = "null";
        //     // }

        //     if ($form_generation_style == "inline") {
        //         $compile .= <<<_END
        //             <div class="form-group my-4">
        //                 <label for="$field" class="fs-4 mb-4" $visibilityState> $fieldName </label>
        //                 <input class="form-control p-4" type="$inputType" name="$field" id="$field" $maxLength value="$defaultValueString" placeholder="$fieldName" $requiredAttribute $visibilityState>
        //             </div>
        //         _END;
        //     } else {
        //         $compile .= <<<_END
        //         <div class="form-group my-4">
        //             <div class="mb-3 row">
        //                 <div class="col-sm-2">
        //                     <label for="$field" class="fs-4 mb-4" $visibilityState> $fieldName </label>
        //                 </div>
        //                 <div class="col-sm-10">
        //                     <input class="$form_control_class p-4" type="$inputType" name="$field" id="$field" $maxLength value="$defaultValueString" placeholder="$fieldName" $requiredAttribute $visibilityState>
        //                 </div>
        //             </div>
        //         </div>
        //         _END;
        //     }

        //     // reset variables for next iteration
        //     $visibilityState = $requiredAttribute = $inputType = $defaultValue = $defaultValueString = $maxLength = null;
        // }

        // $form_html = <<< _END
        // <h2 class="text-start my-4 fs-3">Table: $tblname</h2>
        // $compile
        // _END;
        // echo $form_html;

        // $result = null;
        // $dbconn->close();
    } catch (\Throwable $th) {
        die("Exception Error: $th");
    }
};

// function to determine relationship type: one-to-one, one-to-many, many-to-many
// if a unique foreign key in $tableName (Table A) references a unique key / primary key in $referencedTableName (Table B) one-to-one relationship
function determineRelationshipType($dbconn, $tableName, $referencedTableName, $foreignKeyColumn)
{
    // if a foreign key in $tableName (Table A) references a unique key / primary key in $referencedTableName (Table B), it's a one-to-many relationship 
    $query = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '$tableName' AND REFERENCED_TABLE_NAME = '$referencedTableName' AND REFERENCED_COLUMN_NAME = '$foreignKeyColumn'";

    $result = $dbconn->query($query);
    if (!$result) die("Fatal error occured while executing query." . $dbconn->error);

    $rows = $result->num_rows;

    return ($rows == 1); // 'one-to-many if 1';
}

function getRelatedTables($tableName)
{
    global $dbconn;
    try {
        $query = "SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '$tableName' AND REFERENCED_TABLE_NAME IS NOT NULL";

        $result = $dbconn->query($query);
        if (!$result) die("Fatal error occured while executing query." . $dbconn->error);

        $rows = $result->num_rows;

        $relatedTables = array();

        for ($j = 0; $j < $rows; ++$j) {
            $row = $result->fetch_array(MYSQLI_ASSOC);

            $determineRelationship = determineRelationshipType($dbconn, $tableName, $row['REFERENCED_TABLE_NAME'], $row['COLUMN_NAME']);

            if ($determineRelationship) {
                # The foreign key in $tableName references a unique key or primary key in $referencedTableName, and it's a one-to-many relationship.
                $relationshipType = 'one-to-many';
            } else {
                # The foreign key in $tableName does not meet the criteria for a one-to-many relationship with $referencedTableName.
                $relationshipType = 'many-to-many';
            }

            $relatedTables[] = array(
                'name' => $row['REFERENCED_TABLE_NAME'],
                'type' => $relationshipType
            );
        }


        return $relatedTables;
    } catch (\Throwable $th) {
        die("Exception Error: $th");
    }
};

// try: get all table names and column names from our db (DB_DATABASE is defined in config.php) and compile into $TableData
// catch: if fail then die
try {
    $TableData = array();

    // get all table names from our db
    $query = "SHOW tables";

    $result = $dbconn->query($query);
    if (!$result) die("Fatal error occured while executing query." . $dbconn->error);

    $rows = $result->num_rows;

    // get number of fields
    $num_fields = $rows;

    $table_name = $table_name_text = null;

    // DB_DATABASE is defined in config.php
    $db_name = DB_DATABASE;
    // echo "db name: $db_name \n";

    $list_compile = <<<_END
    <h1 class="fs-5">Database: $db_name ( $num_fields tables )</h1>
    <hr>
    _END;

    $row_indexname = "Tables_in_$db_name";


    $iterator = 1;

    if ($rows == 0) die("Empty Error: No tables found.");

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $primaryKeyExists = $isPrimaryKey = false; // initialize primaryKeyExists and isPrimaryKey to false

        $table = array();

        $table['dbName'] = $db_name;

        $table['tableName'] = $row[$row_indexname]; // all table names in db

        $table['columns'] = array(); // initialize columns array

        // Query to get the columns of the table
        $columnResult = $dbconn->query("SHOW COLUMNS FROM " . $table['tableName']);

        while ($columnRow = $columnResult->fetch_array(MYSQLI_ASSOC)) {
            $isPrimaryKey = ($columnRow['Key'] == 'PRI');
            $table['columns'][] = array(
                'name' => $columnRow['Field'],
                'type' => $columnRow['Type'],
                'isPrimaryKey' =>  $isPrimaryKey
            );

            if ($isPrimaryKey) {
                $primaryKeyExists = true;
            }
        }

        // get the related tables
        $table['relatedTables'] = getRelatedTables($table['tableName']);

        $table['primaryKey'] = $primaryKeyExists;

        $TableData[] = $table;
    }

    $json = json_encode($TableData, JSON_PRETTY_PRINT);
    echo $json;
} catch (Exception $e) {
    die("Exception Error: " . $e->getMessage());
}
