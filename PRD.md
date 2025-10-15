# Product Requirements Document: Convergenx

**Version:** 1.5
**Date:** 15 October, 2025  
**Author:** Don Ferris
**Status:** Final

---

## 1. Overview & Vision

**Product Name:** Convergenx  
*(Abbreviated as "Cx" and pronounced "Convergence" — but spelled with an 'X' because it's eXtensible)*

### Vision Statement

To create the most flexible, user-centric server dashboard and control panel for homelab enthusiasts, server administrators, and developers. Convergenx empowers users to build a unified, personalized command center for their entire digital ecosystem — from system health to container management, from backups to media services — all through a simple, grid-based, and plugin-driven interface.

### Core Philosophy

- **eXtensible** — Everything is a plugin. The core is a lightweight orchestrator.  
- **Unified** — A single pane of glass (or multiple dashboards if that better suits your workflows) for your entire server stack.  
- **Customizable** — The user has complete control over the layout, data, and appearance.  
- **Simple** — JSON configuration and a clean, intuitive UI lower the barrier to entry.

---

## 2. Problem Statement

Users running complex homelab environments (e.g., Docker, NextCloud, Plex, Jellyfin, *arr suite...) face a fragmented management experience. They often juggle multiple web interfaces, SSH terminals, and command-line tools to monitor status, perform actions, and troubleshoot.

Existing solutions are either:

- **Too Rigid** — Monolithic dashboards that are difficult to customize  
- **Too Complex** — Require significant coding knowledge to extend (e.g., Grafana)  
- **Too Limited** — Focus on only one aspect (e.g., only system monitoring or only Docker)

**Convergenx solves this** by providing a highly adaptable dashboard(s) that consolidates all critical information and controls.

---

## 3. Goals & Objectives

### Primary Goals (MVP)

1. **Functional Core** — Successfully deploy as a Docker-based LESP stack. Adminer is included for lightweight SQLite management.
2. **Grid UI** — Implement a drag-and-drop, grid-based layout system for dashboards, panels, and cards
3. **JSON Configuration** — Allow the entire dashboard layout to be defined and modified via a single JSON file  
4. **REST API** — Provide a secure, documented REST API for plugin operations  
5. **Core Plugin Set** — Deliver basic plugins for System Monitoring (CPU, Memory, Disk) and Docker container management  
6. **User Authentication** — Secure the dashboard with a multi-user login system

### Secondary Goals (Post-MVP)

1. **Rich Plugin Ecosystem** — Expand plugin library to cover [SYSTEM], [MEDIA SERVER], [HOMELAB], [BACKUP], [SCRIPTING],  [UTILITIES], and [MISC/OTHER] functionalities  
2. **Plugin Manager** — Build a UI for discovering, installing, and configuring plugins without manually editing files
3. **Plugin Builder** — Environment for building custom plugins - with extensive help/documentation and AI assistance
4. **Real-time Updates** — Implement WebSockets for live data updates (e.g., download speeds, CPU graphs)  
5. **Theming & Appearance** — Introduce a theming system (light/dark mode, custom CSS)  
6. **Mobile-Responsive UI** — Ensure the dashboard is usable on tablets and phones  
7. **Parent/Child Frame** — Optional mode to keep users within the Convergenx interface when accessing linked services

---

## 4. User Stories

### As a Homelab Admin, I want to:

- See the status of all my Docker containers at a glance so I can quickly identify failures  
- Configure push notifications for critical events (e.g., container down, disk full)  
- Restart a misbehaving container with a single click  
- See system resource usage (CPU, RAM, Disk) to plan for upgrades  
- Access a shell inside a container directly from the dashboard  
- Configure and monitor backups for Convergenx and other services from a unified card

### As a Media Server User, I want to:

- Monitor active downloads, including speed and ETA  
- See who is currently streaming and whether streams are transcoding  
- View bandwidth utilization over time

### As a Power User, I want to:

- Arrange panels and cards via a drag-and-drop interface  
- Create a "Sticky Notes" card for server-related todos  
- Add bookmarks to frequently accessed services  
- Navigate to bookmarked services (e.g., Sonarr, wiki) without losing Convergenx context  
- Define custom cards via JSON to display data from unsupported services

---

## 5. Functional Requirements & Features

### 5.1 Core Architecture

- **FR-CORE-01** — The application must be deployable as a single Docker Compose stack  
- **FR-CORE-02** — The backend will be built on a LESP (Linux, Nginx, SQLite, PHP) stack for simplicity and performance  
- **FR-CORE-03** — All dashboard layout and configuration must be stored in and driven by JSON configuration files  
- **FR-CORE-04** — A secure REST API must be available for plugin operations

### 5.2 User Interface (UI)

- **FR-UI-01** — A responsive, grid-based layout using a library like gridstack.js
- **FR-UI-02** — Configurable toolbar for quick access to commonly used tools  
- **FR-UI-03** — Support for multiple dashboards, selectable via a tabbed interface or drop-down selector in the main toolbar
- **FR-UI-04** - Panels are groups of grid cells that can be collapsed/expanded
- **FR-UI-05** - Cards are individual widgets within a panel. Users can add, remove, and rearrange cards  
- **FR-UI-06** — An "Edit Layout" mode toggles drag-and-drop functionality for the entire grid
- **FR-UI-07** — "Save Layout" mode saves the state of the dashboard so that...
- **FR-UI-08** — "Reset Layout" button reverts all changes made since the last "Save Layout". must be available to reset all panels to their default expanded/collapsed state and grid position  
- **FR-UI-06** — An optional "Parent/Child Frame" mode must be available. When enabled, links from Docker service names, bookmarks, and other integrated elements will open within a child frame of the Convergenx UI, which will maintain a persistent title bar and a "Cx Home" button to return to the main dashboard

### 5.3 Plugin System

- **FR-PLUGIN-01** — All data and functionality must be provided by plugins  
- **FR-PLUGIN-02** — A plugin is a self-contained directory with a `manifest.json` and necessary backend (PHP) and frontend (JS) files  
- **FR-PLUGIN-03** — The core system will provide a standard API for plugins to fetch data and register actions, including the ability to securely execute and retrieve data from system (BASH/Python) commands and scripts  
- **FR-PLUGIN-04** — Plugin settings should be configurable via the UI (stored in the main dashboard configuration)

### 5.4 Key Plugins (Categorized)

#### [SYSTEM]

- `system-time` — Displays current date and time, optionally with timezones  
- `system-cpu` — Shows current CPU load and a sparkline graph  
- `system-memory` — Displays RAM usage and a list of top memory-consuming processes  
- `system-disk` — Shows disk usage for mounted filesystems  
- `system-network` — Displays network I/O for active interfaces  
- `system-cron` — Lists upcoming cron jobs  
- `system-security` — Aggregates data from `who`, `fail2ban`, SSH logs, etc  
- `system-bash` — Executes a user-defined BASH script and displays its output  
- `system-python` — Executes a user-defined Python script and displays its output  
- `system-backup` — Configures, manages, and monitors backup jobs for Convergenx and other services (e.g., file backups, database dumps)

#### [MEDIA SERVER]

- `media-downloads` — Connects to downloaders (qBittorrent, NZBGet, SABnzbd) to show active downloads, speed, and ETA  
- `media-streams` — Connects to media servers (Plex, Jellyfin, Emby) to show active streams and transcoding status  
- `media-bandwidth` — Reports bandwidth usage from tools like `vnstat` or `iftop`

#### [HOMELAB]

- `homelab-docker` — Lists all Docker containers. Each container name links to its primary UI (respecting Parent/Child Frame setting)  
  - **Displays**: Status (Running/Stopped), Uptime, CPU%, Memory%, Error Logs (last 5 lines)  
  - **Provides**: Restart button, Terminal button (opens web-based terminal via `docker exec`)

#### [MISC/OTHER]

- `misc-bookmarks` — Grid of custom links to internal/external services with icons (respecting Parent/Child Frame setting)  
- `misc-wiki` — Integrates with a local wiki (e.g., Wiki.js, DokuWiki) to show recent changes or search  
- `misc-notes` — Rich-text sticky notes card  
- `misc-tasks` — Simple project management card (Todo list)

---

## 6. Non-Functional Requirements (NFRs)

- **NFR-01 Performance** — Dashboard should load in under 2 seconds. Card data should refresh asynchronously without full page reloads  
- **NFR-02 Security** — All access must be behind an authentication layer. App must run with least privilege. Passwords and API keys must be encrypted. REST API must implement rate limiting and CSRF protection  
- **NFR-03 Usability** — UI must be intuitive enough for non-technical users to add pre-configured cards, yet powerful enough for developers to create new plugins  
- **NFR-04 Reliability** — Core dashboard must remain stable even if a plugin fails or times out  
- **NFR-05 Maintainability** — Codebase must be well-documented. Plugin API must be versioned and stable

---

## 7. Technical Specifications

### 7.1 Directory Structure

```plaintext
data/
├── nginx/          # Nginx config
├── sqlite/         # SQLite data (optional)
│   └── adminer/    # for lightweight SQLite management.
├── convergenx/
│   ├── index.php                # Entry point
│   ├── config/
│   │   └── dashboard.json       # Panel/card layout
│   ├── plugins/
│   │   └── PluginName/
│   │       └── manifest.json    # Card definitions
│   │       └── logic.sh/php     # Optional logic
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── icons/
│   ├── themes/
│   │   ├── default/
│   │   └── darkmode/
│   ├── api/
│   │   └── trigger.php          # Shortcut endpoint
│   └── lib/
│       └── CardRenderer.php     # Core card rendering logic

### 7.2 Plugin Loader

	- Reads `dashboard.json` to determine active panels and plugins  
- Loads each plugin's `manifest.json`  
- Renders cards using plugin-defined logic  
- Supports refresh intervals and interactive controls

### 7.3 JSON Config Format

#### Dashboard Configuration (`config/dashboard.json`)

```json
{
  "dashboard": {
    "title": "My Homelab",
    "theme": "dark",
    "gridColumns": 12,
    "gridRows": 8,
    "parentChildFrame": true
  },
  "panels": [
    {
      "id": "panel_system",
      "title": "System Health",
      "width": 6,
      "height": 2,
      "x": 0,
      "y": 0,
      "collapsed": false,
      "cards": [
        {
          "type": "plugin-system-cpu",
          "title": "CPU Load",
          "refreshInterval": 5,
          "settings": { "sparkline": true }
        },
        {
          "type": "plugin-system-backup",
          "title": "Weekly Backups",
          "refreshInterval": 60,
          "settings": { "job_name": "full_system_backup" }
        }
      ]
    }
  ]
}

#### Plugin Manifest (`plugins/PluginName/manifest.json`)

```json
{
  "name": "homelab-docker",
  "version": "1.0.0",
  "title": "Docker Containers",
  "description": "Monitors and manages Docker containers.",
  "author": "Digital Lifestyle Creations",
  "className": "DockerPlugin",
  "permissions": ["exec_system_command", "docker_api"],
  "cards": [
    {
      "type": "container-list",
      "refreshInterval": 10,
      "endpoint": "/api/docker/containers"
    }
  ]
}

- `dashboard.json` defines tabs (i.e. multiple dashboards), panels and plugin order  
- `manifest.json` defines cards, titles, card states, refresh intervals, and optional endpoints  
- All layout and behavior is driven by JSON — no PHP required for card configuration

---

## 8. Out of Scope (For V1)

- Multi-user support with role-based access control (RBAC)  
- A full public plugin repository/store  
- Native mobile applications (progressive web app (PWA) functionality is in-scope)  
- Advanced alerting and notification system (e.g., Slack, Discord, email)  
- Historical data logging and complex graphing (focus is on current state)

---

## 9. Success Metrics

- **Adoption** — 1,000 active Docker Hub pulls within 6 months of release  
- **Usability** — Users can successfully add a new card from a plugin to their dashboard in under 3 minutes  
- **Performance** — 95% of dashboard pages render fully in under 2 seconds on average hardware  
- **Community** — At least 10 community-developed plugins within the first year

---

## 10. License

Convergenx is released under the **GNU General Public License v3.0 (GPLv3)**.  
You are free to use, modify, and distribute this software — as long as derivative works remain open and licensed under GPLv3.

---

## Appendix A: Glossary

- **Panel** — A collapsible container on the grid that holds one or more Cards  
- **Card** — An individual widget that displays information or provides controls. It is an instance of a Plugin  
- **Plugin** — A modular package that provides the backend logic and frontend view for a specific type of Card  
- **Grid** — The main layout area of the dashboard, based on a CSS grid system, defined by `gridColumns` and `gridRows`, allowing for responsive placement of Panels  
- **Parent/Child Frame** — An optional UI mode where external and internal links are loaded within a child frame, keeping the Convergenx navigation and "Home" button persistently visible in the parent frame

---

## Appendix B: Open Questions & Considerations

1. **Authentication Backend** — Simple file-based auth (`htpasswd`) or a more robust database-driven system? (SQLite now makes DB-driven simpler)  
2. **Real-time Tech** — Use Server-Sent Events (SSE) or WebSockets for live updates? (SSE may be simpler initially)  
3. **Security of `exec`** — How to securely implement the "Terminal" button and generic script plugins (`system-bash`, `system-python`) without introducing major vulnerabilities. A strict permission and sandboxing model is critical  
4. **Plugin Sandboxing** — How to prevent a poorly-written or malicious plugin from harming the host system  
5. **Parent/Child Frame Compatibility** — Some web applications may have headers (`X-Frame-Options`) that prevent them from being loaded in an iframe. How should Convergenx handle these cases? (e.g., open in a new tab with a warning)
