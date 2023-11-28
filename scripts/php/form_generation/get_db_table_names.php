<?php
// sources: https://stackoverflow.com/questions/25759056/creating-an-html-form-depending-on-database-fields
// https://dev.mysql.com/doc/refman/8.0/en/describe.html
// https://dev.mysql.com/doc/refman/8.0/en/explain.html ***

require('../config.php');
require('../functions.php');

//test connection - if fail then die
if ($dbconn->connect_error) die("Fatal Error: db connection error");

$jquery_submit = $tblname = $jquery_submit_script =
    $db_tables_list = null;

$fields =
    $type =
    $isNull =
    $default =
    $extraInfo = array();

try {
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

    if ($rows == 0) {
        //there is no result die and return with the response
        die("No tables found.");
    } else {
        // $jquery_submit_script = "<!-- custom js -->\n";
        // $compileForm = "<!-- custom html forms -->\n";
        for ($j = 0; $j < $rows; ++$j) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            // echo print_r($row);

            $table_name = $row[$row_indexname]; // 'Tables_in_<$db_name>'
            $table_name_text = ucfirst(str_replace("_", " ", $table_name));

            $list_compile .= <<<_END
            <div class="table-select-item">
                <button id="tbl-$table_name-$iterator" class="table-select-btn" onclick="$.compileFormPreview('$table_name');">$iterator. $table_name_text</button>
            </div>
            _END;

            $iterator++;
        }

        $db_tables_list = $list_compile;

        echo $db_tables_list;
    }

    $result = null;
    $dbconn->close();
} catch (\Throwable $th) {
    throw "Exception error: " . $th;
}
