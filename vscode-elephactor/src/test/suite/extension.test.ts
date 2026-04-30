import * as assert from "node:assert";
import * as vscode from "vscode";

suite("Elephactor Extension", () => {
  test("registers hello world command", async () => {
    const commands = await vscode.commands.getCommands(true);
    assert.ok(
      commands.includes("vscode-elephactor.helloWorld"),
      "Command vscode-elephactor.helloWorld is missing",
    );
  });
});

