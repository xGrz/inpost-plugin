# Webhook

In InPost app you should provide webhook url.
Local endpoint is configured in `inpost.webhook_url` config.
You can easily check this endpoint url with `php artisan inpost:config` or
`Xgrz\InPost\Config\InPostConfig::webhookFullUrl()` method.

Webhook is protected by `Xgrz\InPost\Http\Middleware\InPostWebhookMiddleware` (accepts only allowed IPs).
Request body is validated for proper organization id and filters only allowed events and keys.
This is considered as secure.

If you are security freek you can modify webhook endpoint in a config file. 
