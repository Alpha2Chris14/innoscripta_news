# 📰 Innoscripta News Aggregator

A Laravel-based **news aggregation system** that periodically fetches and stores articles from multiple external news providers — **NewsAPI**, **The Guardian**, and **New York Times**.  
The application uses **jobs**, **service providers**, and **scheduler commands** to automate and structure the data fetching process. It also includes **unit and feature tests** to ensure reliability.

---

## 🚀 Features

-   Fetches news from:
    -   🗞️ [NewsAPI](https://newsapi.org/)
    -   📰 [The Guardian Open Platform](https://open-platform.theguardian.com/)
    -   🗽 [New York Times API](https://developer.nytimes.com/)
-   Stores fetched articles in your local database
-   Structured using **Service Pattern** and **Job Dispatching**
-   Runs automatically via Laravel **Scheduler**
-   Includes **manual Artisan commands** for testing and fetching news
-   Implements **Repository & Interface abstraction** for provider extensibility
-   Includes **unit and feature tests**, using **mocked HTTP clients** for reliable testing

---

## 🧩 Architecture Overview

```
app/
├── Contracts/
│   └── NewsProviderInterface.php
├── Jobs/
│   └── FetchArticlesJob.php
├── Models/
│   ├── Article.php
│   └── Source.php
├── Services/
│   └── Providers/
│       ├── BaseNewsProvider.php
│       ├── NewsApiProvider.php
│       ├── GuardianProvider.php
│       └── NytProvider.php
├── Console/Commands/
│   └── FetchArticlesCommand.php
└── Http/Controllers/
    ├── ArticleController.php
    └── SourceController.php

tests/
├── Unit/
│   ├── ExampleTest.php
│   ├── Providers/
│   │   ├── BaseProviderTest.php
│   │   ├── GuardianProviderTest.php
│   │   ├── NewsApiProviderTest.php
│   │   └── NytProviderTest.php
├── Feature/
│   ├── ExampleTest.php
│   ├── Commands/
│   │   └── FetchArticlesCommandTest.php
│   └── Jobs/
│       └── FetchArticlesJobTest.php

routes/
├── console.php # Scheduler / Artisan command registration
└── api.php     # API routes for articles & sources
```

---

## ⚙️ Requirements

| Requirement | Version     |
| ----------- | ----------- |
| PHP         | ^8.2        |
| Composer    | ^2.x        |
| Laravel     | ^12.x       |
| GuzzleHTTP  | ^7.x        |
| MySQL       | ^5.7 / ^8.x |

---

## 🔑 API Keys

You’ll need valid API keys for all 3 providers:

| Provider       | Environment Variable | Example                              |
| -------------- | -------------------- | ------------------------------------ |
| NewsAPI        | `NEWS_API_KEY`       | `NEWS_API_KEY=your_news_api_key`     |
| The Guardian   | `GUARDIAN_API_KEY`   | `GUARDIAN_API_KEY=your_guardian_key` |
| New York Times | `NYT_KEY`            | `NYT_KEY=your_nyt_key`               |

Register and get keys from:

-   [https://newsapi.org/](https://newsapi.org/)
-   [https://open-platform.theguardian.com/](https://open-platform.theguardian.com/)
-   [https://developer.nytimes.com/](https://developer.nytimes.com/)

---

## 🛠️ Installation & Setup

Follow these steps to get started:

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/alpha2chris14/innoscripta_news.git
cd innoscripta_news
```

### 2️⃣ Install Dependencies

```bash
composer install
```

### 3️⃣ Environment Setup

Duplicate `.env.example` to `.env` and configure your database and API keys:

```bash
cp .env.example .env
php artisan key:generate
```

Then update `.env`:

```env
DB_CONNECTION=mysql
DB_DATABASE=innoscripta_news
DB_USERNAME=root
DB_PASSWORD=

NEWS_API_KEY=your_newsapi_key
GUARDIAN_API_KEY=your_guardian_key
NYT_KEY=your_nyt_key
```

### 4️⃣ Run Migrations

```bash
php artisan migrate
```

---

## 🧪 Testing

Unit and feature tests ensure providers, jobs, and commands work as expected. Tests include:

-   Unit tests for each provider, including `BaseProviderTest`
-   Feature tests for job dispatching and command execution
-   Mocked HTTP clients to simulate API responses

Run all tests with:

```bash
php artisan test
```

---

## 🕓 Automating with Scheduler

The scheduler automatically dispatches the news fetching job periodically.

### Run Once

To trigger immediately:

```bash
php artisan schedule:run
```

### Continuous Scheduler Worker

```bash
php artisan schedule:work
```

---

## 🧰 Job Queue

Each provider fetch runs as a queued job via:

```bash
php artisan queue:work
```

Ensure your `.env` has:

```env
QUEUE_CONNECTION=database
```

Then create the jobs table if needed:

```bash
php artisan queue:table
php artisan migrate
```

---

## 🧱 Provider Summary

### 🗞 NewsAPI

Fetches articles using `/v2/top-headlines`.

### 📰 The Guardian

Fetches stories using `/search` with filters for section, type, and fields.

### 🗽 The New York Times

Fetches stories using `/svc/topstories/v2/{section}.json`.

Standardized data format:

```php
[
  'title' => 'Sample News Title',
  'description' => 'Short description...',
  'url' => 'https://news-source.com/article/123',
  'author' => 'John Doe',
  'published_at' => '2025-11-09T10:00:00Z',
  'image_url' => 'https://image.url',
  'category' => 'World',
  'language' => 'en',
]
```

---

## 🧩 Troubleshooting

| Issue                                    | Possible Cause                     | Solution                                                 |
| ---------------------------------------- | ---------------------------------- | -------------------------------------------------------- |
| `No scheduled commands are ready to run` | Scheduler interval not yet reached | Use `php artisan schedule:work` for real-time scheduling |
| `Client error: 401`                      | Invalid API key                    | Check `.env` API keys                                    |
| `No news fetched`                        | Rate limit or incorrect endpoints  | Run one provider at a time for debugging                 |

---

## 🧾 Example Data Flow

1. Scheduler triggers `FetchArticlesJob`
2. `FetchArticlesJob` calls `NewsService`
3. `NewsService` loads all provider classes (`NewsApiProvider`, `GuardianProvider`, `NytProvider`)
4. Each provider fetches data from its external API
5. News items are normalized and saved in the database
6. Duplicates (by URL or external_id) are skipped

---

## 🧑‍💻 Commands Reference

| Command                     | Description                      |
| --------------------------- | -------------------------------- |
| `php artisan news:fetch`    | Fetch all news manually          |
| `php artisan schedule:run`  | Trigger scheduled tasks manually |
| `php artisan schedule:work` | Continuously run the scheduler   |
| `php artisan queue:work`    | Run queued jobs continuously     |

---

## 📦 Future Improvements

-   Add caching for API responses
-   Integrate Horizon for job monitoring

---

## 👨‍💻 Author

**Christian Onyeka**  
Laravel Developer | API Engineer  
GitHub: [@alpha2chris14](https://github.com/alpha2chris14)
