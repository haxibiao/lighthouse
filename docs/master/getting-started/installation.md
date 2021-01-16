# 安装

本节包含的内容将教您如何安装 Lighthouse 到您的项目中去。

## 通过 composer 安装

```bash
composer require nuwave/lighthouse
```

## 发布默认的模式

Lighthouse 包含一个默认的模式，可以让您马上开始工作。使用下面的 artisan 命令发布它：

```bash
php artisan vendor:publish --tag=lighthouse-schema
```

## Lumen

在您的 `bootstrap/app.php` 文件中注册服务提供商：

```php
$app->register(\Nuwave\Lighthouse\LighthouseServiceProvider::class);
```

Lighthouse 提供的许多功能分散在多个服务提供商之间。由于 Lumen does 不支持自动发现，您必须根据您要使用的功能单独注册它们。查看 [Lighthouse 的 composer.json](https://github.com/nuwave/lighthouse/blob/master/composer.json)，`extra.laravel.providers` 部分包含默认的服务提供者。

## 开发环境配置

Lighthouse 封装了大量的底层操作。为了改善你的编辑体验，你可以[使用 artisan 命令](../api-reference/commands.md#ide-helper)生成一个定义文件：

```bash
php artisan lighthouse:ide-helper
```

我们推荐使用下面的插件 👇：

| IDE      | Plugin                                               |
| -------- | ---------------------------------------------------- |
| PhpStorm | https://plugins.jetbrains.com/plugin/8097-js-graphql |

## 安装 GraphQL 测试工具

为了充分的体现 GraphQL 它的惊人之处， 我们推荐安装 [GraphQL Playground](https://github.com/mll-lab/laravel-graphql-playground)

```bash
composer require mll-lab/laravel-graphql-playground
```

安装完成后，请尝试访问 `/graphql-playground` 。

安装 GraphQL Playground 之后，您可以将任何的 GraphQL 语句与 Lighthouse 结合使用。默认情况下，您只要在地址栏输入 `/graphql` 就可以看到它了。
