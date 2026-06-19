# Modernization Plan: Multi-DBMS Form Generator

## Proposed Architecture: The "Bridge" Approach

### 1. Layered Responsibility
- **Infrastructure Layer**: `DatabaseDrivers` $\rightarrow$ Handles raw connections and schema extraction.
- **Domain Layer**: `SchemaMapper` $\rightarrow$ Normalizes DBMS-specific types into a generic "Field Definition" object.
- **Presentation Layer**: `FormRenderers` $\rightarrow$ Converts Field Definitions into HTML/CSS.

### 2. Class Diagram Concept
- `FormGeneratorService` (The Orchestrator)
    - $\rightarrow$ `IDatabaseDriver` (Interface)
        - `MySQLDriver`
        - `PostgresDriver`
        - `SQLiteDriver`
    - $\rightarrow$ `IFormRenderer` (Interface)
        - `Bootstrap5Renderer`
        - `TailwindRenderer`
        - `MaterialUIRenderer`

### 3. Technical Stack Shift
- **Language**: Upgrade to **PHP 8.2+** (to leverage Readonly properties, Enums, and Strict Typing).
- **Dependency Management**: Implement **Composer** for autoloading (PSR-4) and managing database libraries (e.g., `PDO`).
- **Configuration**: Move hardcoded settings to a `.env` file for security and environment flexibility.

## Implementation Phases

### Phase 1: Abstraction (The "Interface" Era)
- Replace the procedural script with a `FormGenerator` class.
- Implement the `IDatabaseDriver` interface to stop the code from being "MySQL-only."

### Phase 2: The Mapper (The "Intelligence" Era)
- Create a `TypeMap` utility that handles the conversion:
  - `VARCHAR`/`TEXT` $\rightarrow$ `InputType::TEXT`
  - `INT`/`DECIMAL` $\rightarrow$ `InputType::NUMBER`
  - `TINYINT(1)` $\rightarrow$ `InputType::CHECKBOX`

### Phase 3: The Renderer (The "Visual" Era)
- Implement a plugin system where new CSS frameworks can be added as separate classes without modifying the core logic.