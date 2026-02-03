# GAIN Drupal Tech Test

## Overview

This exercise is a short, Drupal-focused tech test.

You will:
- Run a small Drupal 11 site locally.
- Fix one existing issue in a custom module.
- Implement one small feature.
- Open a pull request describing your changes.

**Estimated time:** 60–90 minutes.

We are not expecting polish or perfection; we want to see how you approach real-world Drupal support and enhancement work.

## Prerequisites

You'll need:
- PHP 8.3+
- Composer 2
- Git
- Optional: DDEV or Docker (if you prefer containers)
- Optional: mkcert (for trusted SSL certificates with DDEV)

You may use any editor/IDE and any online documentation (Drupal.org, etc.).

## Setup

### 1. Create your repository

**IMPORTANT:** Use this template to create your own private repository:

1. Click "Use this template" → "Create a new repository"
2. Make it **private**
3. Name it `gain-drupal-tech-test-[your-name]`
4. Clone your new repository:
   ```bash
   git clone <YOUR_REPO_URL> gain-drupal-tech-test
   cd gain-drupal-tech-test
   ```
5. **Add the reviewer as a collaborator** to your private repository

### 2. Environment Setup

#### Quick Setup (Recommended)

Run the automated setup script:

```bash
./setup.sh
```

This will detect your environment (DDEV or Docker) and set everything up automatically.

**DDEV URLs:**
- Site: https://gain-drupal-tech-test.ddev.site:8443
- Admin login: admin / admin

**Docker URLs:**
- Site: http://localhost:8080
- Complete installation via web interface

#### Manual Setup

#### 2. Choose your environment

**Option A: DDEV (Recommended)**

```bash
ddev start
ddev composer install
ddev drush site:install standard --db-url=mysql://db:db@db/db --site-name="GAIN Drupal Tech Test" --account-name=admin --account-pass=admin -y
ddev drush en gain_drupal_tech_test -y
ddev drush cr
```

**Option B: Docker Compose**

```bash
composer install
docker-compose up -d
# Complete Drupal installation via browser at http://localhost:8080
# Database: drupal / drupal @ db:3306
```

**Option C: Local Environment**

```bash
composer install
# Set up web server pointing to web/
# Create database and configure settings.php
# Run Drupal installation
# Enable gain_drupal_tech_test module
```

#### 3. Confirm setup

Confirm the site runs and that the `gain_drupal_tech_test` routes/blocks appear as described in the tasks below.

## Tasks

Please complete both Task 1 and Task 2.

### Task 1 – Bug fix / support ticket

There is an intentionally broken feature in the `gain_drupal_tech_test` module:

A block plugin (e.g. `RecentArticlesBlock`) is meant to display the 5 most recent published nodes of content type `article`.

Currently, it does not behave as expected (you might see wrong ordering, unpublished content, or too many/few items).

**Your job:**

Find and fix the bug so the block:
- Only shows published article nodes.
- Is ordered by newest first.
- Shows the configured number of items (default 5).

**Add a small improvement:**
- Make the number of items configurable via a block configuration form (or a simple config setting) so an editor can choose how many items to show.

You do not need to make it perfect – just implement a clean, standard Drupal solution.

### Task 2 – Small custom feature (JSON endpoint)

We want a simple JSON feed for support tooling.

**Requirements:**

Add a route and controller in `gain_drupal_tech_test` that returns JSON at:
`/latest-articles`

The endpoint should return a JSON array of the latest 10 article nodes with fields:
- Node ID
- Title
- Authored date
- URL (canonical)

**Implementation notes:**
- Use dependency injection to access services (e.g. entity storage, URL generator).
- Ensure the route is only accessible to authenticated users (add a simple permission and check it in the routing YAML).
- Follow Drupal coding standards and structure (services, controller, routing, etc.).

## Optional (nice to have, not required)

If you have time, you may optionally:
- Add a simple config form (under Configuration » GAIN Drupal Tech Test) to set the default "number of items" used by the block and JSON endpoint.
- Add a basic test (kernel or functional) to cover the JSON endpoint or block logic.

These are bonus items; if you are short on time, focus on Task 1 and Task 2 first.

## What we look at

When reviewing your PR, we'll mainly look at:
- **Correctness** – does the block and endpoint behave as requested?
- **Drupal best practices** – use of services, dependency injection, routing, permissions, configuration.
- **Code quality** – structure, naming, readability, basic comments where helpful.
- **Support mindset** – does your change clearly solve the "ticket" and avoid obvious regressions?
- **Git & communication** – clear commit messages and a short PR description.

## How to submit

1. **Create a new branch** from main:
   ```bash
   git checkout -b feature/your-name-solution
   ```

2. **Commit your changes** with clear messages:
   ```bash
   git commit -am "Fix recent articles block and add JSON endpoint"
   ```

3. **Push your branch** to your repository:
   ```bash
   git push origin feature/your-name-solution
   ```

4. **Open a Pull Request** in your own repository:
   - **Title:** Tech test solution – Your Name
   - **Description:**
     - Briefly describe what you changed for Task 1 and Task 2.
     - Note anything you'd do differently with more time.

5. **Notify the reviewer** that your PR is ready for review.

That's it—thank you for taking the time to complete this exercise.