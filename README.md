# Nathan Grove's Personal Website (nathangrove.com)

This repository contains the source code for my personal website, designed with a unique dual interface: one optimized for terminal access via `curl` or `wget`, and another standard HTML version for web browsers. This was made to be able to be deployed directly to my hosting provider.

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

*   **Containerization:** Docker, Docker Compose (for development)
*   **Web Server:** Apache (within Docker)
*   **Backend:** PHP
*   **Frontend (Browser):** HTML, CSS
*   **Frontend (Terminal):** Plain Text, ASCII Art, ANSI Escape Codes
*   **Data:** JSON (for caching GitHub projects)
*   **Routing:** Apache (`.htaccess`), PHP (`index.php`)

## Running Locally (Docker)

1.  **Prerequisites:** Ensure you have Docker and Docker Compose installed.
2.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd nathangrove.com
    ```
3.  **Build and start the Docker container:**
    This command builds the custom Docker image (if it doesn't exist or if the Dockerfile changed) and starts the Apache/PHP service.
    ```bash
    docker-compose up --build -d
    ```
    *   Use `-d` to run in detached mode (in the background).
    *   To stop the service, run `docker-compose down`.
4.  **Access the site:**
    *   **Browser:** Open `http://localhost:8080` in your web browser (Note the port is 8080 as defined in `docker-compose.yml`).
    *   **Terminal:** Use `curl -s localhost:8080`, `curl -s localhost:8080/about`, etc. I find it easier to pipe the curl output to `less -r`

## File Structure

```
.
├── Dockerfile              # Defines the custom PHP/Apache image
├── docker-compose.yml      # Configures the Docker services
├── README.md               # This file
├── apache/
│   └── 000-default.conf    # Custom Apache virtual host configuration
└── src/
    ├── .htaccess           # Apache rewrite rules for routing
    ├── index.php           # Main router and logic script
    ├── projects.json       # Cache for GitHub project data
    └── pages/
        ├── *.html          # HTML page templates
        ├── *.txt           # Terminal page templates
        └── assets/
            └── style.css     # CSS for HTML pages
```