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

$form_generation_style = null;
if (!isset($_GET['style'])) $form_generation_style = "inline";
else $form_generation_style = sanitizeMySQL($dbconn, $_GET['style']);

try {
    $query = "DESCRIBE `$tblname`";

    $result = $dbconn->query($query);
    if (!$result) die("Fatal error occured while executing query." . $dbconn->error);

    $fields =
        $type =
        $isNull =
        $default =
        $extraInfo = array();

    $rows = $result->num_rows;

    for ($j = 0; $j < $rows; ++$j) {
        $row = $result->fetch_array(MYSQLI_ASSOC);

        $fields[] = $row['Field'];
        $type[] = $row['Type'];
        $isNull[] = $row['Null'];
        $default[] = $row['Default'];
        $extraInfo[] = $row['Extra'];
    }

    // while ($row = $result->fetch_assoc()) {
    //     $fields[] = $row['Field'];
    //     $type[] = $row['Type'];
    //     $isNull[] = $row['Null'];
    //     $default[] = $row['Default'];
    //     $extraInfo[] = $row['Extra'];
    // }


    $compile = $jquery_submit = $fieldName = $visibilityState = $requiredAttribute = $inputType = $defaultValue = $defaultValueString = $maxLength = $form_control_class = null;

    foreach ($fields as $key => $field) {
        # code...
        $fieldName = ucfirst(str_replace("_", " ", $field));
        $inputType = $type[$key];
        $requiredAttribute = $isNull[$key];
        $defaultValue = $default[$key];
        $autoIncrement = $extraInfo[$key];

        switch ($inputType) {
            case strpos($inputType, 'varchar'):
                # set input type to text and then set the max length
                $maxLength = get_string_between($inputType, "(", ")");
                $maxLength = <<<_END
                maxlength="$maxLength"
                _END;
                $inputType = "text";
                $form_control_class = "form-control";
                break;

            case strpos($inputType, 'text'):
                # set input type to text and then set the max length
                $maxLength = get_string_between($inputType, "(", ")");
                $maxLength = <<<_END
                maxlength="$maxLength" rows="10"
                _END;
                $inputType = "text";
                $form_control_class = "form-control";
                break;

            case strpos($inputType, 'int'):
                # set input type to text and then set the max length
                $maxLength = get_string_between($inputType, "(", ")");
                $maxLength = <<<_END
                maxlength="$maxLength" 
                _END;
                $inputType = "number";
                $form_control_class = "form-control";
                break;

            case strpos($inputType, 'tinyint'):
                # set input type to text and then set the max length
                $maxLength = get_string_between($inputType, "(", ")");
                $maxLength = <<<_END
                maxlength="$maxLength" checked=false
                _END;
                $inputType = "checkbox";
                $form_control_class = "form-check-input";
                break;

            case strpos($inputType, 'float'):
                # set input type to text and then set the max length
                $maxLength = get_string_between($inputType, "(", ")");
                $maxLength = <<<_END
                maxlength="$maxLength" 
                _END;
                $inputType = "number";
                $form_control_class = "form-control";
                break;

            case strpos($inputType, 'date'):
                # set input type to text and then set the max length
                $maxLength = get_string_between($inputType, "(", ")");
                $maxLength = <<<_END
                maxlength="$maxLength" 
                _END;
                $inputType = "date";
                $form_control_class = "form-control";
                break;

            case strpos($inputType, 'datetime'):
                # set input type to text and then set the max length
                $maxLength = get_string_between($inputType, "(", ")");
                $maxLength = <<<_END
                    maxlength="$maxLength" 
                    _END;
                $inputType = "datetime-local";
                $form_control_class = "form-control";
                break;


            default:
                # set input type to text and then set the max length
                $maxLength = get_string_between($inputType, "(", ")");
                $maxLength = 'maxlength="255"';
                $inputType = "text";
                $form_control_class = "form-control";
                break;
        }

        if ($requiredAttribute == "YES") {
            $requiredAttribute = "required";
        }

        if ($defaultValue != "") {
            $defaultValueString = $defaultValue;
        }

        if ($autoIncrement == "auto_increment") {
            $visibilityState = "hidden";
            $defaultValueString = "null";
        }

        if ($form_generation_style == "inline") {
            $compile .= <<<_END
                <div class="form-group my-4">
                    <label for="$field" class="fs-4 mb-4" $visibilityState> $fieldName </label>
                    <input class="form-control p-4" type="$inputType" name="$field" id="$field" $maxLength value="$defaultValueString" placeholder="$fieldName" $requiredAttribute $visibilityState>
                </div>
            _END;
        } else {
            $compile .= <<<_END
            <div class="form-group my-4">
                <div class="mb-3 row">
                    <div class="col-sm-2">
                        <label for="$field" class="fs-4 mb-4" $visibilityState> $fieldName </label>
                    </div>
                    <div class="col-sm-10">
                        <input class="$form_control_class p-4" type="$inputType" name="$field" id="$field" $maxLength value="$defaultValueString" placeholder="$fieldName" $requiredAttribute $visibilityState>
                    </div>
                </div>
            </div>
            _END;
        }

        // reset variables for next iteration
        $visibilityState = $requiredAttribute = $inputType = $defaultValue = $defaultValueString = $maxLength = null;
    }

    $form_html = <<< _END
    <h2 class="text-start my-4 fs-3">Table: $tblname</h2>
    $compile
    _END;
    echo $form_html;

    $result = null;
    $dbconn->close();
} catch (\Throwable $th) {
    throw $th;
}

// $jquery_submit = <<<_JQuery
    // <script>
    //     $("#$tblname-form").on("submit", function (e) {
    //         e = e || window.event;
    //         e.preventDefault();
    //         e.stopImmediatePropagation();
    //         var form_data = new FormData($('#$tblname-form')[0]);
    //         setTimeout(function () {
    //             $.ajax({
    //                 type: 'POST',
    //                 url: 'scripts/php/database/dynamic_dbtable_insert.php?tblsubmit=$tblname',
    //                 processData: false,
    //                 contentType: false,
    //                 async: false,
    //                 cache: false,
    //                 data: form_data,
    //                 contentType: false /* "multipart/form-data" */ /* "application/json; charset=utf-8" */,
    //                 beforeSend: function () {
    //                     console.log('beforeSend: submitting $tblname-form');
    //                 },
    //                 success: function (response) {
    //                     if (response.startsWith("success")) {
    //                         console.log('success: returning response - submitted $tblname-form successfully');
    //                         console.log("Response: \n" + response);
    //                         // pass success message output to ui
    //                         $("#$tblname-form > #form-output-message").html("Bulk data submitted successfully.");
    //                         // toggle alert classes
    //                         $("#$tblname-form > #form-output-message").show();
    //                         $("#$tblname-form > #form-output-message").removeClass("alert-info");
    //                         $("#$tblname-form > #form-output-message").addClass("alert-success");
    //                     } else {
    //                         console.log("error: returning response - an error occurred");
    //                         console.log("Response: \n" + response);
    //                         // pass error message output to ui
    //                         $("#$tblname-form > #form-output-message").html("An error occured whilst processing your request. \n\n" + response);
    //                         // toggle alert classes
    //                         $("#$tblname-form > #form-output-message").show();
    //                         $("#$tblname-form > #form-output-message").removeClass("alert-info");
    //                         $("#$tblname-form > #form-output-message").addClass("alert-danger");
    //                     }
    //                 },
    //                 error: function (xhr, ajaxOptions, thrownError) {
    //                     console.log("exception error: " + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    //                 }
    //             });
    //         }, 1000);
    //     });
    // </script>
    // _JQuery;