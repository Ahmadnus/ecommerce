<?php

return [

    /*
     * The disk on which to store added files and derived images by default. Choose
     * one or more of the disks you've configured in config/filesystems.php.
     */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * The maximum file size of an item in bytes.
     * Adding a larger file will result in an exception.
     */
    'max_file_size' => 1024 * 1024 * 10, // 10MB

    /*
     * This queue connection will be used to generate derived and responsive images.
     * Leave empty to use the default queue connection.
     */
    'queue_connection_name' => env('QUEUE_CONNECTION', 'sync'),

    /*
     * This queue will be used to generate derived and responsive images.
     * Leave empty to use the default queue.
     */
    'queue_name' => env('MEDIA_QUEUE', ''),

    /*
     * By default all conversions will be performed on a queue.
     *
     * IMPORTANT: This is already true in your app.
     * In the Product and Category models we used ->nonQueued() on each
     * conversion so they run synchronously during the HTTP request.
     * This is safe for low-to-medium traffic stores.
     *
     * For high-traffic production:
     *   1. Remove ->nonQueued() from every conversion in Product.php and Category.php
     *   2. Set QUEUE_CONVERSIONS_BY_DEFAULT=true in .env (already the default)
     *   3. Run: php artisan queue:work
     *
     * The images will still upload fine immediately; the WebP conversions
     * will appear a few seconds later once the queue worker processes them.
     */
    'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', true),

    /*
     * Should database transactions be run after database commits?
     */
    'queue_conversions_after_database_commit' => env('QUEUE_CONVERSIONS_AFTER_DB_COMMIT', true),

    /*
     * The fully qualified class name of the media model.
     */
    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
     * The fully qualified class name of the media observer.
     */
    'media_observer' => Spatie\MediaLibrary\MediaCollections\Models\Observers\MediaObserver::class,

    /*
     * When enabled, media collections will be serialised using the default
     * laravel model serialization behaviour.
     */
    'use_default_collection_serialization' => false,

    /*
     * The fully qualified class name of the model used for temporary uploads.
     */
    'temporary_upload_model' => Spatie\MediaLibraryPro\Models\TemporaryUpload::class,

    /*
     * When enabled, Media Library Pro will only process temporary uploads that were uploaded
     * in the same session.
     */
    'enable_temporary_uploads_session_affinity' => true,

    /*
     * When enabled, Media Library pro will generate thumbnails for uploaded file.
     */
    'generate_thumbnails_for_temporary_uploads' => true,

    /*
     * This is the class that is responsible for naming generated files.
     */
    'file_namer' => Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer::class,

    /*
     * The class that contains the strategy for determining a media file's path.
     */
    'path_generator' => Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,

    /*
     * The class that contains the strategy for determining how to remove files.
     */
    'file_remover_class' => Spatie\MediaLibrary\Support\FileRemover\DefaultFileRemover::class,

    /*
     * Here you can specify which path generator should be used for the given class.
     */
    'custom_path_generators' => [
        // Model::class => PathGenerator::class
    ],

    /*
     * When urls to files get generated, this class will be called.
     */
    'url_generator' => Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,

    /*
     * Moves media on updating to keep path consistent.
     */
    'moves_media_on_update' => false,

    /*
     * Whether to activate versioning when urls to files get generated.
     */
    'version_urls' => false,

    /*
     * The media library will try to optimize all converted images by removing
     * metadata and applying a little bit of compression. These are
     * the optimizers that will be used by default.
     *
     * These are CLI-tool-based optimizers. They only run if the binary is
     * installed on the server and are silently skipped when absent.
     *
     * Since compression is already handled aggressively in registerMediaConversions()
     * (WebP q55–q60, max 1600 px), these post-processors are intentionally
     * disabled below to avoid any dependency on external CLI binaries.
     *
     * If you later want to install these tools on a production Linux server
     * and re-enable them, add the appropriate entries back.
     */
    'image_optimizers' => [
        // Disabled — compression is fully handled by Spatie conversion settings
        // (WebP q55-q60 via GD). Enable individual optimizers here only if
        // the corresponding CLI binary is installed on your server.
    ],

    /*
     * These generators will be used to create an image of media files.
     */
    'image_generators' => [
        Spatie\MediaLibrary\Conversions\ImageGenerators\Image::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Webp::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Avif::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Svg::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Video::class,
    ],

    /*
     * The path where to store temporary files while performing image conversions.
     */
    'temporary_directory_path' => null,

    /*
     * The engine that should perform the image conversions.
     *
     * Set to 'gd' — the GD extension ships with PHP and requires no extra
     * installation. It fully supports JPEG, PNG, and WebP conversions, which
     * is everything this application needs.
     *
     * GD + WebP works correctly on PHP 8.x as long as PHP was compiled with
     * --with-webp (true for all standard PHP 8 distributions).
     *
     * Verify WebP support is available:
     *   php -r "var_dump(gd_info());"
     *   // Look for: "WebP Support" => bool(true)
     *
     * To explicitly lock this in your environment, add to .env:
     *   IMAGE_DRIVER=gd
     */
    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    /*
     * FFMPEG & FFProbe binaries paths.
     */
    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

    /*
     * The timeout (in seconds) for generating video thumbnails via FFMPEG.
     */
    'ffmpeg_timeout' => env('FFMPEG_TIMEOUT', 900),

    /*
     * The number of threads FFMPEG should use.
     */
    'ffmpeg_threads' => env('FFMPEG_THREADS', 0),

    /*
     * Here you can override the class names of the jobs used by this package.
     */
    'jobs' => [
        'perform_conversions'        => Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
        'generate_responsive_images' => Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob::class,
    ],

    /*
     * When using the addMediaFromUrl method you may want to replace the default downloader.
     */
    'media_downloader' => Spatie\MediaLibrary\Downloaders\DefaultDownloader::class,

    /*
     * SSL verification for addMediaFromUrl.
     */
    'media_downloader_ssl' => env('MEDIA_DOWNLOADER_SSL', true),

    /*
     * The default lifetime in minutes for temporary urls.
     */
    'temporary_url_default_lifetime' => env('MEDIA_TEMPORARY_URL_DEFAULT_LIFETIME', 5),

    'remote' => [
        'extra_headers' => [
            'CacheControl' => 'max-age=604800',
        ],
    ],

    'responsive_images' => [
        'width_calculator'        => Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator::class,
        'use_tiny_placeholders'   => true,
        'tiny_placeholder_generator' => Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
    ],

    /*
     * When enabling this option, a route will be registered that will enable
     * the Media Library Pro Vue and React components to move uploaded files
     * in a S3 bucket to their right place.
     */
    'enable_vapor_uploads' => env('ENABLE_MEDIA_LIBRARY_VAPOR_UPLOADS', false),

    /*
     * The default loading attribute value for img tags.
     */
    'default_loading_attribute_value' => null,

    /*
     * Prefix for storing all media.
     */
    'prefix' => env('MEDIA_PREFIX', ''),

    /*
     * Force lazy loading of media.
     */
    'force_lazy_loading' => env('FORCE_MEDIA_LIBRARY_LAZY_LOADING', true),
];