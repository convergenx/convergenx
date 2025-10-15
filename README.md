# Convergenx

**Convergenx** (pronounced "Convergence" and abbreviated "Cx") is a modular, plugin-driven server dashboard built for clarity, control, and flexibility. It runs as a lightweight PHP web app with a grid-based layout, collapsible panels, and JSON-configured cards—giving you comprehensive visibility into your media workflows, system metrics, containers, backups, and automation triggers.

Whether you're managing containers, websites, self-hosted services, media downloads, inspecting logs, or triggering scripts, Convergenx lets you orchestrate it all from a single, expressive interface that you design and create, with your exact requirements, specifications, and workflow in mind.

---

## ✨ Key Features

- 🧩 **Plugin-First Architecture**: Every piece of functionality is a plugin. Mix and match for system metrics, container management, media stats, security, backups, and much more. Need something custom/specific? Create a plugin - easy-peasey.
- 🗂️ **Grid-Based Layout**: A fully customizable interface via `dashboard.json`, featuring multiple tabs, collapsible panels, and a drag-and-drop card system with multi-state cards.
- 📄 **JSON-Configured Cards**: Define a card's title, icon, refresh interval, multiple states (configurations with various sizes and levels of detail) and even the shell command it runs—all through JSON, no PHP required.
- 🐳 **Container Management**: View Docker container status, uptime, resource usage, and execute commands like restart.
- 🔐 **Security Overview**: Monitor active sessions, firewall status, and security logs (e.g., fail2ban) from a centralized view.
- 💾 **Backup Orchestration**: Monitor backup job status and initiate snapshots or restores directly from the dashboard.
- 📥 **Media Workflow Integration**: Live status and control for a variety of download clients and media servers.
- 📲 **Automation Triggers**: Execute scripts securely via webhook or SSH triggers with live feedback.
- 🎨 **Themeable Interface**: Support for light and dark modes with clean, functional visuals.

---

## 🧠 Philosophy

Convergenx is built for homelabbers and server administrators who value a flexible and automatable workflow. It emphasizes a simple, JSON-driven configuration that puts you in full control of your server environment.

---

## 🚀 Getting Started

### Prerequisites
- A server with **Docker** and **Docker Compose** installed.

### Installation & Deployment
1.  Clone the repository:
    ~~~bash
    git clone https://github.com/digital-lifestyle-creations/Convergenx.git
    cd Convergenx
    ~~~
2.  Copy the example environment file and configure it as needed:
    ~~~bash
    cp .env.example .env
    ~~~
3.  Launch the application stack:
    ~~~bash
    docker-compose up --build -d
    ~~~
4.  Open your browser and navigate to `http://your-server-hostname:8262` to access your dashboard.

---

## 🗂️  Directory Structure

~~~
.
├── data
│   ├── convergenx
│   │   ├── api
│   │   ├── assets
│   │   │   ├── css
│   │   │   ├── icons
│   │   │   └── js
│   │   ├── config
│   │   ├── index.php
│   │   ├── lib
│   │   ├── plugins
│   │   ├── start.sh
│   │   └── themes
│   │       ├── darkmode
│   │       └── default
│   ├── nginx
│   │   └── nginx.conf
│   └── sqlite
│       ├── adminer
│       └── db
├── docker-compose.yml
├── Dockerfile
├── LICENSE
├── PRD.md
└── README.md
~~~

---

## 🧩 Plugin System

The plugin system is the core of Convergenx's extensibility. Each plugin is self-contained in its own directory under `/data/plugins/`.

### Plugin Structure
A typical plugin has the following structure:
~~~
/data/plugins/DockerStatus/
├── manifest.json   # Defines the plugin and its cards
└── list_containers.sh  # Script for data retrieval
~~~

### Example Plugin Manifest
The `manifest.json` file defines the cards a plugin provides and how they behave.
~~~json
{
  "plugin": "DockerStatus",
  "version": "1.0.0",
  "description": "Shows status of running Docker containers",
  "cards": [
    {
      "id": "docker-containers",
      "title": "Docker Containers",
      "icon": "🐳",
      "refresh_interval": 60,
      "source": "bash",
      "command": "./list_containers.sh",
      "output_format": "text",
      "controls": [
        {
          "label": "Restart All",
          "icon": "🔄",
          "action": "./restart_all.sh",
          "confirm": true
        }
      ]
    }
  ]
}
~~~

---

## 📄 Configuration-Driven Dashboard

Your entire dashboard layout is defined in a single JSON file, making it easy to version, backup, and replicate your setup.

### `dashboard.json` Overview
~~~json
{
  "dashboard": {
    "title": "My Homelab",
    "theme": "dark",
    "gridColumns": 12,
    "gridRows": 8
  },
  "panels": [
    {
      "id": "system_health",
      "title": "System Health",
      "width": 6,
      "height": 2,
      "collapsed": false,
      "cards": [
        {
          "type": "system-cpu",
          "refreshInterval": 5
        },
        {
          "type": "system-memory",
          "refreshInterval": 10
        }
      ]
    }
  ]
}
~~~

---

## 🤝 Contributing

Contributions are welcome! If you have an idea for a new plugin or an improvement to the core, please feel free to fork the repository and submit a pull request. For major changes, please open an issue first to discuss what you would like to change.

1.  Fork the Project
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the Branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

---

## 📜 License

Convergenx is released under the **GNU General Public License v3.0 (GPLv3)**.

You are free to use, modify, and distribute this software. Derivative works must remain open source and licensed under the same GPLv3 terms. See the `LICENSE` file for more details.

---
