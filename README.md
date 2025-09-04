# Modular CMS Backend Challenge

This project simulates a modular PHP backend component for a custom CMS using Laravel 12.  
It focuses on Media Management and Article Integration with clear separation of concerns, PSR standards, and testability.

------------------------------------

## Requirements

- PHP 8.2+
- Composer
- Laravel 12

------------------------------------

## Project Structure (key parts)
app/
    Domain/
        Media/ # Media entity, service, contracts, DTOs
        Article/ # Article entity, attachments, resolver, contracts
        Shared/ # Shared value objects (UUIDs, URL)
    Infrastructure/
        Media/Repository/ # InMemory + File-based media repositories
        Article/Repository/ # InMemory + File-based article repositories

Providers/ # Service providers for DI bindings
Console/Commands/ # Artisan commands (media + article)
config/
    article.php # Article repository config (memory | file)
    media.php # Media repository config (memory | file)
tests/Unit/ # Unit tests for media and article

------------------------------------

## Installation

Once The project is cloned, run composer install and wait for the files to be generated.
Afterwards, copy the .env.example as is and remove the .env, nothing should be changed unless wanting to move from memory repos to file repos

------------------------------------

## Configuration

Repositories can be memory-backed (default, non-persistant) or file-backed for demo persistence.

MEDIA_REPOSITORY=memory   # memory | file
MEDIA_FILE_DISK=local     # only used when MEDIA_REPOSITORY=file
MEDIA_FILE_PATH=media_store.json #only used when MEDIA_REPOSITORY=file

ARTICLE_REPOSITORY=memory # memory | file
ARTICLE_FILE_DISK=local   # only used when ARTICLE_REPOSITORY=file
ARTICLE_FILE_PATH=articles_store.json # only used when ARTICLE_REPOSITORY=file

------------------------------------

## Notes:

memory: Data exists only for the lifetime of a process (each command/request). This satisfies the “memory-backed” requirement.

file: Data is serialized to JSON under storage/app/ to demonstrate a simple persistence strategy and serialization format.

If you switch repository types, rebuild autoload and clear config:

composer dump-autoload -o
php artisan config:clear

## Commands
Here are some examples for using the commands, more details about the flags can be found under console/commands

# Media
# Upload media (type title description source_url)
    php artisan media:upload image "Cover" "Front Cover" https://example.com/cover.jpg
    php artisan media:upload image "Cover" "Front Cover" https://example.com/cover.jpg --enrich

# Search media
    php artisan media:search --type=image --title=cover
    php artisan media:search --type=image --title=cover --with-meta
    php artisan media:search --type=image --title=cover --json

# Enrich media (by UUID or all)
    php artisan media:enrich <MEDIA_UUID>
    php artisan media:enrich --all

# Article
# Create article (inline content)
    php artisan article:create "My Headline" --content="Hello world"

# Create article from Markdown file (placed a file at docs/sample.md)
    php artisan article:create "Markdown Article" --content-file=docs/sample.md

# Add media references
    php artisan article:create "Photo Story" --image=UUID1 --image=UUID2
    php artisan article:create "Video Piece" --attach=UUID3:video:label (repeatable)

# Show article
    php artisan article:show <ARTICLE_UUID>
    php artisan article:show <ARTICLE_UUID> --resolve
    php artisan article:show <ARTICLE_UUID> --resolve --json

------------------------------------

## Testing

Unit tests cover:

    - Media storage and validation

    - Metadata enrichment logic

    - Media resolution from article data

    - Media entity immutability

Simply use "php artisan test" to run all tests

------------------------------------

## Assumptions

- No Eloquent or database is used. All domain logic is implemented with custom interfaces and repositories.

- In-memory repositories are the default to match “memory-backed” behavior. Data does not persist between commands.

- File-backed repositories are provided to demonstrate serialization and to ease manual testing across commands.

- Metadata enrichment is handled by BasicMetadataService (e.g., source_host, file extension, checksum).

- MediaResolverService resolves image_uuid_list and media_attachments to actual Media entities using the repository.

- Service container bindings and PSR-4 autoloading are used. Services are injected via constructors.
