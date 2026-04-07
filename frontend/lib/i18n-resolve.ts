export type MessageTree = string | { [key: string]: MessageTree };

export function resolveMessage(messages: MessageTree, path: string): string {
  const parts = path.split(".").filter(Boolean);
  let cur: MessageTree | undefined = messages;
  for (const p of parts) {
    if (cur === undefined || typeof cur === "string") {
      return path;
    }
    cur = cur[p];
  }
  return typeof cur === "string" ? cur : path;
}
