-- دکتر خسته | ساختار دیتابیس D1

CREATE TABLE IF NOT EXISTS admins (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT UNIQUE NOT NULL,
  password_hash TEXT NOT NULL,
  salt TEXT NOT NULL,
  name TEXT NOT NULL,
  color TEXT NOT NULL DEFAULT '#f2a154',
  is_super INTEGER NOT NULL DEFAULT 0,
  telegram_chat_id TEXT,
  telegram_link_code TEXT,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS sessions (
  token TEXT PRIMARY KEY,
  admin_id INTEGER NOT NULL,
  expires_at TEXT NOT NULL,
  FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tasks (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  description TEXT,
  due_date TEXT,               -- فرمت YYYY-MM-DD
  status TEXT NOT NULL DEFAULT 'pending',   -- pending | in_progress | done
  priority TEXT NOT NULL DEFAULT 'normal',  -- low | normal | high | urgent
  created_by INTEGER NOT NULL,
  assigned_to INTEGER,
  notified_1day INTEGER NOT NULL DEFAULT 0,
  notified_due INTEGER NOT NULL DEFAULT 0,
  notified_overdue INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  updated_at TEXT NOT NULL DEFAULT (datetime('now')),
  FOREIGN KEY (created_by) REFERENCES admins(id),
  FOREIGN KEY (assigned_to) REFERENCES admins(id)
);

CREATE TABLE IF NOT EXISTS notes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  content TEXT,
  color TEXT NOT NULL DEFAULT '#4fd1c5',
  created_by INTEGER NOT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  updated_at TEXT NOT NULL DEFAULT (datetime('now')),
  FOREIGN KEY (created_by) REFERENCES admins(id)
);

CREATE TABLE IF NOT EXISTS settings (
  key TEXT PRIMARY KEY,
  value TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS messages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  admin_id INTEGER NOT NULL,
  content TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_tasks_due ON tasks(due_date);
CREATE INDEX IF NOT EXISTS idx_notes_created_by ON notes(created_by);
CREATE INDEX IF NOT EXISTS idx_messages_created_at ON messages(created_at);
