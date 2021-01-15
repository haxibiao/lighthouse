const versioning = require("./lib/versioning.js");

module.exports = {
    title: "Lighthouse",
    description: "A framework for serving GraphQL from Laravel",
    head: [
        [
            "link",
            {
                rel: "icon",
                href: "/favicon.png",
            },
        ],
        [
            "link",
            {
                rel: "stylesheet",
                type: "text/css",
                href:
                    "https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,800,800i,900,900i",
            },
        ],
        [
            "link",
            {
                rel: "stylesheet",
                type: "text/css",
                href:
                    "https://fonts.googleapis.com/css?family=Miriam+Libre:400,700",
            },
        ],
    ],
    theme: "default-prefers-color-scheme",
    themeConfig: {
        defaultTheme: "light",
        logo: "/logo.svg",
        editLinks: true, //  "Edit this page" at the bottom of each page
        repo: "nuwave/lighthouse", //  Github repo
        docsDir: "docs/", //  Github repo docs folder
        versions: {
            latest: versioning.versions.latest,
            selected: versioning.versions.latest,
            all: versioning.versions.all,
        },
        nav: [
            {
                text: "Docs",
                items: versioning.linksFor("getting-started/installation.md"), // TODO create custom component
            },
            {
                text: "Tutorial",
                link: "/tutorial/",
            },
            {
                text: "Resources",
                link: "/resources/",
            },
            {
                text: "Users",
                link: "/users/",
            },
            {
                text: "Changelog",
                link:
                    "https://github.com/nuwave/lighthouse/blob/master/CHANGELOG.md",
            },
            {
                text: "Upgrade Guide",
                link:
                    "https://github.com/nuwave/lighthouse/blob/master/UPGRADE.md",
            },
        ],
        sidebar: versioning.sidebars,
    },
    locales: {
        "/en/": {
            lang: "en-US",
            title: "Lighthouse",
            description: "A framework for serving GraphQL from Laravel",
            selectText: "Languages",
            label: "English",
            ariaLabel: "Languages",
            editLinkText: "Edit this page on GitHub",
        },
        "/": {
            lang: "zh-CN",
            title: "Lighthouse 中文网",
            description:
                "适用于 Laravel 的 GraphQL 服务器支持，PHP 实现 GraphQL 服务，Lighthouse 中文网，提供 Lighthouse-php 中文文档",
            selectText: "Languages",
            label: "简体中文",
            ariaLabel: "Languages",
            editLinkText: "在 GitHub 上编辑此页",
        },
    },
    plugins: [
        ["@vuepress/back-to-top", true],
        ["@vuepress/medium-zoom", true],
        [
            "@vuepress/search",
            {
                searchMaxSuggestions: 10,
                // Only search the latest version, e.g. 4.3, otherwise many duplicates will show up
                test: `/${versioning.versions.latest.replace(".", "\\.")}/`,
            },
        ],
    ],
};
