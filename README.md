# Nathan Grove's Personal Website (nathangrove.com)

This repository contains the source code for my personal website, designed with a unique dual interface: one optimized for terminal access via `curl` or `wget`, and another standard HTML version for web browsers.

## Features

*   **Dual Interface:**
    *   **Terminal:** Accessing the site with `curl` or `wget` (e.g., `curl nathangrove.com`) serves plain text content with ASCII art and ANSI colors for a retro feel.
    *   **Browser:** Accessing the site via a web browser serves standard HTML pages with a dark theme and CSS styling.
*   **Dynamic Content:**
    *   The `/projects` page dynamically fetches my public repositories from the GitHub API.
    *   Results are cached in `src/projects.json` for 24 hours to minimize API calls.
    *   Both terminal and browser versions display the fetched project data, formatted appropriately.
*   **Routing:** A simple PHP router (`src/index.php`) handles requests based on the User-Agent and the requested path.
*   **Pages:**
    *   `/` (Home): Introduction and overview.
    *   `/about`: Information about my background and experience.
    *   `/projects`: Dynamically generated list of my GitHub projects.
    *   `/contact`: Contact information (Location, Email, LinkedIn).

## Technology Stack

*   **Backend:** PHP (using the built-in web server for local development)
*   **Frontend (Browser):** HTML, CSS
*   **Frontend (Terminal):** Plain Text, ASCII Art, ANSI Escape Codes
*   **Data:** JSON (for caching GitHub projects)

## Running Locally

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd nathangrove.com
    ```
2.  **Start the PHP built-in web server:**
    The server needs to be pointed to the `src` directory as the document root, and use `index.php` as the router script to handle requests correctly.
    ```bash
    php -S localhost:8000 -t src/ src/index.php
    ```
    *(Note: The `localdev.sh` script in the root contains this command)*
3.  **Access the site:**
    *   **Browser:** Open `http://localhost:8000` in your web browser.
    *   **Terminal:** Use `curl -s localhost:8000`, `curl -s localhost:8000/about`, etc. I find it easier to read if piped to `less -r`

## File Structure
```
├── localdev.sh # Script to start local PHP server  
├── README.md # This file  
└── src/  
    ├── .htaccess # Apache config (optional, PHP router preferred for dev)  
    ├── index.php # Main router and logic script  
    ├── projects.json # Cache for GitHub project data (not stored in the repo)
    └── pages/  
        ├── *.html # HTML page templates  
        ├── *.txt # Terminal page templates  
        └── assets/  
            └── style.css # CSS for HTML pages 
```