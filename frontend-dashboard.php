<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>دکتر خسته | داشبورد خلاقانه تیمی</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jetbrains-mono@1.0.6/css/jetbrains-mono.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<style>
:root {
  --bg: #030712;
  --surface: rgba(17, 24, 39, 0.7);
  --surface-2: rgba(31, 41, 55, 0.65);
  --border: rgba(255, 255, 255, 0.08);
  --border-glow: rgba(59, 130, 246, 0.25);
  --text: #f3f4f6;
  --text-dim: #9ca3af;
  --accent: #f59e0b;
  --accent-glow: rgba(245, 158, 11, 0.35);
  --accent2: #10b981;
  --accent2-glow: rgba(16, 185, 129, 0.35);
  --danger: #ef4444;
  --success: #10b981;
  --radius: 20px;
  --mono: 'JetBrains Mono', monospace;
  --shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
  --glass: backdrop-filter: blur(12px) saturate(180%); -webkit-backdrop-filter: blur(12px) saturate(180%);
}
:root.light-mode {
  --bg: #f3f4f6;
  --surface: rgba(255, 255, 255, 0.75);
  --surface-2: rgba(243, 244, 246, 0.8);
  --border: rgba(0, 0, 0, 0.06);
  --border-glow: rgba(59, 130, 246, 0.15);
  --text: #111827;
  --text-dim: #6b7280;
  --accent: #d97706;
  --accent-glow: rgba(217, 119, 6, 0.2);
  --accent2: #059669;
  --accent2-glow: rgba(5, 150, 105, 0.2);
  --danger: #dc2626;
  --success: #059669;
  --shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
}
*{box-sizing:border-box; transition: background 0.3s, border-color 0.3s, box-shadow 0.3s;}
html,body{margin:0;padding:0;}
body{
  background: var(--bg);
  background-image:
    radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.12) 0, transparent 50%),
    radial-gradient(at 100% 0%, rgba(245, 158, 11, 0.1) 0, transparent 50%),
    radial-gradient(at 50% 100%, rgba(16, 185, 129, 0.08) 0, transparent 50%);
  color:var(--text);
  font-family:'Vazirmatn',sans-serif;
  min-height:100vh;
  overflow-x:hidden;
}
::selection{background:var(--accent);color:#111;}
a{color:inherit; text-decoration: none;}
button{font-family:inherit;}
.hidden{display:none !important;}

/* --- عنصر امضا: خط پالس/مانیتور --- */
.pulse-line{width:100%;height:34px;overflow:hidden;opacity:.85; border-radius: 10px; margin-bottom: 20px;}
.pulse-line svg{width:200%;height:100%;animation:pulse-move 8s linear infinite;}
@keyframes pulse-move{from{transform:translateX(0);}to{transform:translateX(-50%);}}
.pulse-line path{fill:none;stroke:var(--accent2);stroke-width:2.5;filter:drop-shadow(0 0 6px var(--accent2-glow));}

/* ---------- هدر موبایل ---------- */
.mobile-header {
  display: none;
  background: var(--surface);
  --glass;
  border-bottom: 1px solid var(--border);
  padding: 14px 20px;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 90;
}
.menu-toggle {
  background: none;
  border: none;
  color: var(--text);
  cursor: pointer;
  padding: 6px;
}

/* ---------- دکمه‌های اصلی و ورودی‌ها ---------- */
.field{margin-bottom:18px;}
.field label{display:block;font-size:13.5px;color:var(--text-dim);margin-bottom:8px;font-weight:600;}
.field input,.field select,.field textarea{
  width:100%;padding:12px 16px;border-radius:12px;border:1px solid var(--border);
  background:var(--surface-2);color:var(--text);font-family:inherit;font-size:14px;outline:none;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
}
.field input:focus,.field select:focus,.field textarea:focus{
  border-color:var(--accent);
  box-shadow: 0 0 0 3px var(--accent-glow);
}
.field input[type=color]{padding:6px;height:46px;cursor:pointer;}
.btn{
  display:inline-flex;align-items:center;justify-content:center;gap:10px;
  padding:12px 24px;border-radius:12px;border:none;cursor:pointer;font-size:14px;font-weight:700;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transform: translateY(0);
  transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
  font-family:inherit;
}
.btn:hover{
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}
.btn:active{transform:translateY(0);}
.btn-primary{
  background: linear-gradient(135deg, var(--accent), #e08840);
  color:#111;
}
.btn-primary:hover{filter:brightness(1.12);}
.btn-ghost {
  background: var(--surface-2);
  --glass;
  color: var(--text);
  border: 1px solid var(--border);
}
.btn-ghost:hover {
  border-color: var(--accent2);
  box-shadow: 0 0 10px var(--accent2-glow);
}
.btn-danger{
  background: rgba(239, 68, 68, 0.15);
  color: var(--danger);
  border: 1px solid rgba(239, 68, 68, 0.3);
}
.btn-danger:hover {
  background: var(--danger);
  color: #fff;
}
.btn-block{width:100%;}
.btn-sm{padding:8px 16px;font-size:13px;border-radius:10px;}

/* ---------- آیکون‌های SVG کاستوم ---------- */
.svg-icon {
  width: 18px;
  height: 18px;
  stroke-width: 2.2;
  stroke: currentColor;
  fill: none;
  display: inline-block;
  vertical-align: middle;
}
.svg-icon-large {
  width: 52px;
  height: 52px;
  stroke-width: 1.5;
  stroke: var(--text-dim);
  fill: none;
  margin-bottom: 14px;
}
.svg-icon-inline {
  width: 14px;
  height: 14px;
  stroke-width: 2.2;
  stroke: currentColor;
  fill: none;
  display: inline-block;
  vertical-align: middle;
  margin-left: 6px;
}

/* ---------- چیدمان اصلی با طراحی شیشه‌ای مدرن ---------- */
.app-shell{
  display:flex;
  min-height:100vh;
  padding: 16px;
  gap: 16px;
}
.sidebar {
  width: 280px;
  flex-shrink: 0;
  background: var(--surface);
  --glass;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  display: flex;
  flex-direction: column;
  padding: 24px 18px;
  position: sticky;
  top: 16px;
  height: calc(100vh - 32px);
  box-shadow: var(--shadow);
}
.sidebar .brand{
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 24px;
  padding: 0 8px;
}
.brand-dot{
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: var(--accent);
  box-shadow: 0 0 14px var(--accent);
  animation: blink 2.5s ease-in-out infinite;
}
@keyframes blink{0%,100%{opacity:1; transform: scale(1.0);} 50%{opacity:.4; transform: scale(0.9);}}
.sidebar .brand h1{
  font-size: 20px;
  margin: 0;
  font-weight: 900;
  letter-spacing: -0.03em;
}
.nav-item{
  display:flex;align-items:center;gap:14px;padding:12px 14px;border-radius:12px;
  color:var(--text-dim);cursor:pointer;font-size:14.5px;margin-bottom:4px;transition:0.2s ease;
}
.nav-item:hover{
  background:var(--surface-2);
  color:var(--text);
  transform: translateX(-4px);
}
.nav-item.active{
  background: var(--surface-2);
  color: var(--accent);
  font-weight: 800;
  border-right: 3px solid var(--accent);
  box-shadow: inset -4px 0 10px var(--accent-glow);
}
.nav-spacer{flex:1;}
.user-badge{
  display:flex;align-items:center;gap:12px;padding:12px;border-radius:14px;
  background:var(--surface-2);
  border:1px solid var(--border);
}
.avatar{
  width:38px;
  height:38px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:800;
  font-size:14px;
  color:#111;
  flex-shrink:0;
  box-shadow: 0 0 10px rgba(0,0,0,0.15);
}
.user-badge .name{font-size:13.5px;font-weight:700;}
.user-badge .role{font-size:11px;color:var(--text-dim);margin-top:2px;}
.logout-btn{
  margin-right:auto;
  background:none;
  border:none;
  color:var(--text-dim);
  cursor:pointer;
  padding:6px;
  transition: color 0.2s;
}
.logout-btn:hover{color:var(--danger);}

/* بخش نمایش اصلی */
.main {
  flex: 1;
  background: var(--surface);
  --glass;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 32px;
  box-shadow: var(--shadow);
  overflow-y: auto;
  height: calc(100vh - 32px);
  position: sticky;
  top: 16px;
}
.view{display:none;animation:fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);}
.view.active{display:block;}
@keyframes fadeIn{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:none;}}
.view-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:14px;}
.view-header h2{margin:0;font-size:24px;font-weight:900; letter-spacing: -0.02em;}
.view-header p{margin:4px 0 0;color:var(--text-dim);font-size:14px;}

/* ---------- کارت‌های آماری خلاقانه ---------- */
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
.stat-card{
  background:var(--surface-2);
  --glass;
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:22px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  position: relative;
  overflow: hidden;
}
.stat-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; width: 4px; height: 100%;
  background: var(--text-dim);
}
.stat-card.accent::before { background: var(--accent); }
.stat-card.teal::before { background: var(--accent2); }
.stat-card.danger::before { background: var(--danger); }

