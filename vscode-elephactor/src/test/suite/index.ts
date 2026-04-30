import * as path from "node:path";
import glob from "glob";
import Mocha from "mocha";

export async function run(): Promise<void> {
  const mocha = new Mocha({
    ui: "bdd",
    color: true,
    timeout: 10000,
  });
  const testsRoot = path.resolve(__dirname, ".");

  const files = await new Promise<string[]>((resolve, reject) => {
    glob("**/*.test.js", { cwd: testsRoot }, (err, matches) => {
      if (err) {
        reject(err);
        return;
      }
      resolve(matches ?? []);
    });
  });

  files.forEach((file) => {
    mocha.addFile(path.resolve(testsRoot, file));
  });

  await new Promise<void>((resolve, reject) => {
    mocha.run((failures) => {
      if (failures > 0) {
        reject(new Error(`${failures} tests failed`));
        return;
      }
      resolve();
    });
  });
}

