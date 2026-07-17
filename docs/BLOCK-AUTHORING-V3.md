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

## Semantics and ARIA

A page has one H1. Root sections use H2; cards use H3. A titled section should have `aria-labelledby` referencing its visible heading. Prefer native HTML semantics over ARIA. Decorative visuals are hidden from assistive technology. All controls have accessible names and keyboard support.

## SEO and AI search

Keep essential copy in initial HTML, use meaningful headings and links, expose pages in the sitemap, and keep structured data consistent with visible content. `llms.txt` and JSON context improve discovery but never replace crawlable HTML.

## Agent workflow

Read manifest and rules, inspect the current tree, build a typed proposal, validate it, show a preview, save a draft, and publish only through a separate permission.