.stat-card .num{font-family:var(--mono);font-size:28px;font-weight:900;}
.stat-card .label{color:var(--text-dim);font-size:13px;margin-top:6px;font-weight:600;}
.stat-card.accent .num{color:var(--accent); text-shadow: 0 0 10px var(--accent-glow);}
.stat-card.teal .num{color:var(--accent2); text-shadow: 0 0 10px var(--accent2-glow);}
.stat-card.danger .num{color:var(--danger); text-shadow: 0 0 10px rgba(239, 68, 68, 0.25);}

/* ---------- طراحی تسک‌ها ---------- */
.filter-bar{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
.chip{
  padding:8px 18px;
  border-radius:100px;
  border:1px solid var(--border);
  background:var(--surface-2);
  color:var(--text-dim);
  font-size:13px;
  font-weight:700;
  cursor:pointer;
  transition: all 0.2s ease;
}
.chip.active{
  border-color:var(--accent);
  color:var(--accent);
  background:var(--accent-glow);
  box-shadow: 0 4px 12px var(--accent-glow);
}
.task-list{display:flex;flex-direction:column;gap:12px;}
.task-card{
  background:var(--surface-2);
  --glass;
  border:1px solid var(--border);
  border-radius:16px;
  padding:16px 20px;
  display:flex;
  align-items:center;
  gap:16px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.task-card:hover {
  transform: translateY(-2px);
  border-color: var(--border-glow);
  box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}
.task-check{
  width:24px;height:24px;border-radius:8px;border:2.5px solid var(--border);
  flex-shrink:0;cursor:pointer;display:flex;align-items:center;justify-content:center;
}
.task-check.done{background:var(--success);border-color:var(--success); box-shadow: 0 0 10px var(--accent2-glow);}
.task-check.done::after{content:'✓';color:#111;font-size:14px;font-weight:900;}
.task-body{flex:1;min-width:0;}
.task-title{font-size:15px;font-weight:700;}
.task-title.done{text-decoration:line-through;color:var(--text-dim);opacity: 0.5;}
.task-meta{display:flex;gap:12px;margin-top:6px;font-size:12px;color:var(--text-dim);flex-wrap:wrap;align-items:center;}
.badge{
  display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:100px;
  font-size:11.5px;font-family:var(--mono); font-weight: 800;
}
.pr-urgent{background:rgba(239, 68, 68, 0.15);color:var(--danger);}
.pr-high{background:rgba(245, 158, 11, 0.15);color:var(--accent);}
.pr-normal{background:rgba(16, 185, 129, 0.15);color:var(--accent2);}
.pr-low{background:var(--surface-2);color:var(--text-dim);}
.dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;}
.task-actions{display:flex;gap:8px;}
.icon-btn{
  background:none;border:none;color:var(--text-dim);cursor:pointer;padding:8px;border-radius:10px;
}
.icon-btn:hover{background:var(--surface-2);color:var(--text);}
.icon-btn.danger:hover{color:var(--danger); background: rgba(239, 68, 68, 0.12);}
.empty-state{text-align:center;padding:60px 20px;color:var(--text-dim);}

/* ---------- طراحی تقویم ---------- */
.cal-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;}
.cal-head h3{margin:0;font-size:18px; font-weight: 800;}
.cal-nav{display:flex;gap:8px;}
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:8px;}
.cal-dow{text-align:center;font-size:12px;color:var(--text-dim);font-weight:700;padding-bottom:10px;}
.cal-day{
  aspect-ratio:1/0.85;border-radius:12px;border:1px solid var(--border);background:var(--surface-2);
  padding:10px;font-size:13px;font-family:var(--mono);cursor:pointer;position:relative;
  display:flex;flex-direction:column;gap:4px;
}
.cal-day:hover {
  border-color: var(--accent);
  box-shadow: 0 0 10px var(--accent-glow);
}
.cal-day.other-month{opacity:.22;}
.cal-day.today{border-color:var(--accent2);box-shadow:0 0 0 2px var(--accent2) inset;}
.cal-day.selected{border-color:var(--accent); background: var(--surface-2); box-shadow: 0 0 12px var(--accent-glow);}
.cal-dots{display:flex;gap:4px;flex-wrap:wrap;margin-top:auto;}
.cal-dots .dot{width:7px;height:7px;}
.cal-day-tasks{margin-top:24px;}

/* ---------- طراحی مدرن ابسیدین ---------- */
.obsidian-layout {
  display: grid;
  grid-template-columns: 240px 280px 1fr;
  gap: 16px;
  background: var(--surface-2);
  --glass;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  height: calc(100vh - 200px);
  overflow: hidden;
  box-shadow: var(--shadow);
}
.obsidian-folders {
  border-left: 1px solid var(--border);
  background: rgba(3, 7, 18, 0.2);
  padding: 18px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  overflow-y: auto;
}
.folder-title {
  font-size: 13.5px;
  color: var(--text-dim);
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.folder-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.folder-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 12px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 14px;
  color: var(--text-dim);
}
.folder-item:hover, .folder-item.active {
  background: var(--surface-2);
  color: var(--accent);
  font-weight: 700;
}
.folder-badge {
  background: var(--border);
  font-size: 11px;
  padding: 3px 8px;
  border-radius: 100px;
  color: var(--text-dim);
}
.obsidian-notes-list {
  border-left: 1px solid var(--border);
  background: rgba(17, 24, 39, 0.3);
  padding: 18px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  overflow-y: auto;
}
.obsidian-card {
  padding: 14px;
  border-radius: 12px;
  background: var(--surface-2);
  border: 1px solid var(--border);
  cursor: pointer;
}
.obsidian-card:hover, .obsidian-card.active {
  border-color: var(--accent);
  box-shadow: 0 4px 15px var(--accent-glow);
}
.obsidian-card h4 {
  margin: 0 0 6px 0;
  font-size: 14.5px;
  font-weight: 800;
}
.obsidian-card p {
  margin: 0;
  font-size: 12px;
  color: var(--text-dim);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.obsidian-editor {
  background: transparent;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  height: 100%;
}
.editor-toolbar {
  padding: 14px 20px;
  border-bottom: 1px solid var(--border);
  background: rgba(3, 7, 18, 0.4);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}
.editor-tabs {
  display: flex;
  gap: 6px;
}
.editor-main-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  padding: 20px;
}
.markdown-editor {
  width: 100%;
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  resize: none;
  color: var(--text);
  font-family: var(--mono);
  font-size: 15px;
  line-height: 1.8;
}
.markdown-preview {
  flex: 1;
  overflow-y: auto;
  font-size: 14.5px;
  line-height: 1.8;
  color: var(--text);
  padding: 10px;
}
.markdown-preview h1, .markdown-preview h2, .markdown-preview h3 {
  border-bottom: 1px solid var(--border);
  padding-bottom: 8px;
  margin-top: 20px;
}
.markdown-preview img {
  max-width: 100%;
  border-radius: 12px;
  border: 1px solid var(--border);
  box-shadow: var(--shadow);
}

/* ---------- ادمین‌ها ---------- */
.admin-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;}
.admin-card{
  background:var(--surface-2);
  --glass;
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:24px;
  text-align:center;
  box-shadow: var(--shadow);
}
.admin-card:hover {
  transform: translateY(-2px);
  border-color: var(--border-glow);
}
.admin-card .avatar{width:56px;height:56px;font-size:20px;margin:0 auto 12px;}
.super-tag{
  display:inline-block;margin-top:8px;font-size:11px;
  background:var(--accent-glow);color:var(--accent);
  padding:3px 12px;border-radius:100px; font-weight: 800;
}

/* ---------- تنظیمات ---------- */
.settings-card{
  background:var(--surface-2);
  --glass;
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:24px;max-width:100%;margin-bottom:18px;
  box-shadow:var(--shadow);
}
.settings-card h3{margin:0 0 6px;font-size:16px;display:flex;align-items:center;gap:10px; font-weight: 800;}
.settings-card p{color:var(--text-dim);font-size:13.5px;margin:0 0 16px;line-height:1.8;}
.code-box{
  font-family:var(--mono);background:var(--surface);border:1.5px dashed var(--border);
  border-radius:12px;padding:16px;text-align:center;font-size:22px;letter-spacing:3px;
  color:var(--accent2);margin-bottom:16px;
}
.status-line{display:flex;align-items:center;gap:10px;font-size:14px;margin-bottom:16px;}
.status-dot{width:11px;height:11px;border-radius:50%;}
.status-dot.on{background:var(--success);box-shadow:0 0 10px var(--success);}
.status-dot.off{background:var(--text-dim);}

