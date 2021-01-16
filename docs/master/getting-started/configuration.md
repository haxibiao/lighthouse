# 配置文件

Lighthouse 默认的配置文件一切都刚刚好，您可以直接使用。
如果您需要更改默认配置，则需要先生成配置文件。

```bash
php artisan vendor:publish --tag=lighthouse-config
```

配置文件将被在 `config/lighthouse.php`.

## CORS

可以从多个客户端使用 GraphQL API，这些客户端可能驻留在与服务器相同的域中，也可能不驻留在相同的域中。确保在 `config/cors.php` 中为您的 GraphQL endpoint 启用了 [跨域资源共享(CORS)](https://laravel.com/docs/routing#cors)：

```diff
return [
-   'paths' => ['api/*', 'sanctum/csrf-cookie'],
+   'paths' => ['api/*', 'graphql', 'sanctum/csrf-cookie'],
    ...
];
```

> 从版本 7 开始，CORS 内置在 Laravel 中，以供以前的版本使用 https://github.com/fruitcake/laravel-cors
