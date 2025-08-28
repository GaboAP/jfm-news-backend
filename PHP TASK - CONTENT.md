# PHP Backend Challenge – Modular CMS Component

## Objective
The goal of this challenge is to assess your ability to architect and implement a modular PHP backend component using modern development principles. You may use Laravel and Artisan commands, but you should build everything with strong separation of concerns and a focus on extensibility. Avoid Eloquent – build your own abstractions for data access and business logic.

## Context
You are contributing to the future of a custom CMS. The team is about to redesign the **Page Editor**, with a major focus on flexible and extensible **Media Management**. This includes support for not just images, but also videos, audio, graphs, and other media types.

You’ll simulate the design of this new media handling capability in a standalone Laravel application. Use this as an opportunity to demonstrate your thinking and ability to build scalable code – similar in spirit to what you’d do in a Kodus project.

## Functional Requirements

### Part 1: Media Module
Implement a basic Media Manager component that can:

1. **Store media entries**
   - `uuid` (unique ID)
   - `type` (e.g., image, video, audio, graph, file)
   - `title`
   - `description`
   - `source_url`
   - `uploaded_at` (datetime)
   - `metadata` (optional, structured key-value pairs)

2. **Search media** by type or title.
3. **Support validation** for known types (at least 5 types).
4. **Support metadata enrichment** via a `MediaMetadataService`.
5. **Use Artisan commands** to simulate media upload, metadata enrichment, and search.

### Part 2: Article Integration (Partial)
Simulate how articles might consume media:

- Define a simple Article model (no DB required):
  - `article_uuid`
  - `headline`
  - `content`
  - `image_uuid_list`
  - `media_attachments` (support multiple types)

- Build a **MediaResolverService** that resolves an article’s media uuids to real media entries.

- Provide a command like `article:show` that displays article content and resolved media.

## Technical Requirements
- Laravel 10+ or similar standard
- Use your own interfaces and repositories (do not use Eloquent)
- Use dependency injection for all service classes
- Use PSR standards and service container bindings
- Include unit tests for at least:
  - Media storage and validation
  - Metadata enrichment logic
  - Media resolution from article data

## Optional / Bonus
- Serialization format for media metadata and article content (your own design)
- Memory-backed repositories (instead of DB)
- Configurable media validation logic (via config or strategy pattern)

## Realistic API Example (inspiration)
Some typical article fields you may mock or simulate:
- `article_uuid`
- `version_uuid`
- `created`, `published`, `modified`, `updated`
- `headline`, `lead`, `byline`, `content`
- `image_uuid_list`
- `classifications` (map field to name "terms")
- `media_attachments` (extend this field)

## Deliverables
- GitHub repo or zip file containing:
  - Laravel project with code and tests
  - README with instructions and assumptions

## Evaluation Criteria
- Clear architecture and extensibility
- Correct use of services, interfaces, and patterns
- Test coverage and separation of responsibilities
- Simulated integration of content and media data

---

There is no fixed deadline, but please aim to deliver within a 1-2 weeks. This is a realistic simulation of a real-world project. Reach out if any assumptions need clarification.

Good luck!