/* ---------- میز کار و گفتگوی تیمی ---------- */
.chat-container{
  background:var(--surface-2);
  --glass;
  border:1px solid var(--border);
  border-radius:var(--radius);
  height:62vh;
  display:flex;
  flex-direction:column;
  overflow:hidden;
  box-shadow: var(--shadow);
}
.chat-messages{
  flex:1;
  overflow-y:auto;
  padding:24px;
  display:flex;
  flex-direction:column;
  gap:14px;
}
.chat-bubble{
  max-width:75%;
  padding:12px 16px;
  border-radius:16px;
  background:var(--surface);
  position:relative;
  border-right:4px solid var(--bubble-color, var(--accent));
  align-self:flex-start;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.chat-bubble.me{
  align-self:flex-end;
  border-right:none;
  border-left:4px solid var(--bubble-color, var(--accent));
}
.chat-meta-info{
  font-size:11.5px;
  color:var(--text-dim);
  margin-bottom:6px;
  display:flex;
  align-items:center;
  gap:8px;
}
.chat-content-text{
  font-size:14px;
  line-height:1.7;
  white-space:pre-wrap;
}
.chat-input-bar{
  padding:16px;
  border-top:1px solid var(--border);
  background:rgba(3, 7, 18, 0.3);
  display:flex;
  gap:12px;
}
.chat-input-bar input{
  flex:1;
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:12px;
  padding:12px 16px;
  color:var(--text);
  outline:none;
}
.chat-input-bar input:focus{
  border-color:var(--accent);
  box-shadow: 0 0 0 3px var(--accent-glow);
}

/* ---------- مودال ---------- */
.modal-overlay{
  position:fixed;inset:0;background:rgba(3, 7, 18, 0.7);backdrop-filter:blur(8px);
  display:flex;align-items:center;justify-content:center;z-index:100;padding:20px;
}
.modal{
  background:var(--surface);
  --glass;
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:32px;width:100%;max-width:460px;max-height:88vh;overflow-y:auto;
  box-shadow: var(--shadow);
}
.modal h3{margin:0 0 20px;font-size:18px; font-weight: 800;}
.modal-actions{display:flex;gap:12px;margin-top:20px;}
.modal-actions .btn{flex:1;}
.row-2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}

.toast{
  position:fixed;bottom:24px;left:50%;transform:translateX(-50%);
  background:var(--surface-2);
  --glass;
  border:1px solid var(--border);color:var(--text);
  padding:14px 24px;border-radius:14px;font-size:14px;z-index:200;box-shadow: 0 10px 40px rgba(0,0,0,0.4);
  display:flex;align-items:center;gap:12px;
}
.toast.err{border-color:var(--danger);color:var(--danger);}

/* ---------- رسپانسیو و موبایل ---------- */
@media(max-width:820px){
  .mobile-header {
    display: flex;
  }
  .app-shell {
    flex-direction: column;
    padding: 10px;
  }
  .sidebar {
    position: fixed;
    top: 0;
    right: -290px;
    height: 100vh;
    width: 280px;
    box-shadow: -10px 0 40px rgba(0,0,0,0.5);
    z-index: 1000;
  }
  .sidebar.open {
    right: 0;
  }
  #sidebar-close-btn {
    display: block !important;
  }
  .main {
    padding: 20px;
    height: auto;
  }
  .stat-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
  .cal-day {
    aspect-ratio: auto;
    height: 56px;
    padding: 6px;
    font-size: 11px;
  }
  .obsidian-layout {
    grid-template-columns: 1fr;
    height: auto;
  }
  .obsidian-folders, .obsidian-notes-list {
    border-left: none;
    border-bottom: 1px solid var(--border);
    max-height: 220px;
  }
}
@media(max-width:480px){
  .stat-grid {
    grid-template-columns: 1fr;
  }
  .row-2 {
    grid-template-columns: 1fr;
  }
}
</style>
</head>
<body>

<!-- هدر موبایل -->
<header class="mobile-header">
  <div class="brand" style="display:flex; align-items:center; gap:10px;"><span class="brand-dot"></span><h1 style="margin:0; font-size:18px;">دکتر خسته</h1></div>
  <button class="menu-toggle" onclick="toggleSidebar()">
    <svg class="svg-icon" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" /></svg>
  </button>
</header>

