<?php

namespace FormGenerator;

/**
 * SchemaMapper handles the translation of Database Column Types 
 * to HTML Input Types.
 */
class SchemaMapper {
    // Mapping of DB types to HTML input types
    private array $typeMap = [
        'varchar' => 'text',
        'text'    => 'textarea',
        'int'     => 'number',
        'integer' => 'number',
        'decimal' => 'number',
        'float'   => 'number',
        'date'    => 'date',
        'datetime'=> 'datetime-local',
        'timestamp'=> 'datetime-local',
        'tinyint' => 'checkbox', // Often used for booleans
        'enum'    => 'select',
    ];

    /**
     * Determines the appropriate HTML input type based on the DB type string.
     * This replaces the buggy strpos() switch logic.
     */
    public function mapType(string $dbType): string {
        $dbType = strtolower($dbType);

        // Direct match check
        foreach ($this->typeMap as $key => $value) {
            if (str_contains($dbType, $key)) {
                return $value;
            }
        }

        return 'text'; // Default fallback
    }

    /**
     * Returns the specific HTML element based on the mapped type.
     */
    public function getElementForType(string $mappedType): string {
        return match ($mappedType) {
            'textarea' => 'textarea',
            'select'   => 'select',
            default    => 'input',
        };
    }
}
