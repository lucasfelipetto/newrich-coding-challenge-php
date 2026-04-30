# Coding Challenge

## Overview

You are given a PHP snippet that fetches data from an API and returns formatted results. Your task is to implement this functionality in a clean and well-structured way, write tests for it, **and build a frontend UI to display the data**.

This is a **fullstack challenge**. The frontend can live in the same repository as the backend (monorepo), organized however you see fit.

---

## Environment

- You can use Composer to manage PHP dependencies
- Tests should run with `composer test` (Pest PHP framework is recommended, but not required)
- An API server can be run locally via Docker
- You may include any setup instructions in your README
- For the frontend, you may use any framework or library you prefer (React, Vue, vanilla JS, etc.)

---

## Part 1 — Backend: Refactor the API Fetcher

Here is the code snippet to implement:

```php
<?php

class ApiFetcher
{
    private $url = 'http://localhost:8000';
    private $logFile = '/tmp/api_log.txt';

    public function getData()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (!$response) {
            $this->log('Error fetching data: ' . curl_error($ch));
            return [];
        }

        $decoded = json_decode($response);
        curl_close($ch);

        if (!$decoded) {
            $this->log('Error decoding JSON');
            return [];
        }

        $results = [];
        foreach ($decoded as $item) {
            if ($item->active) {
                $results[] = strtoupper($item->name);
            }
        }

        return $results;
    }

    private function log($message)
    {
        file_put_contents($this->logFile, date('c') . ' ' . $message . PHP_EOL, FILE_APPEND);
    }
}

$fetcher = new ApiFetcher();
$data = $fetcher->getData();
print_r($data);
```

### Your Task (Backend)

1. Implement this functionality in a structured way (e.g., using classes, services, etc.)
2. Organize your code as you see fit
3. Write tests to validate your implementation
4. Extend the API endpoint to support the filtering and sorting parameters described in Part 2 below

---

## Part 2 — Frontend: Data Listing UI

Build a web UI that consumes the API and displays the data as an interactive listing. The UI must live in the same repository (a `ui/` or `frontend/` folder is fine).

### Required Features

The listing **must** include all of the following:

- **Display** the list of items returned by the API (name, active status, and any other relevant fields)
- **Filtering** — at minimum, allow the user to filter by:
  - Active / Inactive / All (toggle or dropdown)
  - A text search on the item name
- **Column sorting** — clicking a column header should sort the table by that column
- **Sort direction toggle** — clicking an already-sorted column header should reverse the sort order (ascending ↔ descending), with a clear visual indicator (e.g., an arrow icon)
- **Responsive design** — the UI must work correctly on both desktop and mobile screen sizes

### Notes

- The frontend must be built with **React**
- You are free to be creative with the design — clean and simple is perfectly fine
- Filtering and sorting may be implemented client-side or server-side (if server-side, update the API accordingly)
- No authentication is required

---

## Part 3 — Submission Requirements

### README

Your README must include:

1. **How to run** — clear step-by-step instructions to start both the API and the UI locally (Docker commands, `npm install`, etc.)
2. **Assumptions** — any assumptions you made about the data or requirements
3. **Design decisions** — why you structured things the way you did
4. **Trade-offs** — what you would do differently with more time

---

## Constraints

- PHP 8.2+ for the backend
- You are free to structure the project (monorepo) however you prefer
- You may add any dependencies you find appropriate — please justify them
- As a guideline, try not to spend more than **60 minutes** on the full challenge

---

## Evaluation Criteria

Your solution will be evaluated based on:

| Area | What we look at |
|---|---|
| **Correctness** | Does the backend work? Does the UI accurately reflect the data? |
| **Code quality** | Structure, readability, separation of concerns |
| **Testing** | Coverage, approach, edge cases |
| **UI/UX** | Usability, responsiveness, interaction quality |
| **Communication** | Clarity of README |