<!-- ================= اپ اصلی ================= -->
<div id="app" class="app-shell">
  <aside class="sidebar" id="sidebar">
    <div class="brand" style="display:flex; justify-content:space-between; align-items:center; width:100%;">
      <div style="display:flex; align-items:center; gap:10px;"><span class="brand-dot"></span><h1>دکتر خسته</h1></div>
      <button onclick="toggleSidebar()" style="background:none; border:none; color:var(--text-dim); cursor:pointer; display:none;" id="sidebar-close-btn">
        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </button>
    </div>

    <!-- دکمه سوییچ تم دارک/لایت -->
    <div style="padding:0 6px 16px 6px;">
      <button class="btn btn-ghost btn-block" onclick="toggleTheme()" style="justify-content: flex-start; gap:12px;">
        <svg class="svg-icon" viewBox="0 0 24 24" id="theme-btn-icon"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span id="theme-btn-text">تم روشن</span>
      </button>
    </div>

    <nav>
      <div class="nav-item active" data-view="overview">
        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>نمای کلی</span>
      </div>
      <div class="nav-item" data-view="tasks">
        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>تسک‌ها</span>
      </div>
      <div class="nav-item" data-view="calendar">
        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>تقویم</span>
      </div>
      <div class="nav-item" data-view="chat">
        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>میز کار و گفتگو</span>
      </div>
      <div class="nav-item" data-view="notes">
        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>یادداشت‌ها</span>
      </div>
      <div class="nav-item" data-view="admins" id="nav-admins">
        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>ادمین‌ها</span>
      </div>
      <div class="nav-item" data-view="settings">
        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>تنظیمات</span>
      </div>
    </nav>
    <div class="nav-spacer"></div>
    <div class="user-badge">
      <div class="avatar" id="me-avatar"></div>
      <div>
        <div class="name" id="me-name"></div>
        <div class="role" id="me-role"></div>
      </div>
    </div>
  </aside>

  <main class="main" onclick="closeSidebarOnNavigate()">

    <!-- نمای کلی -->
    <section class="view active" id="view-overview">
      <div class="view-header"><div><h2>نمای کلی</h2><p>وضعیت کلی پروژه دکتر خسته</p></div></div>
      <div class="pulse-line"><svg viewBox="0 0 300 34" preserveAspectRatio="none"><path d="M0,17 L40,17 L52,4 L64,30 L76,17 L300,17 L340,17 L352,4 L364,30 L376,17 L600,17"/></svg></div>
      <div class="stat-grid" id="stat-grid" style="margin-top:16px;"></div>

      <!-- نمودارهای گرافیکی گزارش‌گیری -->
      <div class="settings-card" style="margin-bottom: 24px;">
        <h3 style="font-size: 16px; margin-bottom: 18px;">📊 نمودارها و تحلیل عملکرد</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
          <div style="background: var(--surface); padding: 18px; border-radius: 14px; border: 1px solid var(--border); display: flex; flex-direction: column; align-items: center;">
            <span style="font-size: 13.5px; color: var(--text-dim); margin-bottom: 12px; font-weight:700;">توزیع تسک‌ها بر اساس وضعیت</span>
            <div style="position: relative; width: 220px; height: 220px;">
              <canvas id="statusChart"></canvas>
            </div>
          </div>
          <div style="background: var(--surface); padding: 18px; border-radius: 14px; border: 1px solid var(--border); display: flex; flex-direction: column; align-items: center;">
            <span style="font-size: 13.5px; color: var(--text-dim); margin-bottom: 12px; font-weight:700;">تعداد تسک‌ها بر اساس اولویت</span>
            <div style="position: relative; width: 100%; height: 220px;">
              <canvas id="priorityChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="view-header"><h2 style="font-size:18px;">تسک‌های نزدیک</h2></div>
      <div class="task-list" id="overview-tasks"></div>
    </section>

    <!-- تسک‌ها -->
    <section class="view" id="view-tasks">
      <div class="view-header">
        <div><h2>تسک‌ها</h2><p>مدیریت کارهای پروژه</p></div>
        <button class="btn btn-primary" onclick="openTaskModal()">
          <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"/></svg>
          تسک جدید
        </button>
      </div>
      <div class="filter-bar" id="task-filters">
        <div class="chip active" data-filter="all">همه</div>
        <div class="chip" data-filter="pending">در انتظار</div>
        <div class="chip" data-filter="in_progress">در حال انجام</div>
        <div class="chip" data-filter="done">انجام‌شده</div>
      </div>
      <div class="task-list" id="task-list"></div>
    </section>

    <!-- تقویم -->
    <section class="view" id="view-calendar">
      <div class="view-header"><div><h2>تقویم موعد تسک‌ها</h2><p>روی هر روز کلیک کنید تا تسک‌های آن را ببینید</p></div></div>
      <div class="cal-head">
        <div class="cal-nav">
          <button class="btn btn-ghost btn-sm" onclick="calShift(-1)">‹ ماه قبل</button>
          <button class="btn btn-ghost btn-sm" onclick="calShift(1)">ماه بعد ›</button>
        </div>
        <h3 id="cal-title"></h3>
      </div>
      <div class="cal-grid" id="cal-dow"></div>
      <div class="cal-grid" id="cal-grid" style="margin-top:6px;"></div>
      <div class="cal-day-tasks">
        <h3 style="font-size:15px; font-weight: 800;" id="cal-day-label"></h3>
        <div class="task-list" id="cal-day-tasks"></div>

        <!-- فرم ثبت سریع تسک برای روز انتخاب شده -->
        <div id="cal-quick-add" class="settings-card" style="margin-top: 24px; padding: 20px;">
          <h4 style="margin:0 0 14px; font-size:14.5px; color:var(--accent2); display:flex; align-items:center; gap:8px; font-weight: 800;">
            <svg class="svg-icon-inline" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            ثبت سریع تسک برای این روز
          </h4>
          <div style="display:flex; flex-wrap:wrap; gap:12px;">
            <input id="q-title" placeholder="عنوان تسک..." style="flex:2; min-width:200px; padding:10px 14px; border-radius:10px; border:1px solid var(--border); background:var(--surface); color:var(--text); outline:none;">
            <select id="q-priority" style="flex:1; min-width:110px; padding:10px; border-radius:10px; border:1px solid var(--border); background:var(--surface); color:var(--text); outline:none;">
              <option value="normal">عادی</option>
              <option value="low">کم</option>
              <option value="high">بالا</option>
              <option value="urgent">فوری</option>
            </select>
            <select id="q-assignee" style="flex:1; min-width:130px; padding:10px; border-radius:10px; border:1px solid var(--border); background:var(--surface); color:var(--text); outline:none;">
              <option value="">واگذار به...</option>
            </select>
            <button class="btn btn-primary btn-sm" onclick="submitQuickTask()">ثبت</button>
          </div>
        </div>
      </div>
    </section>

    <!-- میز کار و گفتگو -->
    <section class="view" id="view-chat">
      <div class="view-header"><div><h2>میز کار و گفتگو</h2><p>محیط گفتگو و اشتراک‌گذاری ایده‌ها</p></div></div>
      <div class="chat-container">
        <div class="chat-messages" id="chat-messages"></div>
        <div class="chat-input-bar">
          <input id="chat-input" placeholder="پیام خود را بنویسید..." onkeydown="if(event.key==='Enter') sendChatMessage()">
          <button class="btn btn-primary" onclick="sendChatMessage()">
            ارسال
          </button>
        </div>
      </div>
    </section>

    <!-- یادداشت‌ها (ابسیدین استایل با سیستم فولدری و مارک‌دان) -->
    <section class="view" id="view-notes">
      <div class="view-header">
        <div><h2>یادداشت‌های تیمی (ابسیدین استایل)</h2><p>مدیریت با ساختار پوشه‌ای و ویرایشگر پیشرفته مارک‌دان با پشتیبانی آپلود فایل</p></div>
        <div style="display:flex; gap:10px;">
          <button class="btn btn-ghost" onclick="createNewFolderPrompt()">+ پوشه جدید</button>
          <button class="btn btn-primary" onclick="createNewNoteObsidian()">+ یادداشت جدید</button>
        </div>
      </div>

      <div class="obsidian-layout">
        <!-- ستون اول: پوشه‌ها -->
        <div class="obsidian-folders">
          <div class="folder-title">
            <span>📁 پوشه‌ها</span>
          </div>
          <div class="folder-list" id="obsidian-folder-list"></div>
        </div>

        <!-- ستون دوم: لیست یادداشت‌ها -->
        <div class="obsidian-notes-list" id="obsidian-notes-list"></div>

        <!-- ستون سوم: ویرایشگر و پیش‌نمایش -->
        <div class="obsidian-editor">
          <div class="editor-toolbar">
            <div style="display:flex; align-items:center; gap:12px;">
              <input id="note-title-input" placeholder="عنوان یادداشت..." style="background:var(--surface); border:1px solid var(--border); border-radius:8px; padding:8px 14px; color:var(--text); outline:none; font-weight:800;">
              <select id="note-folder-select" style="background:var(--surface); border:1px solid var(--border); border-radius:8px; padding:8px; color:var(--text); outline:none;"></select>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
              <!-- دکمه آپلود تصویر -->
              <input type="file" id="note-file-upload" class="hidden" onchange="uploadFileInNote(this)">
              <button class="btn btn-ghost btn-sm" onclick="document.getElementById('note-file-upload').click()">
                📎 آپلود تصویر
              </button>

              <div class="editor-tabs">
                <button class="btn btn-ghost btn-sm active" id="tab-edit" onclick="setEditorTab('edit')">✍️ ویرایش</button>
                <button class="btn btn-ghost btn-sm" id="tab-preview" onclick="setEditorTab('preview')">👁 پیش‌نمایش</button>
              </div>
              <button class="btn btn-primary btn-sm" onclick="saveActiveNoteObsidian()">ذخیره</button>
            </div>
          </div>
          <div class="editor-main-area">
            <textarea id="note-textarea" class="markdown-editor" placeholder="# یادداشت مارک‌دان خود را در این بخش بنویسید..."></textarea>
            <div id="note-preview-div" class="markdown-preview hidden"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ادمین‌ها -->
    <section class="view" id="view-admins">
      <div class="view-header">
        <div><h2>ادمین‌ها (مدیران کل وردپرس)</h2><p>می‌توانید کاربران جدید را از همین بخش بسازید یا ویرایش کنید</p></div>
        <button class="btn btn-primary" id="add-admin-btn" onclick="openAdminModal()">
          <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"/></svg>
          ادمین جدید
        </button>
      </div>
      <div class="admin-grid" id="admin-grid"></div>
    </section>

    <!-- تنظیمات -->
    <section class="view" id="view-settings">
      <div class="view-header"><div><h2>تنظیمات سیستم</h2><p>اتصال تلگرام و پروفایل شما</p></div></div>

      <div class="settings-card">
        <h3>
          <svg class="svg-icon" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke-linecap="round" stroke-linejoin="round"/></svg>
          وضعیت اتصال تلگرام شما
        </h3>
        <p>ربات تلگرام به صورت خودکار تغییرات را با متصل شدن مستقیم به آیدی عددی تلگرام شما ارسال می‌کند.</p>
        <div class="status-line">
          <span class="status-dot" id="tg-dot"></span>
          <span id="tg-dot-label">در حال بررسی...</span>
        </div>
      </div>

      <!-- پنل تنظیمات پیشرفته سیستم مخصوص ادمین اصلی -->
      <div class="settings-card" id="advanced-settings-card">
        <h3>
          <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke-linecap="round" stroke-linejoin="round"/></svg>
          تنظیمات پیشرفته سیستم
        </h3>
        <p>تمام متغیرها و کلیدهای مورد نیاز تلگرام را می‌توانید مستقیماً از این‌جا مدیریت کنید.</p>

        <div class="row-2">
          <div class="field">
            <label>توکن ربات تلگرام (TELEGRAM_BOT_TOKEN)</label>
            <input id="sys-tg-token" type="password" placeholder="مثلا 123456:ABCdef...">
          </div>
          <div class="field">
            <label>رمز وبهوک تلگرام (TELEGRAM_WEBHOOK_SECRET)</label>
            <input id="sys-tg-webhook" placeholder="یک رمز تصادفی">
          </div>
        </div>
        <div class="row-2" style="grid-template-columns: 1fr;">
          <div class="field">
            <label>آیدی ربات تلگرام (TELEGRAM_BOT_USERNAME)</label>
            <input id="sys-tg-username" placeholder="dr_khaste_bot">
          </div>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
          <button class="btn btn-primary" onclick="saveSystemSettings()">ذخیره تنظیمات سیستم</button>
          <button class="btn btn-ghost" id="app-set-webhook-btn" onclick="triggerAppWebhookSetup()" style="border-color: var(--accent2); color: var(--accent2);">⚡ تنظیم خودکار وبهوک تلگرام ربات</button>
        </div>
      </div>

      <div class="settings-card">
        <h3>
          <svg class="svg-icon" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round"/></svg>
          پروفایل من
        </h3>
        <div class="field"><label>نام نمایشی</label><input id="profile-name"></div>
        <div class="field"><label>رنگ اختصاصی</label><input id="profile-color" type="color"></div>
        <div class="field"><label>رمز عبور جدید (اختیاری)</label><input id="profile-password" type="password" placeholder="خالی بگذارید یعنی بدون تغییر"></div>
        <button class="btn btn-primary" onclick="saveProfile()">ذخیره تغییرات</button>
      </div>
    </section>

  </main>
</div>

<div id="toast-root"></div>

<script>
// Pass WP API variables
const WP_API_URL = "<?php echo esc_url_raw(rest_url('doctor-khaste/v1')); ?>";
const WP_API_NONCE = "<?php echo esc_js(wp_create_nonce('wp_rest')); ?>";

let ME = null;
let TASKS = [];
let NOTES = [];
let ADMINS = [];
let taskFilter = 'all';

// تقویم شمسی
let currentPersianYear = 1403;
let currentPersianMonth = 1; // 1-indexed (فروردین)
let calSelected = null;

let statusChartInstance = null;
let priorityChartInstance = null;

// فولدربندی ابسیدین
let activeFolder = 'عمومی';
let activeNote = null;
let editorTab = 'edit'; // edit or preview

const $ = (id) => document.getElementById(id);

// --- تم دارک و لایت مود ---
function initTheme() {
  const saved = localStorage.getItem('theme');
  if (saved === 'light') {
    document.documentElement.classList.add('light-mode');
    $('theme-btn-text').textContent = 'تم تیره';
    $('theme-btn-icon').innerHTML = '<path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" stroke-linecap="round" stroke-linejoin="round" />';
  } else {
    document.documentElement.classList.remove('light-mode');
    $('theme-btn-text').textContent = 'تم روشن';
    $('theme-btn-icon').innerHTML = '<path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round" />';
  }
}

function toggleTheme() {
  const isLight = document.documentElement.classList.toggle('light-mode');
  localStorage.setItem('theme', isLight ? 'light' : 'dark');
  initTheme();
}

function toggleSidebar() {
  $('sidebar').classList.toggle('open');
}

function closeSidebarOnNavigate() {
  if (window.innerWidth <= 820) {
    $('sidebar').classList.remove('open');
  }
}

