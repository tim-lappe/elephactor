# Elephactor Quickstart

Thank you for building with Elephactor. This document summarizes the most common workflows when iterating on the extension.

## Run in development

1. Execute `npm install` if you have not yet pulled dependencies.
2. Run `npm run watch` to keep the TypeScript compiler active.
3. Press `F5` from this workspace to open a new Extension Development Host window.
4. Open the Command Palette (`Cmd+Shift+P`), search for `Elephactor: Hello World`, and run it.

## Test the extension

Use `npm run test` to launch the VS Code Extension Test CLI. Expand the suites inside `src/test` to cover your commands, helpers, and integration behaviors.

## Package for distribution

1. Ensure every change is committed and versioned in `package.json`.
2. Run `npm run package` to produce a `.vsix` artifact inside the root directory.
3. Share the generated file or publish it with `vsce publish` once a publisher is configured.

## Troubleshooting tips

- Re-run `npm run compile` whenever TypeScript output appears stale.
- Delete the `.vscode-test` folder if the test CLI leaves corrupted caches.
- Verify the `engines.vscode` range in `package.json` matches the target VS Code release.

