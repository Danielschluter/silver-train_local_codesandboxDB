Using this repo in CodeSandbox (Container/Docker environment)

Overview
- This project is a PHP app (Nginx + PHP-FPM) with a database service defined in `docker-compose.yml`.
- CodeSandbox can run it using a "Container" (Docker) sandbox that supports `docker-compose`.

Quick steps to import and run
1. Commit and push this repository to GitHub (private or public).
2. In CodeSandbox, choose "Create Sandbox" → "Import from GitHub" and paste the repo URL.
3. Select the Container/Docker option when prompted (CodeSandbox will detect the Dockerfiles / docker-compose.yml).
4. In the CodeSandbox UI set environment variables (Secrets) for the sandbox. Do NOT commit secrets to the repo. Required variables for Azure SQL usage:
   - `DB_DRIVER` (set to `sqlsrv` or `pgsql`)
   - `DB_HOST` (e.g. yourserver.database.windows.net)
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
   - `DB_ENCRYPT` (optional, default `1`)
   - `DB_TRUST_SERVER_CERT` (optional, default `0`)

Notes about ports and preview
- The app serves HTTP via Nginx on port `80` inside the container; CodeSandbox maps that to a preview URL. The repo's `docker-compose.yml` exposes port `8080` locally; CodeSandbox will publish a URL you can open from the UI.

Security and secrets
- Never commit database passwords or keys. Use the CodeSandbox environment/secret UI or a `.env` file that you keep locally (and add `.env` to `.gitignore`).

If you hit Azure firewall issues
- When connecting to Azure SQL from CodeSandbox, add the sandbox's public egress IP to the Azure SQL Server firewall (or use a private endpoint/VNet). You can determine the sandbox IP by running `curl https://ifconfig.me` inside the container.

Local testing alternative
- If you prefer to run locally instead of CodeSandbox, use Docker Compose:
```bash
docker compose up --build
# open http://localhost:8080
```

If you want, I can add a small `.codesandbox` configuration or a short example `.env.example` with placeholders — tell me which you'd prefer.
