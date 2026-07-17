# AI Pilot Blocks

Расширяемая библиотека динамических Gutenberg-блоков для FSE-сайтов и управления через AI Pilot / MCP.

## Почему блоки совместимы с агентом

1. Все блоки зарегистрированы через `block.json` API v3.
2. Названия имеют стабильный namespace `aipilot/*`.
3. Изменяемый текст хранится в атрибутах блока без HTML `source`.
4. Фронтенд формируется серверным `render.php`, поэтому изменение атрибутов через `update-block-attributes` сразу отражается на сайте.
5. Контейнерные блоки сохраняют `InnerBlocks`, поэтому работают операции вставки, удаления и перемещения по дереву.
6. Каждый блок содержит `ai.json` с назначением и ограничениями.
7. Плагин предоставляет REST-манифест и read-only Ability `aipilot-blocks/get-manifest`.
8. Плагин не создаёт таблицы, CPT, таксономии или опции и не меняет настройки БД.

## Готовые блоки

- `aipilot/section`
- `aipilot/hero`
- `aipilot/logo-cloud`
- `aipilot/statement`
- `aipilot/feature-grid`
- `aipilot/feature-card`
- `aipilot/split-panel`
- `aipilot/steps`
- `aipilot/stats`
- `aipilot/testimonial`
- `aipilot/faq`
- `aipilot/cta`

## REST и Abilities

- `GET /wp-json/aipilot-blocks/v1/manifest` — требует `edit_posts`.
- `aipilot-blocks/get-manifest` — read-only WordPress Ability; автоматически добавляется в конфигурацию MCP Adapter, когда он установлен.
- Стандартная Ability `aipilot-mcp/get-block-registry` также видит все блоки автоматически.

## Как добавить новый блок

Создайте папку `blocks/my-block/`:

```text
my-block/
├── block.json
├── ai.json
├── index.js
├── index.asset.php
└── render.php
```

### Обязательные правила

- используйте имя `aipilot/my-block`;
- храните редактируемый агентом контент в атрибутах без `source`;
- используйте динамический `render.php`;
- ограничивайте варианты через `enum` в `block.json` и повторяйте проверку на сервере;
- экранируйте текст через `esc_html`, URL через `esc_url`, разрешённый HTML через `wp_kses_post`;
- не выполняйте запись в БД при регистрации блока;
- для контейнера возвращайте `InnerBlocks.Content` в `save()`.

## Установка

Установите ZIP через **Плагины → Добавить новый → Загрузить плагин**. Сборка Node.js не требуется: архив содержит готовые браузерные скрипты без JSX.

## 1.0.1

- секционные блоки по умолчанию используют выравнивание `full`;
- исправлена ширина `feature-grid` и горизонтального блока `steps`;
- добавлены защитные `width: 100%`, `min-width: 0` и `box-sizing` для сеток;
- существующий контент получает корректную ширину через значение атрибута по умолчанию без миграции БД.


## API v3 authoring and agent rules

- Machine rules: `rules/block-authoring-v3.json`
- Human guide: `docs/BLOCK-AUTHORING-V3.md`
- Public manifest: `/wp-json/aipilot-blocks/v1/manifest`
- Public rules: `/wp-json/aipilot-blocks/v1/rules`
- Authenticated validation: `POST /wp-json/aipilot-blocks/v1/validate` with serialized Gutenberg `content`
- Abilities: `aipilot-blocks/get-manifest`, `aipilot-blocks/get-rules`, `aipilot-blocks/validate-content`

The library now includes `aipilot/seo-ai-benefits`, bringing the total to 13 blocks. Public discovery can be disabled with the `aipilot_blocks_public_discovery` filter.
