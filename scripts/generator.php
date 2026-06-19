<?php
require_once 'SchemaMapper.php';

use FormGenerator\SchemaMapper;

// Initialize the mapper
$mapper = new SchemaMapper();

// ... (Existing database connection and query logic should be here) ...
// Assuming $columns is the result of the DESCRIBE query

if (isset($columns) && is_array($columns)) {
    foreach ($columns as $column) {
        $dbType = $column['Type'] ?? ''; // e.g., "varchar(255)"
        $fieldName = $column['Field'] ?? 'unknown';
        
        // Use the new Mapper instead of the buggy switch(strpos())
        $htmlInputType = $mapper->mapType($dbType);
        $element = $mapper->getElementForType($htmlInputType);

        echo "<div class='form-group'>";
        echo "<label for='{$fieldName}'>" . ucfirst($fieldName) . "</label>";
        
        if ($element === 'input') {
            echo "<input type='{$htmlInputType}' name='{$fieldName}' id='{$fieldName}' class='form-control'>";
        } elseif ($element === 'textarea') {
            echo "<textarea name='{$fieldName}' id='{$fieldName}' class='form-control'></textarea>";
        } elseif ($element === 'select') {
            echo "<select name='{$fieldName}' id='{$fieldName}' class='form-control'><option value=''>Select...</option></select>";
        }
        
        echo "</div>";
    }
}