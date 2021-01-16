# Artisan 指令（Artisan Commands）

Lighthouse 提供了一些方便的 Artisan 指令。所有这些都在 `lighthouse` 开始命名。

## cache

编译 GraphQL 架构并缓存它。

    php artisan lighthouse:cache

## clear-cache

清除 GraphQL AST 的缓存。

    php artisan lighthouse:clear-cache

## directive

为自定义架构指令创建类。

    php artisan lighthouse:directive

Use the `--type`, `--field` and `--argument` options to create type, field and
argument directives, respectively. The command will then ask you which
interfaces the directive should implement and add the required method stubs and
imports for you.

## ide-helper

创建一个包含所有服务器端指令的模式。这将允许一些 IDEs 在您的 GraphQL-schema 中完成代码。

    php artisan lighthouse:ide-helper

这将创建以下文件：

-   `schema-directives.graphql`: 可在架构中使用的指令的架构定义

-   `programmatic-types.graphql`: 以编程方式注册的类型的架构定义（如果有

-   `_lighthouse_ide_helper.php`: 一些神奇 PHP 的类定义，比如 `TestResponse` 混合

使用当前版本的 Lighthouse 保持最新状态的好方法是将此脚本添加到您的 `composer.json` 中：

```json
"scripts": {
    ...
    "post-update-cmd": [
        "php artisan lighthouse:ide-helper"
    ],
```

## interface

为 GraphQL 接口类型创建一个类。

    php artisan lighthouse:interface <name>

## mutation

为 root Mutation 类型上的单个字段创建一个类。

    php artisan lighthouse:mutation <name>

使用 `--full` 选项可以包含不需要的 resolver 参数，如 `$context` 和 `$solveInfo` 。

## print-schema

编译最后的 GraphQL 模式并打印结果。

    php artisan lighthouse:print-schema

这可能非常有用，因为 root `.graphql` 文件不一定包含整个模式（schema）。模式（schema）导入、原生 PHP 类型和模式（schema）操作可能会影响最终模式（schema）。

Use the `-W` / `--write` option to output the schema to the default file storage
(usually `storage/app`) as `lighthouse-schema.graphql`.

您可以使用 `--json` 标志以 JSON 格式输出您的模式（schema）。

## query

为 root Query 类型的单个字段创建一个类。

    php artisan lighthouse:query <name>

使用 `--full` 选项可以包含不需要的 resolver 参数，如 `$context` 和 `$solveInfo` 。

## scalar

为 GraphQL scalar 类型创建一个类。

    php artisan lighthouse:scalar <name>

## subscription

为 root Subscription 类型上的单个字段创建类。

    php artisan lighthouse:subscription <name>

## union

为 GraphQL union 类型创建一个类。

    php artisan lighthouse:union <name>

## validate-schema

验证 GraphQL schema 定义。

    php artisan lighthouse:validate-schema
