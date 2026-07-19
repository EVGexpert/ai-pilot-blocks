# AI Pilot Block Authoring Rules — API v3

This document accompanies the machine-readable `rules/block-authoring-v3.json` file.

## Required structure

Each block lives in its own folder and contains `block.json`, `ai.json`, `index.js`, `index.asset.php`, and `render.php`. `block.json` is the canonical registration source and must use `apiVersion: 3`.

## Content model

Agent-editable copy is stored in declared attributes or InnerBlocks. Important public content is rendered on the server. Arbitrary HTML, inline scripts, event handlers, arbitrary CSS values, and undeclared block names are not allowed.

## Metadata

Declare name, version, title, category, description, textdomain, attributes, supports and assets. Use `parent`, `ancestor`, and `allowedBlocks` to constrain nesting. Use enums for layout, tone, spacing and variants. Add `keywords`, `example`, styles, variations, context and selectors when relevant.

## API v3 and iframe editor

Use `useBlockProps()` in the editor and `get_block_wrapper_attributes()` on the server. Do not depend on the parent wp-admin DOM. Register every dependency in `index.asset.php`; provide editor styles explicitly.


## Общие дизайн-токены и визуальные варианты

Все блоки `aipilot/*` получают общие переменные из одного namespace-селектора:

```css
:where([class*="wp-block-aipilot-"]) {
    --ap-ink: var(--wp--preset--color--ink, #11120f);
    --ap-paper: var(--wp--preset--color--paper, #f3f1e9);
}
```

Нельзя перечислять существующие блоки вручную в селекторе токенов. Иначе новый блок может использовать `var(--ap-paper)` или `var(--ap-ink)`, но переменная не будет определена на его wrapper — визуальный класс изменится, а фон останется прежним.

Каждый новый или изменённый блок с параметрами `tone`, `variant`, `layout`, `spacing`, `width`, `alignment` или другими CSS-управляемыми атрибутами обязан иметь в `ai.json` раздел `styleContract`:

```json
{
  "styleContract": {
    "rootClass": "ap-example",
    "tokenScope": "shared",
    "classAttributes": {
      "tone": {
        "classPrefix": "is-tone-",
        "baseValues": ["paper"]
      }
    },
    "booleanClasses": {
      "reverse": "is-reverse"
    },
    "cssVariableAttributes": {
      "columns": {
        "variable": "--ap-columns"
      }
    }
  }
}
```

Правила контракта:

- `rootClass` должен присутствовать на корневом элементе `render.php` и иметь стили в общем CSS;
- `tokenScope: shared` означает обязательное подключение `aipilot-blocks-style` и `aipilot-blocks-editor-style` через `block.json`;
- каждое значение enum, кроме перечисленных в `baseValues`, должно иметь явный CSS-селектор;
- редактор и серверный рендер должны использовать одинаковые значения и префиксы классов;
- значения CSS-переменных должны присутствовать и в `render.php`, и в CSS;
- все `var()` должны иметь безопасные fallback-значения;
- после добавления варианта нужно вручную проверить его в iframe-редакторе и на публичной странице.

## Автоматический аудит библиотеки

Перед упаковкой плагина или публикацией нового блока необходимо выполнить read-only аудит:

```text
GET /wp-json/aipilot-blocks/v1/audit
```

или Ability/MCP-инструмент:

```text
aipilot-blocks/audit-library
```

Аудит проверяет обязательные файлы, Block API v3, регистрацию стилей, общий namespace-селектор токенов, `styleContract`, соответствие enum-классов серверному рендеру и наличие CSS для каждого визуального варианта. Релиз допустим только при `valid: true` и пустом массиве `errors`.

## Semantics and ARIA

A page has one H1. Root sections use H2; cards use H3. A titled section should have `aria-labelledby` referencing its visible heading. Prefer native HTML semantics over ARIA. Decorative visuals are hidden from assistive technology. All controls have accessible names and keyboard support.

## SEO and AI search

Keep essential copy in initial HTML, use meaningful headings and links, expose pages in the sitemap, and keep structured data consistent with visible content. `llms.txt` and JSON context improve discovery but never replace crawlable HTML.

## Agent workflow

Read manifest and rules, inspect the current tree, build a typed proposal, validate it, show a preview, save a draft, and publish only through a separate permission.


## Editorial and archive rules

For post content, the `single-post.html` template provides the only H1. AI-generated `post_content` must not add another H1. Start with `aipilot/article-lead` or a paragraph, use H2 for main sections and H3 for nested sections. Use `aipilot/media-text` for images that need explanatory context, `aipilot/pullquote` for quotations, `aipilot/callout` for short conclusions, `aipilot/link-card` for an important link, and `aipilot/article-links` for source lists.

Archive templates use `core/query` with `inherit: true`, semantic post cards, `core/query-no-results`, and pagination nested inside the Query block. Category and tag templates include `core/term-description`. Author and date archives use the same inherited query system.

Template-level blocks such as `aipilot/author-box` and `aipilot/related-posts` belong after `core/post-content`, not inside every article body.


## Editorial width contract

- The single-post template must place `core/post-content` directly in a constrained layout with an explicit reading width and wide width.
- Do not wrap post content in a narrower constrained group: it prevents `alignwide` and `alignfull` blocks from expanding.
- Text paragraphs target a readable width of about 900px on desktop.
- Editorial components use `width: 100%`, `max-width: none`, `min-width: 0`, and `box-sizing: border-box`; the FSE parent controls final alignment.
- Test every new editorial block at 1440, 1024, 768, and 390px.
- If the template provides fallback `author-box` or `related-posts`, avoid rendering a second copy when the post content already contains that block.


### Article rhythm and FAQ controls (1.2.4)

Post headings use compact vertical rhythm. FAQ exposes separate `headingSize` and `questionSize` enums. Full-bleed CTA backgrounds use symmetric bleed without horizontal overflow or a right-side gap.
