# Project Analysis: DB-To-HTML-Form-Generator

## Current State
The project is a PHP-based utility that introspects MySQL database tables using the `DESCRIBE` command and dynamically generates HTML form fields based on column types.

### Technical Debt & Observations
1. **Type Mapping Logic**: The current implementation uses `strpos()` within a `switch` statement, which is logically flawed in PHP (since `strpos` returns an integer/false, not a string). This likely causes the `default` case to trigger more often than intended.
2. **Security**: While `sanitizeMySQL` is called, the project relies on `$_GET` parameters for table names, which is a high-risk area for SQL injection if not handled perfectly.
3. **UI/UX**: Hardcoded Bootstrap classes are used, limiting the flexibility of the generated output.
4. **Architecture**: The logic is tightly coupled; the PHP script handles DB connection, business logic (mapping), and presentation (HTML strings) in one file.

## Proposed Roadmap

### Phase 1: Core Logic Fixes
- Fix the `switch` statement to correctly evaluate database types.
- Implement a more robust mapping object (Type $\rightarrow$ HTML Element).

### Phase 2: Modernization
- Decouple the generator into a Class-based structure.
- Create a JSON-based configuration for style templates (e.g., Tailwind, Bootstrap).

### Phase 3: Feature Expansion
- Add support for `ENUM` and `FOREIGN KEY` lookups to generate `<select>` dropdowns instead of text inputs.
- Implement a "Preview" mode before finalizing the HTML output.