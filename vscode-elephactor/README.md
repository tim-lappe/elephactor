# Elephactor VS Code Extension

Elephactor is a starter VS Code extension that registers a single `Elephactor: Hello World` command. Use it as the base for experimenting with editor automations or future Elephactor tooling.

## Getting started

1. Install dependencies:

   ```bash
   npm install
   ```

2. Build once or start watch mode:

   ```bash
   npm run compile
   # or
   npm run watch
   ```

3. Press `F5` in VS Code to launch an Extension Development Host, then run the `Elephactor: Hello World` command from the Command Palette.

## Available scripts

- `npm run compile` compiles the TypeScript sources into `dist`.
- `npm run watch` keeps the compiler running for iterative development.
- `npm run check` performs a no-emit type check.
- `npm run test` executes the extension tests via `@vscode/test-cli`.
- `npm run package` creates a `.vsix` artifact using `@vscode/vsce`.

## Extension structure

- `src/extension.ts`: activation entry point and command registration.
- `.vscode/`: launch and task configs for debugging and building.
- `.vscodeignore`: excludes dev-only files from packaged artifacts.
- `vsc-extension-quickstart.md`: handy reminders for extension development.

## Next steps

- Add new commands in `package.json` under `contributes.commands`.
- Use the VS Code API through the `vscode` module to interact with the editor.
- Expand the test suite by adding files in `src/test` and running `npm run test`.

