<?php
// sources: https://stackoverflow.com/questions/25759056/creating-an-html-form-depending-on-database-fields
// https://dev.mysql.com/doc/refman/8.0/en/describe.html
// https://dev.mysql.com/doc/refman/8.0/en/explain.html ***

require('../config.php');
require('../functions.php');

//test connection - if fail then die
if ($dbconn->connect_error) die("Fatal Error: db connection error");

if (!isset($_GET['tblname'])) die("Please provide a database name using url parameter: tblname");
else $tblname = sanitizeMySQL($dbconn, $_GET['tblname']);

/* 
contentType: false // "multipart/form-data" // "application/json; charset=utf-8"
*/

$jquery_submit = <<<_JQuery
<!-- <script> -->
$("#$tblname-form").on("submit", function (e) {
    e = e || window.event;
    e.preventDefault();
    e.stopImmediatePropagation();
    var form_data = new FormData($('#$tblname-form')[0]);
    setTimeout(function () {
        $.ajax({
            type: 'POST',
            url: 'path/to/script/script_name.php?param=param_value',
            processData: false,
            contentType: false,
            async: false,
            cache: false,
            data: form_data,
            contentType: false,
            beforeSend: function () {
                console.log('beforeSend: submitting $tblname-form');
                // validation code here
            },
            success: function (response) {
                // handle success response here based on what you are returning from php script
                if (response.startsWith("success")) {
                    console.log('success: returning response - submitted $tblname-form successfully');
                    console.log("Response: " + response);
                    // pass success message output to ui
                    $("#$tblname-form > #form-output-message").html("Bulk data submitted successfully.");
                    // toggle alert classes
                    $("#$tblname-form > #form-output-message").show();
                    $("#$tblname-form > #form-output-message").removeClass("alert-info");
                    $("#$tblname-form > #form-output-message").addClass("alert-success");
                } else {
                    console.log("error: returning response - an error occurred");
                    console.log("Response: " + response);
                    // pass error message output to ui
                    $("#$tblname-form > #form-output-message").html("An error occured whilst processing your request: " + response);
                    // toggle alert classes
                    $("#$tblname-form > #form-output-message").show();
                    $("#$tblname-form > #form-output-message").removeClass("alert-info");
                    $("#$tblname-form > #form-output-message").addClass("alert-danger");
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // error handling code here
                console.log("exception error: " + thrownError + " | " + xhr.statusText + " | " + xhr.responseText);
            }
        });
    }, 1000);
});
<!-- </script> -->
_JQuery;

echo $jquery_submit;

// try {
//     $query = "DESCRIBE `$tblname`";

//     $result = $dbconn->query($query);
//     if (!$result) die("Fatal error occured while executing query." . $dbconn->error);

//     $fields =
//         $type =
//         $isNull =
//         $default =
//         $extraInfo = array();

//     $rows = $result->num_rows;

//     for ($j = 0; $j < $rows; ++$j) {
//         $row = $result->fetch_array(MYSQLI_ASSOC);

//         $fields[] = $row['Field'];
//         $type[] = $row['Type'];
//         $isNull[] = $row['Null'];
//         $default[] = $row['Default'];
//         $extraInfo[] = $row['Extra'];
//     }

//     // while ($row = $result->fetch_assoc()) {
//     //     $fields[] = $row['Field'];
//     //     $type[] = $row['Type'];
//     //     $isNull[] = $row['Null'];
//     //     $default[] = $row['Default'];
//     //     $extraInfo[] = $row['Extra'];
//     // }



//     $compile = $jquery_submit = $fieldName = $visibilityState = $requiredAttribute = $inputType = $defaultValue = $defaultValueString = $maxLength = $form_control_class = null;

//     foreach ($fields as $key => $field) {
//         # code...
//         $fieldName = ucfirst(str_replace("_", " ", $field));
//         $inputType = $type[$key];
//         $requiredAttribute = $isNull[$key];
//         $defaultValue = $default[$key];
//         $autoIncrement = $extraInfo[$key];

//         // switch ($inputType) {
//         //     case strpos($inputType, 'varchar'):
//         //         # set input type to text and then set the max length
//         //         $maxLength = get_string_between($inputType, "(", ")");
//         //         $maxLength = <<<_END
//         //         maxlength="$maxLength"
//         //         _END;
//         //         $inputType = "text";
//         //         $form_control_class = "form-control";
//         //         break;

//         //     case strpos($inputType, 'text'):
//         //         # set input type to text and then set the max length
//         //         $maxLength = get_string_between($inputType, "(", ")");
//         //         $maxLength = <<<_END
//         //         maxlength="$maxLength" rows="10"
//         //         _END;
//         //         $inputType = "text";
//         //         $form_control_class = "form-control";
//         //         break;

//         //     case strpos($inputType, 'int'):
//         //         # set input type to text and then set the max length
//         //         $maxLength = get_string_between($inputType, "(", ")");
//         //         $maxLength = <<<_END
//         //         maxlength="$maxLength" 
//         //         _END;
//         //         $inputType = "number";
//         //         $form_control_class = "form-control";
//         //         break;

//         //     case strpos($inputType, 'tinyint'):
//         //         # set input type to text and then set the max length
//         //         $maxLength = get_string_between($inputType, "(", ")");
//         //         $maxLength = <<<_END
//         //         maxlength="$maxLength" checked=false
//         //         _END;
//         //         $inputType = "checkbox";
//         //         $form_control_class = "form-check-input";
//         //         break;

//         //     case strpos($inputType, 'float'):
//         //         # set input type to text and then set the max length
//         //         $maxLength = get_string_between($inputType, "(", ")");
//         //         $maxLength = <<<_END
//         //         maxlength="$maxLength" 
//         //         _END;
//         //         $inputType = "number";
//         //         $form_control_class = "form-control";
//         //         break;

//         //     case strpos($inputType, 'date'):
//         //         # set input type to text and then set the max length
//         //         $maxLength = get_string_between($inputType, "(", ")");
//         //         $maxLength = <<<_END
//         //         maxlength="$maxLength" 
//         //         _END;
//         //         $inputType = "date";
//         //         $form_control_class = "form-control";
//         //         break;

//         //     case strpos($inputType, 'datetime'):
//         //         # set input type to text and then set the max length
//         //         $maxLength = get_string_between($inputType, "(", ")");
//         //         $maxLength = <<<_END
//         //             maxlength="$maxLength" 
//         //             _END;
//         //         $inputType = "datetime-local";
//         //         $form_control_class = "form-control";
//         //         break;


//         //     default:
//         //         # set input type to text and then set the max length
//         //         $maxLength = get_string_between($inputType, "(", ")");
//         //         $maxLength = 'maxlength="255"';
//         //         $inputType = "text";
//         //         $form_control_class = "form-control";
//         //         break;
//         // }

//         // if ($requiredAttribute == "YES") {
//         //     $requiredAttribute = "required";
//         // }

//         // if ($defaultValue != "") {
//         //     $defaultValueString = $defaultValue;
//         // }

//         // if ($autoIncrement == "auto_increment") {
//         //     $visibilityState = "hidden";
//         //     $defaultValueString = "null";
//         // }

        

//         // reset variables for next iteration
//         // $visibilityState = $requiredAttribute = $inputType = $defaultValue = $defaultValueString = $maxLength = null;
//     }

//     echo $compile;

//     $result = null;
//     $dbconn->close();
// } catch (\Throwable $th) {
//     throw $th;
// }