function toast(msg, isErr){
  const el = document.createElement('div');
  el.className = 'toast' + (isErr ? ' err' : '');
  el.textContent = msg;
  $('toast-root').appendChild(el);
  setTimeout(()=>el.remove(), 3200);
}

async function api(path, opts){
  const headers = {
    'Content-Type': 'application/json',
    'X-WP-Nonce': WP_API_NONCE
  };
  const res = await fetch(WP_API_URL + path, {
    method: (opts && opts.method) || 'GET',
    headers: headers,
    body: opts && opts.body ? JSON.stringify(opts.body) : undefined,
  });
  const data = await res.json().catch(()=>({}));
  if(!res.ok) throw new Error(data.message || data.error || 'خطا رخ داد');
  return data;
}

function initials(name){ return (name||'?').trim().slice(0,1); }

/* ---------------- بوت ---------------- */
async function boot(){
  initTheme();
  try{
    const m = await api('/me');
    ME = m.admin;
    await enterApp();
  }catch(e){
    toast('مشکل در بارگذاری اطلاعات کاربر: ' + e.message, true);
  }
}

async function enterApp(){
  $('me-avatar').style.background = ME.color;
  $('me-avatar').textContent = initials(ME.name);
  $('me-name').textContent = ME.name;
  $('me-role').textContent = ME.is_super ? 'ادمین اصلی' : 'ادمین';

  loadSystemSettings();
  $('profile-name').value = ME.name;
  $('profile-color').value = ME.color;

  document.querySelectorAll('.nav-item[data-view]').forEach(el=>{
    el.addEventListener('click', ()=> {
      switchView(el.dataset.view);
      closeSidebarOnNavigate();
      if(el.dataset.view === 'chat') {
        loadChatMessages();
      } else if(el.dataset.view === 'notes') {
        renderObsidianNotes();
      }
    });
  });
  document.querySelectorAll('#task-filters .chip').forEach(el=>{
    el.addEventListener('click', ()=>{
      document.querySelectorAll('#task-filters .chip').forEach(c=>c.classList.remove('active'));
      el.classList.add('active'); taskFilter = el.dataset.filter; renderTasks();
    });
  });

  // ست کردن پیش‌فرض ماه تقویم شمسی بر اساس تاریخ امروز
  const todayShamsi = getShamsiDetails(new Date());
  currentPersianYear = todayShamsi.year;
  currentPersianMonth = todayShamsi.month;
  calSelected = todayShamsi.gregorianDateStr;

  await Promise.all([loadTasks(), loadNotes(), loadAdmins(), loadTelegramStatus()]);
  renderOverview();
  renderTasks();
  renderCalendar();
  renderNotes();
  renderAdmins();
  populateAssigneeSelects();
}

function switchView(name){
  document.querySelectorAll('.nav-item[data-view]').forEach(el=>el.classList.toggle('active', el.dataset.view===name));
  document.querySelectorAll('.view').forEach(el=>el.classList.remove('active'));
  $('view-'+name).classList.add('active');
}

/* ---------------- بارگذاری داده ---------------- */
async function loadTasks(){ const r = await api('/tasks'); TASKS = r.tasks; }
async function loadNotes(){ const r = await api('/notes'); NOTES = r.notes; }
async function loadAdmins(){ const r = await api('/admins'); ADMINS = r.admins; }

function populateAssigneeSelects() {
  const options = '<option value="">— واگذار به... —</option>' + ADMINS.map(a=>`<option value="${a.id}">${a.name}</option>`).join('');
  const qAssignee = $('q-assignee');
  if(qAssignee) qAssignee.innerHTML = options;
}

/* ---------------- نمای کلی ---------------- */
function renderOverview(){
  const open = TASKS.filter(t=>t.status!=='done').length;
  const today = new Date().toISOString().slice(0,10);
  const dueToday = TASKS.filter(t=>t.due_date===today && t.status!=='done').length;
  $('stat-grid').innerHTML = `
    <div class="stat-card accent"><div class="num">${open}</div><div class="label">تسک‌های باز</div></div>
    <div class="stat-card danger"><div class="num">${dueToday}</div><div class="label">موعد امروز</div></div>
    <div class="stat-card teal"><div class="num">${NOTES.length}</div><div class="label">یادداشت‌ها</div></div>
    <div class="stat-card"><div class="num">${ADMINS.length}</div><div class="label">ادمین‌ها</div></div>
  `;
  const upcoming = TASKS.filter(t=>t.status!=='done').slice(0,6);
  $('overview-tasks').innerHTML = upcoming.length ? upcoming.map(taskCardHtml).join('') : emptyHtml('همه تسک‌ها انجام شده! 🎉');
  bindTaskCardEvents('overview-tasks');

  // رندر کردن نمودارهای آماری Chart.js
  renderOverviewCharts();
}

function renderOverviewCharts() {
  const pendingCount = TASKS.filter(t => t.status === 'pending').length;
  const inProgressCount = TASKS.filter(t => t.status === 'in_progress').length;
  const doneCount = TASKS.filter(t => t.status === 'done').length;

  const urgentCount = TASKS.filter(t => t.priority === 'urgent').length;
  const highCount = TASKS.filter(t => t.priority === 'high').length;
  const normalCount = TASKS.filter(t => t.priority === 'normal').length;
  const lowCount = TASKS.filter(t => t.priority === 'low').length;

  // Status Chart (Doughnut)
  if (statusChartInstance) statusChartInstance.destroy();
  const ctx1 = $('statusChart').getContext('2d');
  statusChartInstance = new Chart(ctx1, {
    type: 'doughnut',
    data: {
      labels: ['در انتظار', 'در حال انجام', 'انجام‌شده'],
      datasets: [{
        data: [pendingCount, inProgressCount, doneCount],
        backgroundColor: ['#f59e0b', '#10b981', '#10b981'],
        borderWidth: 1,
        borderColor: 'var(--surface-2)'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: 'var(--text)', font: { family: 'Vazirmatn', size: 11 } }
        }
      }
    }
  });

  // Priority Chart (Bar)
  if (priorityChartInstance) priorityChartInstance.destroy();
  const ctx2 = $('priorityChart').getContext('2d');
  priorityChartInstance = new Chart(ctx2, {
    type: 'bar',
    data: {
      labels: ['فوری', 'بالا', 'عادی', 'کم'],
      datasets: [{
        label: 'تعداد تسک‌ها',
        data: [urgentCount, highCount, normalCount, lowCount],
        backgroundColor: ['#ef4444', '#f59e0b', '#10b981', '#9ca3af'],
        borderWidth: 0,
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: 'var(--text)', font: { family: 'Vazirmatn' } }
        },
        y: {
          grid: { color: 'var(--border)' },
          ticks: { color: 'var(--text)', font: { family: 'Vazirmatn' }, precision: 0 }
        }
      }
    }
  });
}

/* ---------------- تسک‌ها ---------------- */
function priorityLabel(p){ return {urgent:'فوری',high:'بالا',normal:'عادی',low:'کم'}[p]||'عادی'; }

function taskCardHtml(t){
  const doneCls = t.status==='done' ? 'done' : '';
  let shamsiLabel = '';
  if (t.due_date) {
    const sh = gregorianToShamsi(t.due_date);
    if (sh) shamsiLabel = `${sh.year}/${sh.month}/${sh.day}`;
  }

  return `
  <div class="task-card" data-id="${t.id}">
    <div class="task-check ${doneCls}" data-action="toggle"></div>
    <div class="task-body">
      <div class="task-title ${doneCls}">${escapeHtml(t.title)}</div>
      <div class="task-meta">
        <span class="badge pr-${t.priority}">${priorityLabel(t.priority)}</span>
        ${t.due_date ? '<span><svg class="svg-icon-inline" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round"/></svg> ' + shamsiLabel + ' (' + t.due_date + ')</span>' : ''}
        ${t.assignee_name ? '<span><span class="dot" style="display:inline-block;background:'+t.assignee_color+'"></span> '+t.assignee_name+'</span>' : '<span>بدون واگذاری</span>'}
        <span>ثبت: ${t.creator_name||''}</span>
      </div>
    </div>
    <div class="task-actions">
      <button class="icon-btn" data-action="edit" title="ویرایش">
        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </button>
      <button class="icon-btn danger" data-action="delete" title="حذف">
        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </button>
    </div>
  </div>`;
}

function emptyHtml(msg){
  return `<div class="empty-state">
    <svg class="svg-icon-large" viewBox="0 0 24 24"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <div>${msg}</div>
  </div>`;
}

function renderTasks(){
  let list = TASKS;
  if(taskFilter!=='all') list = list.filter(t=>t.status===taskFilter);
  $('task-list').innerHTML = list.length ? list.map(taskCardHtml).join('') : emptyHtml('تسکی یافت نشد.');
  bindTaskCardEvents('task-list');
}

function bindTaskCardEvents(containerId){
  document.querySelectorAll('#'+containerId+' .task-card').forEach(card=>{
    const id = Number(card.dataset.id);
    const t = TASKS.find(x=>x.id===id);
    card.querySelector('[data-action=toggle]').addEventListener('click', async ()=>{
      const newStatus = t.status==='done' ? 'pending' : 'done';
      await api('/tasks/'+id, {method:'PUT', body:{status:newStatus}});
      await loadTasks(); renderOverview(); renderTasks(); renderCalendar();
    });
    card.querySelector('[data-action=edit]').addEventListener('click', ()=> openTaskModal(t));
    card.querySelector('[data-action=delete]').addEventListener('click', async ()=>{
      if(!confirm('این تسک حذف شود؟')) return;
      await api('/tasks/'+id, {method:'DELETE'});
      await loadTasks(); renderOverview(); renderTasks(); renderCalendar();
    });
  });
}

