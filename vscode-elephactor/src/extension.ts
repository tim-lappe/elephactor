import * as vscode from "vscode";

export function activate(context: vscode.ExtensionContext) {
  const disposable = vscode.commands.registerCommand(
    "vscode-elephactor.installElephactor",
    () => {
      const terminal = vscode.window.createTerminal("Elephactor Install");
      terminal.show();
      terminal.sendText("composer require --dev tim-lappe/elephactor");
    },
  );
  context.subscriptions.push(disposable);

  const renameDisposable = vscode.workspace.onDidRenameFiles(async (event) => {
    for (const file of event.files) {
      const oldUri = file.oldUri;
      const newUri = file.newUri;

      vscode.window.showInformationMessage(`File renamed/moved: ${oldUri.fsPath} → ${newUri.fsPath}`);
    }
  });

  context.subscriptions.push(renameDisposable);
}

export function deactivate() {}