function openTaskModal(t){
  const isEdit = !!t;
  const assigneeOptions = ADMINS.map(a=>`<option value="${a.id}" ${t&&t.assigned_to===a.id?'selected':''}>${a.name}</option>`).join('');

  // لود تاریخ به فرمت شمسی در مودال
  let currentSh = { year: 1403, month: 1, day: 1 };
  if (t && t.due_date) {
    const extracted = gregorianToShamsi(t.due_date);
    if (extracted) currentSh = extracted;
  } else {
    currentSh = getShamsiDetails(new Date());
  }

  const daysOptions = Array.from({length:31}, (_, i)=>`<option value="${i+1}" ${currentSh.day===i+1?'selected':''}>${i+1}</option>`).join('');
  const monthsOptions = PERSIAN_MONTH_NAMES.map((m, i)=>`<option value="${i+1}" ${currentSh.month===i+1?'selected':''}>${m}</option>`).join('');
  const yearsOptions = Array.from({length:11}, (_, i)=>`<option value="${1400+i}" ${currentSh.year===(1400+i)?'selected':''}>${1400+i}</option>`).join('');

  openModal(`
    <h3>${isEdit?'ویرایش تسک':'تسک جدید'}</h3>
    <div class="field"><label>عنوان</label><input id="m-title" value="${t?escapeAttr(t.title):''}"></div>
    <div class="field"><label>توضیحات</label><textarea id="m-desc" rows="3">${t?escapeHtml(t.description||''):''}</textarea></div>

    <label style="display:block; font-size:13.5px; color:var(--text-dim); margin-bottom:8px; font-weight:600;">موعد (تاریخ شمسی)</label>
    <div class="row-2" style="grid-template-columns: 1fr 1fr 1fr; margin-bottom:18px;">
      <div class="field" style="margin-bottom:0;"><select id="m-sh-day">${daysOptions}</select></div>
      <div class="field" style="margin-bottom:0;"><select id="m-sh-month">${monthsOptions}</select></div>
      <div class="field" style="margin-bottom:0;"><select id="m-sh-year">${yearsOptions}</select></div>
    </div>

    <div class="row-2">
      <div class="field"><label>اولویت</label>
        <select id="m-priority">
          ${['low','normal','high','urgent'].map(p=>`<option value="${p}" ${t&&t.priority===p?'selected':''}>${priorityLabel(p)}</option>`).join('')}
        </select>
      </div>
      <div class="field"><label>واگذار به</label>
        <select id="m-assignee"><option value="">— بدون واگذاری —</option>${assigneeOptions}</select>
      </div>
    </div>

    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeModal()">انصراف</button>
      <button class="btn btn-primary" id="m-save">ذخیره</button>
    </div>
  `);

  $('m-save').addEventListener('click', async ()=>{
    const shYear = Number($('m-sh-year').value);
    const shMonth = Number($('m-sh-month').value);
    const shDay = Number($('m-sh-day').value);
    const due_date = shamsiToGregorian(shYear, shMonth, shDay);

    const body = {
      title: $('m-title').value.trim(),
      description: $('m-desc').value,
      due_date: due_date,
      priority: $('m-priority').value,
      assigned_to: $('m-assignee').value ? Number($('m-assignee').value) : null,
    };
    if(!body.title){ toast('عنوان الزامی است', true); return; }
    try{
      if(isEdit) await api('/tasks/'+t.id, {method:'PUT', body});
      else await api('/tasks', {method:'POST', body});
      closeModal();
      await loadTasks(); renderOverview(); renderTasks(); renderCalendar();
      toast('ذخیره شد ✅');
    }catch(e){ toast(e.message, true); }
  });
}

/* ---------------- تقویم خورشیدی ایران با متد بومی ICU ---------------- */
const DOW = ["ش", "ی", "د", "س", "چ", "پ", "ج"];
const PERSIAN_MONTH_NAMES = [
  "فروردین", "اردیبهشت", "خرداد",
  "تیر", "مرداد", "شهریور",
  "مهر", "آبان", "آذر",
  "دی", "بهمن", "اسفند"
];

function getShamsiDetails(date) {
  const parts = new Intl.DateTimeFormat("en-US-u-ca-persian", {
    year: "numeric", month: "numeric", day: "numeric", timeZone: "UTC"
  }).formatToParts(date);
  const year = parseInt(parts.find(p => p.type === "year").value, 10);
  const month = parseInt(parts.find(p => p.type === "month").value, 10);
  const day = parseInt(parts.find(p => p.type === "day").value, 10);
  return { year, month, day, gregorianDateStr: date.toISOString().slice(0,10) };
}

function gregorianToShamsi(gDateStr) {
  if (!gDateStr) return null;
  const parts = gDateStr.split("-");
  const date = new Date(Date.UTC(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2])));
  return getShamsiDetails(date);
}

function shamsiToGregorian(jy, jm, jd) {
  let approxGregYear = jy + 621;
  let gMonth = 0;
  if (jm <= 10) {
    gMonth = jm + 1;
  } else {
    approxGregYear = jy + 622;
    gMonth = jm - 11;
  }

  let baseDate = new Date(Date.UTC(approxGregYear, gMonth, jd + 19));
  let scanStart = new Date(baseDate.getTime() - 10 * 24 * 60 * 60 * 1000);
  for (let i = 0; i < 20; i++) {
    const testDate = new Date(scanStart.getTime() + i * 24 * 60 * 60 * 1000);
    const details = getShamsiDetails(testDate);
    if (details.year === jy && details.month === jm && details.day === jd) {
      return details.gregorianDateStr;
    }
  }
  return null;
}

// فرمت دقیق تاریخ خورشیدی به همراه ساعت برای پیام‌های میزکار
function formatMessageDateTime(createdAtStr) {
  if (!createdAtStr) return '';
  try {
    const cleanStr = createdAtStr.replace(' ', 'T') + (createdAtStr.includes('Z') ? '' : 'Z');
    const d = new Date(cleanStr);
    const sh = getShamsiDetails(d);
    const pad = (n) => String(n).padStart(2, '0');
    const hours = pad(d.getHours());
    const minutes = pad(d.getMinutes());
    return `${sh.year}/${sh.month}/${sh.day} ساعت ${hours}:${minutes}`;
  } catch (e) {
    return createdAtStr;
  }
}

// تولید روزهای یک ماه شمسی خاص
function getPersianMonthDays(year, month) {
  let approxGregYear = year + 621;
  let gMonth = 0;
  if (month === 1) { gMonth = 2; }
  else if (month === 2) { gMonth = 3; }
  else if (month === 3) { gMonth = 4; }
  else if (month === 4) { gMonth = 5; }
  else if (month === 5) { gMonth = 6; }
  else if (month === 6) { gMonth = 7; }
  else if (month === 7) { gMonth = 8; }
  else if (month === 8) { gMonth = 9; }
  else if (month === 9) { gMonth = 10; }
  else if (month === 10) { gMonth = 11; }
  else if (month === 11) { approxGregYear = year + 622; gMonth = 0; }
  else if (month === 12) { approxGregYear = year + 622; gMonth = 1; }

  const baseDate = new Date(Date.UTC(approxGregYear, gMonth, 15));

  let day1Gregorian = null;
  for (let i = 0; i < 15; i++) {
    const testDate = new Date(baseDate.getTime() + i * 24 * 60 * 60 * 1000);
    const details = getShamsiDetails(testDate);
    if (details.year === year && details.month === month && details.day === 1) {
      day1Gregorian = testDate;
      break;
    }
  }

  const days = [];
  let current = new Date(day1Gregorian);
  while (true) {
    const details = getShamsiDetails(current);
    if (details.year !== year || details.month !== month) {
      break;
    }
    days.push({
      persianDay: details.day,
      gregorianDateStr: details.gregorianDateStr,
      gregorianDate: new Date(current)
    });
    current = new Date(current.getTime() + 24 * 60 * 60 * 1000);
  }
  return days;
}

function getPersianWeekdayIndex(gregorianDate) {
  const d = gregorianDate.getDay(); // 0 is Sunday, 6 is Saturday
  return (d + 1) % 7; // نگاشت شنبه به 0، یکشنبه به 1، و جمعه به 6
}

function renderCalendar(){
  $('cal-dow').innerHTML = DOW.map(d=>`<div class="cal-dow">${d}</div>`).join('');
  $('cal-title').textContent = `${PERSIAN_MONTH_NAMES[currentPersianMonth - 1]} ${currentPersianYear}`;

  const days = getPersianMonthDays(currentPersianYear, currentPersianMonth);
  const firstDay = days[0];
  const startOffset = getPersianWeekdayIndex(firstDay.gregorianDate);

  const cells = [];
  // خانه‌های خالی ماه قبل
  for(let i=0; i<startOffset; i++) {
    cells.push({ other: true });
  }
  // روزهای اصلی ماه
  for(const d of days) {
    cells.push({ ...d, other: false });
  }
  // پر کردن خانه‌های آخر جدول تا مضرب ۷
  while(cells.length % 7 !== 0) {
    cells.push({ other: true });
  }

  const todayStr = new Date().toISOString().slice(0,10);
  $('cal-grid').innerHTML = cells.map(c=>{
    if(c.other) return `<div class="cal-day other-month"></div>`;
    const dayTasks = TASKS.filter(t=>t.due_date===c.gregorianDateStr);
    const isToday = c.gregorianDateStr===todayStr;
    const isSel = c.gregorianDateStr===calSelected;
    const dots = dayTasks.slice(0,4).map(t=>`<span class="dot pr-${t.priority}" style="background:currentColor"></span>`).join('');
    return `<div class="cal-day ${isToday?'today':''} ${isSel?'selected':''}" data-date="${c.gregorianDateStr}"><span>${c.persianDay}</span><div class="cal-dots">${dots}</div></div>`;
  }).join('');

  document.querySelectorAll('.cal-day[data-date]').forEach(el=>{
    el.addEventListener('click', ()=>{ calSelected = el.dataset.date; renderCalendar(); renderCalDayTasks(); });
  });
  if(!calSelected) calSelected = todayStr;
  renderCalDayTasks();
}

function renderCalDayTasks(){
  const sh = gregorianToShamsi(calSelected);
  const shLabel = sh ? `${sh.year}/${sh.month}/${sh.day}` : calSelected;
  $('cal-day-label').textContent = 'تسک‌های روز ' + shLabel + ' (' + calSelected + ')';

  const list = TASKS.filter(t=>t.due_date===calSelected);
  $('cal-day-tasks').innerHTML = list.length ? list.map(taskCardHtml).join('') : emptyHtml('تسکی برای این روز نیست.');
  bindTaskCardEvents('cal-day-tasks');
}

function calShift(dir){
  currentPersianMonth += dir;
  if (currentPersianMonth > 12) {
    currentPersianMonth = 1;
    currentPersianYear += 1;
  } else if (currentPersianMonth < 1) {
    currentPersianMonth = 12;
    currentPersianYear -= 1;
  }
  renderCalendar();
}

// ثبت سریع تسک برای روز انتخاب شده در تقویم
async function submitQuickTask() {
  const title = $('q-title').value.trim();
  const priority = $('q-priority').value;
  const assignee = $('q-assignee').value ? Number($('q-assignee').value) : null;

  if (!title) {
    toast('لطفاً عنوان تسک را وارد کنید.', true);
    return;
  }

  const body = {
    title,
    description: 'ثبت شده از تقویم برای تاریخ ' + calSelected,
    due_date: calSelected,
    priority,
    assigned_to: assignee
  };

  try {
    await api('/tasks', { method: 'POST', body });
    $('q-title').value = '';
    await loadTasks();
    renderOverview();
    renderTasks();
    renderCalendar();
    toast('تسک جدید ثبت و به تقویم اضافه شد! ✅');
  } catch (e) {
    toast(e.message, true);
  }
}

async function triggerAppWebhookSetup() {
  const btn = $('app-set-webhook-btn');
  btn.disabled = true;
  toast('در حال ارسال دستور به تلگرام...');
  try {
    const res = await api('/telegram/set-webhook', { method: 'POST' });
    if (res.ok) {
      toast('وبهوک با موفقیت روی تلگرام ست شد! ✅');
    } else {
      throw new Error(res.message || 'مشکل در تنظیم وبهوک');
    }
  } catch (e) {
    toast('خطا: ' + e.message, true);
  } finally {
    btn.disabled = false;
  }
}

/* ---------------- میز کار و گفتگوی تیمی ---------------- */
async function loadChatMessages() {
  try {
    const res = await api('/messages');
    const messages = res.messages || [];
    const container = $('chat-messages');

    container.innerHTML = messages.length ? messages.map(m => {
      const isMe = m.admin_id === ME.id;
      const bubbleClass = 'chat-bubble' + (isMe ? ' me' : '');
      const timeStr = formatMessageDateTime(m.created_at); // نمایش تاریخ خورشیدی به همراه ساعت دقیق

      return `
        <div class="${bubbleClass}" style="--bubble-color: ${m.sender_color}">
          <div class="chat-meta-info">
            <span style="font-weight:700; color:${m.sender_color}">${escapeHtml(m.sender_name)}</span>
            <span>${timeStr}</span>
          </div>
          <div class="chat-content-text">${escapeHtml(m.content)}</div>
        </div>
      `;
    }).join('') : `<div class="empty-state" style="padding: 24px;">هیچ پیامی در این گفتگو ثبت نشده است. شروع به مکالمه کنید! 💬</div>`;

    // Auto-scroll to bottom
    container.scrollTop = container.scrollHeight;
  } catch (e) {
    toast('خطا در بارگذاری پیام‌ها: ' + e.message, true);
  }
}

async function sendChatMessage() {
  const input = $('chat-input');
  const content = input.value.trim();
  if (!content) return;

  try {
    await api('/messages', { method: 'POST', body: { content } });
    input.value = '';
    await loadChatMessages();
  } catch (e) {
    toast(e.message, true);
  }
}

/* ---------------- یادداشت‌ها (ابسیدین استایل با سیستم فولدری و مارک‌دان) ---------------- */
function renderObsidianNotes() {
  // ۱. استخراج پوشه‌ها
  const folders = [...new Set(NOTES.map(n => n.folder || 'عمومی'))];
  if (!folders.includes('عمومی')) folders.push('عمومی');
  if (!folders.includes(activeFolder)) folders.push(activeFolder);

  // ۲. رندر کردن لیست پوشه‌ها در ستون اول
  $('obsidian-folder-list').innerHTML = folders.map(f => {
    const count = NOTES.filter(n => (n.folder || 'عمومی') === f).length;
    const activeClass = f === activeFolder ? 'active' : '';
    return `
      <div class="folder-item ${activeClass}" onclick="setActiveFolderObsidian('${escapeAttr(f)}')">
        <span>📁 ${escapeHtml(f)}</span>
        <span class="folder-badge">${count}</span>
      </div>
    `;
  }).join('');

  // ۳. رندر کردن لیست یادداشت‌های پوشه فعال در ستون دوم
  const folderNotes = NOTES.filter(n => (n.folder || 'عمومی') === activeFolder);
  $('obsidian-notes-list').innerHTML = folderNotes.length ? folderNotes.map(n => {
    const activeClass = activeNote && activeNote.id === n.id ? 'active' : '';
    const contentSneak = n.content ? n.content.slice(0, 45) : 'یادداشت خالی...';
    return `
      <div class="obsidian-card ${activeClass}" onclick="setActiveNoteObsidian(${n.id})">
        <h4>${escapeHtml(n.title)}</h4>
        <p>${escapeHtml(contentSneak)}</p>
      </div>
    `;
  }).join('') : `<div class="empty-state" style="font-size:12px; padding:24px;">یادداشتی در این پوشه نیست.</div>`;

  // ۴. پر کردن سلکتور پوشه‌ها در ویرایشگر
  $('note-folder-select').innerHTML = folders.map(f => `<option value="${escapeAttr(f)}" ${activeFolder === f ? 'selected' : ''}>${escapeHtml(f)}</option>`).join('');

  // ۵. به‌روزرسانی محتوای ویرایشگر بر اساس یادداشت فعال
  if (activeNote) {
    $('note-title-input').value = activeNote.title;
    $('note-textarea').value = activeNote.content || '';
    $('note-folder-select').value = activeNote.folder || 'عمومی';
  } else {
    $('note-title-input').value = '';
    $('note-textarea').value = '';
  }

  updateMarkdownPreview();
}

function setActiveFolderObsidian(folderName) {
  activeFolder = folderName;
  activeNote = null;
  renderObsidianNotes();
}

function setActiveNoteObsidian(noteId) {
  activeNote = NOTES.find(n => n.id === noteId);
  renderObsidianNotes();
}

function setEditorTab(tab) {
  editorTab = tab;
  $('tab-edit').classList.toggle('active', tab === 'edit');
  $('tab-preview').classList.toggle('active', tab === 'preview');

  if (tab === 'preview') {
    updateMarkdownPreview();
    $('note-textarea').classList.add('hidden');
    $('note-preview-div').classList.remove('hidden');
  } else {
    $('note-textarea').classList.remove('hidden');
    $('note-preview-div').classList.add('hidden');
  }
}

function updateMarkdownPreview() {
  const content = $('note-textarea').value;
  // رندر مارک‌دان با marked
  try {
    $('note-preview-div').innerHTML = marked.parse(content || '*پیش‌نمایش خالی است*');
  } catch(e) {
    $('note-preview-div').innerHTML = content;
  }
}

function createNewFolderPrompt() {
  const f = prompt('لطفاً نام پوشه جدید را وارد کنید:');
  if (f && f.trim()) {
    activeFolder = f.trim();
    renderObsidianNotes();
    toast('پوشه جدید ایجاد شد 📁');
  }
}

function createNewNoteObsidian() {
  activeNote = null;
  $('note-title-input').value = 'یادداشت بدون عنوان';
  $('note-textarea').value = '';
  setEditorTab('edit');
  renderObsidianNotes();
}

async function saveActiveNoteObsidian() {
  const title = $('note-title-input').value.trim();
  const content = $('note-textarea').value;
  const folder = $('note-folder-select').value;

  if (!title) {
    toast('عنوان یادداشت الزامی است.', true);
    return;
  }

  const body = { title, content, folder };

  try {
    let noteId = activeNote ? activeNote.id : null;
    if (activeNote) {
      await api('/notes/' + activeNote.id, { method: 'PUT', body });
      toast('یادداشت بروزرسانی شد ✅');
    } else {
      const res = await api('/notes', { method: 'POST', body });
      noteId = res.id;
      toast('یادداشت جدید ذخیره شد ✅');
    }
    await loadNotes();
    if (noteId) {
      activeNote = NOTES.find(n => n.id === noteId);
    }
    renderObsidianNotes();
    renderNotes();
  } catch (e) {
    toast(e.message, true);
  }
}

// آپلود فایل ضمیمه در حین ویرایش یادداشت مارک‌دان
async function uploadFileInNote(inputEl) {
  const file = inputEl.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = async () => {
    const dataBase64 = reader.result;
    toast('در حال آپلود تصویر...');
    try {
      const res = await api('/upload', {
        method: 'POST',
        body: { filename: file.name, dataBase64 }
      });
      // افزودن تگ مارک‌دان تصویر به ویرایشگر
      const mdTag = `\n\n![${file.name}](${res.url})\n\n`;
      const textarea = $('note-textarea');
      const start = textarea.selectionStart;
      const end = textarea.selectionEnd;
      const text = textarea.value;
      textarea.value = text.substring(0, start) + mdTag + text.substring(end);
      textarea.focus();
      updateMarkdownPreview();
      toast('تصویر با موفقیت آپلود و پیوست شد! 🚀');
    } catch (e) {
      toast('خطا در آپلود تصویر: ' + e.message, true);
    }
  };
  reader.readAsDataURL(file);
}

// رندر قدیمی کارت‌های یادداشت ساده
function renderNotes(){
  // این تابع برای حفظ همخوانی با نمای کلی وجود دارد
}

/* ---------------- ادمین‌ها ---------------- */
function renderAdmins(){
  $('admin-grid').innerHTML = ADMINS.map(a=>`
    <div class="admin-card">
      <div class="avatar" style="background:${a.color}">${initials(a.name)}</div>
      <div style="font-weight:800;font-size:15px;">${escapeHtml(a.name)}</div>
      <div style="color:var(--text-dim);font-size:12.5px;">@${escapeHtml(a.username)}</div>
      ${a.is_super?'<div class="super-tag">مدیر کل</div>':''}
      ${a.telegram_chat_id ? `<div style="margin-top:10px;font-size:12px;color:var(--success);">🔗 تلگرام: ${a.telegram_chat_id}</div>` : '<div style="margin-top:10px;font-size:12px;color:var(--text-dim);">⚠️ تلگرام وصل نیست</div>'}
      <div style="display:flex; gap:8px; justify-content:center; margin-top:16px;">
        <button class="btn btn-ghost btn-sm" onclick="openEditAdminModal(${a.id})">ویرایش</button>
        ${a.id !== ME.id ? `<button class="btn btn-danger btn-sm" onclick="deleteAdmin(${a.id})">حذف</button>` : ''}
      </div>
    </div>
  `).join('');
}

async function deleteAdmin(id){
  if(!confirm('این ادمین حذف شود؟')) return;
  try{ await api('/admins/'+id, {method:'DELETE'}); await loadAdmins(); renderAdmins(); toast('حذف شد'); }
  catch(e){ toast(e.message, true); }
}

function openAdminModal(){
  openModal(`
    <h3>ادمین جدید</h3>
    <div class="field"><label>نام نمایشی</label><input id="a-name"></div>
    <div class="field"><label>نام کاربری</label><input id="a-username"></div>
    <div class="field"><label>رمز عبور</label><input id="a-password" type="password"></div>
    <div class="field"><label>رنگ اختصاصی</label><input id="a-color" type="color" value="#4fd1c5"></div>
    <div class="field"><label>آیدی عددی تلگرام (اختیاری)</label><input id="a-tg-id" placeholder="مثلا 123456789"></div>
    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeModal()">انصراف</button>
      <button class="btn btn-primary" id="a-save">ساخت ادمین</button>
    </div>
  `);
  $('a-save').addEventListener('click', async ()=>{
    const body = {
      name:$('a-name').value.trim(),
      username:$('a-username').value.trim(),
      password:$('a-password').value,
      color:$('a-color').value,
      telegram_chat_id: $('a-tg-id').value.trim() || null
    };
    if(!body.name||!body.username||!body.password){ toast('همه فیلدها الزامی است', true); return; }
    try{ await api('/admins', {method:'POST', body}); closeModal(); await loadAdmins(); renderAdmins(); toast('ادمین ساخته شد ✅'); }
    catch(e){ toast(e.message, true); }
  });
}

function openEditAdminModal(adminId) {
  const a = ADMINS.find(x => x.id === adminId);
  if (!a) return;
  openModal(`
    <h3>ویرایش ادمین: ${escapeHtml(a.name)}</h3>
    <div class="field"><label>نام نمایشی</label><input id="ea-name" value="${escapeAttr(a.name)}"></div>
    <div class="field"><label>رنگ اختصاصی</label><input id="ea-color" type="color" value="${a.color}"></div>
    <div class="field"><label>رمز عبور جدید (اختیاری)</label><input id="ea-password" type="password" placeholder="خالی بگذارید یعنی بدون تغییر"></div>
    <div class="field"><label>آیدی عددی تلگرام</label><input id="ea-tg-id" value="${a.telegram_chat_id || ''}" placeholder="مثلا 123456789"></div>
    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeModal()">انصراف</button>
      <button class="btn btn-primary" id="ea-save">ذخیره تغییرات</button>
    </div>
  `);
  $('ea-save').addEventListener('click', async () => {
    const body = {
      name: $('ea-name').value.trim(),
      color: $('ea-color').value,
      telegram_chat_id: $('ea-tg-id').value.trim() || null
    };
    const pass = $('ea-password').value;
    if (pass) body.password = pass;
    try {
      await api('/admins/' + adminId, { method: 'PUT', body });
      closeModal();
      await loadAdmins();
      renderAdmins();
      toast('تغییرات ادمین ذخیره شد ✅');
    } catch (e) {
      toast(e.message, true);
    }
  });
}

/* ---------------- تنظیمات: تلگرام / پروفایل / سیستم پیشرفته ---------------- */
async function loadTelegramStatus(){
  const on = !!ME.telegram_chat_id;
  $('tg-dot').className = 'status-dot ' + (on?'on':'off');
  $('tg-dot-label').innerHTML = on ? `متصل به آیدی عددی تلگرام شما: <code>${ME.telegram_chat_id}</code>` : 'آیدی تلگرام شما هنوز تنظیم نشده است. از منوی ادمین‌ها آیدی خود را وارد کنید.';
}

// مدیریت متغیرهای سیستم از داخل اپلیکیشن
async function loadSystemSettings() {
  try {
    const res = await api('/settings');
    const s = res.settings || {};
    $('sys-tg-token').value = s.TELEGRAM_BOT_TOKEN || '';
    $('sys-tg-webhook').value = s.TELEGRAM_WEBHOOK_SECRET || '';
    $('sys-tg-username').value = s.TELEGRAM_BOT_USERNAME || '';
  } catch (e) {
    toast('خطا در بارگذاری تنظیمات سیستم: ' + e.message, true);
  }
}

async function saveSystemSettings() {
  const body = {
    TELEGRAM_BOT_TOKEN: $('sys-tg-token').value,
    TELEGRAM_WEBHOOK_SECRET: $('sys-tg-webhook').value,
    TELEGRAM_BOT_USERNAME: $('sys-tg-username').value
  };
  try {
    await api('/settings', { method: 'POST', body });
    toast('تنظیمات پیشرفته سیستم با موفقیت ذخیره شد ✅');
  } catch (e) {
    toast(e.message, true);
  }
}

async function saveProfile(){
  const body = { name: $('profile-name').value.trim(), color: $('profile-color').value };
  const pass = $('profile-password').value;
  if(pass) body.password = pass;
  try{
    await api('/admins/'+ME.id, {method:'PUT', body});
    ME.name = body.name; ME.color = body.color;
    $('me-name').textContent = ME.name; $('me-avatar').style.background = ME.color; $('me-avatar').textContent = initials(ME.name);
    $('profile-password').value='';
    toast('پروفایل ذخیره شد ✅');
  }catch(e){ toast(e.message, true); }
}

/* ---------------- مودال عمومی ---------------- */
function openModal(html){
  closeModal();
  const overlay = document.createElement('div');
  overlay.className = 'modal-overlay';
  overlay.id = 'modal-overlay';
  overlay.innerHTML = '<div class="modal">'+html+'</div>';
  overlay.addEventListener('click', (e)=>{ if(e.target===overlay) closeModal(); });
  document.body.appendChild(overlay);
  return overlay;
}
function closeModal(){ const el = $('modal-overlay'); if(el) el.remove(); }

function escapeHtml(s){ return (s||'').replace(/[&<>]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c])); }
function escapeAttr(s){ return escapeHtml(s).replace(/"/g,'&quot;'); }

boot();
</script>
</body>
</html>
