# Sci-Fi / FUI (Fictional User Interface) Design System

## Overview

This document defines the design language for creating futuristic, data-dense, mission-critical user interfaces inspired by sci-fi film UI, military command centers, and advanced system monitoring applications. The aesthetic prioritizes information density, technical authenticity, and atmospheric immersion.

---

## 1. Core Design Philosophy

### 1.1 Guiding Principles

| Principle | Description |
|-----------|-------------|
| **Data Density** | Maximize information per viewport. Every pixel should convey meaning. |
| **Functional Mystique** | Elements should appear complex and purposeful, even if decorative. |
| **Ambient Intelligence** | The UI should feel "alive" with subtle animations, flickering data, and state changes. |
| **Militaristic Precision** | Clean alignments, grid-based layouts, hierarchical information architecture. |
| **Degraded Futurism** | Perfect technology with imperfect rendering—scan lines, noise, glitches suggest analog roots. |

### 1.2 Narrative Context

Design as if the interface is:
- A mission-critical system where failure means catastrophe
- Operated by trained specialists who understand cryptic abbreviations
- Running on advanced but battle-worn hardware
- Displaying real-time telemetry from complex systems

---

## 2. Color System

### 2.1 Primary Palette

```css
:root {
  /* Base/Background Colors */
  --color-void: #000000;           /* Pure black - deepest background */
  --color-abyss: #0a0a0f;          /* Near-black with blue undertone */
  --color-deep-space: #0d1117;     /* Primary background */
  --color-dark-matter: #151921;    /* Elevated surfaces */
  --color-carbon: #1a1f2e;         /* Cards, panels */
  --color-gunmetal: #252a36;       /* Interactive surface hover */
  
  /* Primary Accent - Cyan/Teal (Primary UI Color) */
  --color-primary-100: #e0fcff;
  --color-primary-200: #b8f5fa;
  --color-primary-300: #7ee8f1;
  --color-primary-400: #45d9e8;
  --color-primary-500: #00d4ff;    /* PRIMARY - Key accent */
  --color-primary-600: #00b4d8;
  --color-primary-700: #0096b4;
  --color-primary-800: #007a94;
  --color-primary-900: #005f73;
  
  /* Secondary Accent - Teal/Green (Confirmations, Online States) */
  --color-secondary-500: #00ffcc;
  --color-secondary-600: #00d9a8;
  --color-secondary-700: #00b389;
  
  /* Text Colors */
  --color-text-primary: #e8f4f8;   /* Primary text - slightly cyan-tinted white */
  --color-text-secondary: #8fa4b0; /* Secondary/dimmed text */
  --color-text-tertiary: #4a5d6a;  /* Disabled, hints */
  --color-text-accent: #00d4ff;    /* Highlighted text */
}
```

### 2.2 Semantic/Status Colors

```css
:root {
  /* Status Colors */
  --color-success: #00ff88;        /* Online, complete, nominal */
  --color-success-dim: #00cc6a;
  
  --color-warning: #ffaa00;        /* Caution, pending, loading */
  --color-warning-dim: #cc8800;
  --color-warning-hot: #ff6600;    /* Elevated warning */
  
  --color-error: #ff3355;          /* Critical, offline, failure */
  --color-error-dim: #cc2944;
  --color-error-glow: #ff0040;     /* Pulsing alerts */
  
  --color-info: #4488ff;           /* Informational, selected */
  --color-info-dim: #3366cc;
  
  /* Special States */
  --color-scanning: #aa66ff;       /* Processing, scanning */
  --color-encrypted: #ff66aa;      /* Secure, encrypted, locked */
  --color-standby: #666688;        /* Idle, standby, inactive */
}
```

### 2.3 Color Application Rules

1. **Background Layering**: Use progressively lighter backgrounds to indicate elevation
   - Base layer: `--color-abyss`
   - Panel layer: `--color-deep-space`
   - Card layer: `--color-dark-matter`
   - Modal layer: `--color-carbon`

2. **Accent Usage**: Primary cyan (`--color-primary-500`) should comprise ~10-15% of visible UI
   - Borders of active/focused elements
   - Key data values
   - Interactive element highlights
   - Section headers

3. **Status Colors**: Use sparingly and consistently
   - Green: System nominal, connection active, process complete
   - Orange/Amber: Warning states, pending operations
   - Red: Errors, disconnections, critical alerts
   - Purple: Scanning, processing, in-progress operations

---

## 3. Typography

### 3.1 Font Stack

```css
:root {
  /* Primary Monospace - For data, code, values */
  --font-mono: 'JetBrains Mono', 'Fira Code', 'SF Mono', 'Consolas', 'Monaco', monospace;
  
  /* Secondary Monospace - Condensed for dense data */
  --font-mono-condensed: 'IBM Plex Mono', 'Roboto Mono', monospace;
  
  /* Display/Headers - Technical sans-serif */
  --font-display: 'Orbitron', 'Eurostile', 'Bank Gothic', 'Rajdhani', sans-serif;
  
  /* UI Labels - Clean, readable */
  --font-ui: 'Inter', 'SF Pro Display', 'Segoe UI', system-ui, sans-serif;
  
  /* Fallback for sci-fi headers if custom fonts unavailable */
  --font-tech: 'Share Tech Mono', 'VT323', 'Courier New', monospace;
}
```

### 3.2 Type Scale

```css
:root {
  /* Size Scale */
  --text-3xs: 0.625rem;   /* 10px - Micro labels, timestamps */
  --text-2xs: 0.6875rem;  /* 11px - Dense data tables */
  --text-xs: 0.75rem;     /* 12px - Secondary labels, metadata */
  --text-sm: 0.8125rem;   /* 13px - Body text, descriptions */
  --text-base: 0.875rem;  /* 14px - Primary UI text */
  --text-md: 1rem;        /* 16px - Section headers */
  --text-lg: 1.125rem;    /* 18px - Panel titles */
  --text-xl: 1.25rem;     /* 20px - Major headers */
  --text-2xl: 1.5rem;     /* 24px - Page titles */
  --text-3xl: 2rem;       /* 32px - Hero displays */
  --text-4xl: 3rem;       /* 48px - Large numerical readouts */
  
  /* Line Heights */
  --leading-none: 1;
  --leading-tight: 1.15;
  --leading-snug: 1.3;
  --leading-normal: 1.5;
  
  /* Letter Spacing */
  --tracking-tighter: -0.02em;
  --tracking-tight: -0.01em;
  --tracking-normal: 0;
  --tracking-wide: 0.05em;
  --tracking-wider: 0.1em;
  --tracking-widest: 0.2em;
}
```

### 3.3 Typography Patterns

```css
/* Large numerical displays (percentages, counts, timers) */
.display-value {
  font-family: var(--font-mono);
  font-size: var(--text-4xl);
  font-weight: 300;
  letter-spacing: var(--tracking-tight);
  line-height: var(--leading-none);
  font-variant-numeric: tabular-nums;
  color: var(--color-primary-500);
}

/* Section/Panel headers */
.panel-header {
  font-family: var(--font-display);
  font-size: var(--text-md);
  font-weight: 600;
  letter-spacing: var(--tracking-widest);
  text-transform: uppercase;
  color: var(--color-text-primary);
}

/* Data labels */
.data-label {
  font-family: var(--font-mono);
  font-size: var(--text-2xs);
  font-weight: 500;
  letter-spacing: var(--tracking-wider);
  text-transform: uppercase;
  color: var(--color-text-secondary);
}

/* Data values */
.data-value {
  font-family: var(--font-mono);
  font-size: var(--text-sm);
  font-weight: 400;
  font-variant-numeric: tabular-nums slashed-zero;
  color: var(--color-text-primary);
}

/* Terminal/Log text */
.terminal-text {
  font-family: var(--font-mono);
  font-size: var(--text-xs);
  line-height: var(--leading-snug);
  color: var(--color-primary-400);
}

/* Micro labels (timestamps, IDs) */
.micro-label {
  font-family: var(--font-mono-condensed);
  font-size: var(--text-3xs);
  letter-spacing: var(--tracking-wide);
  text-transform: uppercase;
  color: var(--color-text-tertiary);
}
```

### 3.4 Text Styling Conventions

1. **Case Usage**:
   - UPPERCASE: Headers, labels, status indicators, system messages
   - lowercase: User-generated content, descriptions, long-form text
   - Mixed: File names, identifiers, code

2. **Numeric Display**:
   - Always use tabular figures (`font-variant-numeric: tabular-nums`)
   - Use slashed zeros for disambiguation (`slashed-zero`)
   - Pad numbers with leading zeros for fixed-width display: `007`, `0042`
   - Use decimal points with fixed precision: `98.7%`, `0.005`

3. **Technical Notation**:
   - Hexadecimal: `0x4A2F`, `#FF00CC`
   - Memory addresses: `0x00FF:1A2B`
   - Coordinates: `X: 127.45 | Y: -89.02 | Z: 0.00`
   - Timestamps: `2087.03.15 // 14:32:07.445`

---

## 4. Layout System

### 4.1 Grid Foundation

```css
:root {
  /* Base unit - all spacing derives from this */
  --unit: 4px;
  
  /* Spacing scale */
  --space-1: calc(var(--unit) * 1);   /* 4px */
  --space-2: calc(var(--unit) * 2);   /* 8px */
  --space-3: calc(var(--unit) * 3);   /* 12px */
  --space-4: calc(var(--unit) * 4);   /* 16px */
  --space-5: calc(var(--unit) * 5);   /* 20px */
  --space-6: calc(var(--unit) * 6);   /* 24px */
  --space-8: calc(var(--unit) * 8);   /* 32px */
  --space-10: calc(var(--unit) * 10); /* 40px */
  --space-12: calc(var(--unit) * 12); /* 48px */
  --space-16: calc(var(--unit) * 16); /* 64px */
  
  /* Panel gaps */
  --gap-panel: var(--space-2);        /* Between major panels */
  --gap-section: var(--space-4);      /* Between sections within panels */
  --gap-element: var(--space-2);      /* Between related elements */
}
```

### 4.2 Panel Architecture

The interface is composed of nested panels following a strict hierarchy:

```
┌─────────────────────────────────────────────────────────────────┐
│ VIEWPORT (Full screen)                                          │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ HEADER BAR (Fixed height: 48-64px)                          │ │
│ │ [Logo] [Nav Tabs] [System Status] [User] [Time]             │ │
│ └─────────────────────────────────────────────────────────────┘ │
│ ┌───────────┬───────────────────────────────────┬─────────────┐ │
│ │ LEFT      │ MAIN CONTENT AREA                 │ RIGHT       │ │
│ │ SIDEBAR   │ ┌───────────────┬───────────────┐ │ SIDEBAR     │ │
│ │           │ │ PANEL A       │ PANEL B       │ │             │ │
│ │ [Nav]     │ │               │               │ │ [Quick      │ │
│ │ [Tree]    │ ├───────────────┼───────────────┤ │  Actions]   │ │
│ │ [Status]  │ │ PANEL C       │ PANEL D       │ │ [Alerts]    │ │
│ │           │ │               │               │ │ [Stats]     │ │
│ │           │ └───────────────┴───────────────┘ │             │ │
│ └───────────┴───────────────────────────────────┴─────────────┘ │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ FOOTER BAR (Fixed height: 32-48px)                          │ │
│ │ [Connection Status] [Coordinates] [Memory] [Logs]           │ │
│ └─────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

### 4.3 Panel Component Structure

```css
/* Base panel container */
.panel {
  position: relative;
  background: var(--color-deep-space);
  border: 1px solid var(--color-primary-800);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* Panel with corner accents */
.panel::before,
.panel::after {
  content: '';
  position: absolute;
  width: 12px;
  height: 12px;
  border-color: var(--color-primary-500);
  border-style: solid;
  pointer-events: none;
}

.panel::before {
  top: 0;
  left: 0;
  border-width: 2px 0 0 2px;
}

.panel::after {
  bottom: 0;
  right: 0;
  border-width: 0 2px 2px 0;
}

/* Panel header */
.panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-2) var(--space-3);
  background: linear-gradient(
    180deg,
    rgba(0, 212, 255, 0.1) 0%,
    transparent 100%
  );
  border-bottom: 1px solid var(--color-primary-900);
}

/* Panel body */
.panel-body {
  flex: 1;
  padding: var(--space-3);
  overflow: auto;
}
```

### 4.4 Responsive Breakpoints

```css
:root {
  --breakpoint-sm: 640px;   /* Mobile landscape */
  --breakpoint-md: 1024px;  /* Tablet */
  --breakpoint-lg: 1440px;  /* Desktop */
  --breakpoint-xl: 1920px;  /* Large desktop */
  --breakpoint-2xl: 2560px; /* Ultra-wide */
}

/* Layout should prioritize horizontal space - panels stack only on mobile */
@media (max-width: 1024px) {
  /* Stack sidebars below main content */
}

@media (min-width: 1920px) {
  /* Expand to show additional data columns */
}
```

---

## 5. UI Components

### 5.1 Borders & Frames

```css
:root {
  /* Border widths */
  --border-thin: 1px;
  --border-medium: 2px;
  --border-thick: 3px;
  
  /* Border styles */
  --border-default: var(--border-thin) solid var(--color-primary-800);
  --border-active: var(--border-thin) solid var(--color-primary-500);
  --border-glow: var(--border-thin) solid var(--color-primary-400);
}

/* Corner bracket frame - signature FUI element */
.frame-brackets {
  position: relative;
  padding: var(--space-4);
}

.frame-brackets::before,
.frame-brackets::after,
.frame-brackets > .corner-bl,
.frame-brackets > .corner-br {
  position: absolute;
  width: 16px;
  height: 16px;
  border-color: var(--color-primary-500);
  border-style: solid;
}

.frame-brackets::before { /* Top-left */
  top: 0; left: 0;
  border-width: 2px 0 0 2px;
}

.frame-brackets::after { /* Top-right */
  top: 0; right: 0;
  border-width: 2px 2px 0 0;
}

.frame-brackets > .corner-bl { /* Bottom-left */
  bottom: 0; left: 0;
  border-width: 0 0 2px 2px;
}

.frame-brackets > .corner-br { /* Bottom-right */
  bottom: 0; right: 0;
  border-width: 0 2px 2px 0;
}

/* Hexagonal clip path for special containers */
.hex-frame {
  clip-path: polygon(
    8px 0%, calc(100% - 8px) 0%,
    100% 8px, 100% calc(100% - 8px),
    calc(100% - 8px) 100%, 8px 100%,
    0% calc(100% - 8px), 0% 8px
  );
}
```

### 5.2 Buttons

```css
/* Base button */
.btn {
  font-family: var(--font-mono);
  font-size: var(--text-xs);
  font-weight: 500;
  letter-spacing: var(--tracking-wider);
  text-transform: uppercase;
  padding: var(--space-2) var(--space-4);
  border: var(--border-default);
  background: var(--color-dark-matter);
  color: var(--color-text-primary);
  cursor: pointer;
  position: relative;
  transition: all 0.15s ease;
  clip-path: polygon(
    6px 0%, calc(100% - 6px) 0%,
    100% 6px, 100% calc(100% - 6px),
    calc(100% - 6px) 100%, 6px 100%,
    0% calc(100% - 6px), 0% 6px
  );
}

.btn:hover {
  background: var(--color-carbon);
  border-color: var(--color-primary-500);
  color: var(--color-primary-400);
}

.btn:active {
  background: var(--color-primary-900);
}

/* Primary action button */
.btn-primary {
  background: linear-gradient(
    180deg,
    var(--color-primary-700) 0%,
    var(--color-primary-900) 100%
  );
  border-color: var(--color-primary-500);
  color: var(--color-text-primary);
}

.btn-primary:hover {
  background: linear-gradient(
    180deg,
    var(--color-primary-600) 0%,
    var(--color-primary-800) 100%
  );
  box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
}

/* Danger/Cancel button */
.btn-danger {
  border-color: var(--color-error-dim);
  color: var(--color-error);
}

.btn-danger:hover {
  background: rgba(255, 51, 85, 0.1);
  border-color: var(--color-error);
  box-shadow: 0 0 15px rgba(255, 51, 85, 0.2);
}

/* Icon-only button */
.btn-icon {
  width: 32px;
  height: 32px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}
```

### 5.3 Form Inputs

```css
/* Text input */
.input {
  font-family: var(--font-mono);
  font-size: var(--text-sm);
  padding: var(--space-2) var(--space-3);
  background: var(--color-abyss);
  border: var(--border-default);
  color: var(--color-text-primary);
  caret-color: var(--color-primary-500);
  width: 100%;
}

.input:focus {
  outline: none;
  border-color: var(--color-primary-500);
  box-shadow: 
    inset 0 0 10px rgba(0, 212, 255, 0.1),
    0 0 5px rgba(0, 212, 255, 0.2);
}

.input::placeholder {
  color: var(--color-text-tertiary);
  font-style: italic;
}

/* Input with inline label */
.input-group {
  display: flex;
  align-items: stretch;
}

.input-group-label {
  font-family: var(--font-mono);
  font-size: var(--text-2xs);
  letter-spacing: var(--tracking-wider);
  text-transform: uppercase;
  padding: var(--space-2) var(--space-3);
  background: var(--color-dark-matter);
  border: var(--border-default);
  border-right: none;
  color: var(--color-text-secondary);
  display: flex;
  align-items: center;
  white-space: nowrap;
}

/* Slider/Range input */
.slider {
  -webkit-appearance: none;
  appearance: none;
  width: 100%;
  height: 4px;
  background: var(--color-dark-matter);
  border: 1px solid var(--color-primary-800);
  cursor: pointer;
}

.slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 12px;
  height: 20px;
  background: var(--color-primary-500);
  border: none;
  clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
}

/* Toggle switch */
.toggle {
  position: relative;
  width: 48px;
  height: 24px;
  background: var(--color-dark-matter);
  border: var(--border-default);
  cursor: pointer;
}

.toggle::after {
  content: '';
  position: absolute;
  top: 3px;
  left: 3px;
  width: 16px;
  height: 16px;
  background: var(--color-text-tertiary);
  transition: all 0.2s ease;
}

.toggle.active {
  border-color: var(--color-success);
  background: rgba(0, 255, 136, 0.1);
}

.toggle.active::after {
  left: 27px;
  background: var(--color-success);
  box-shadow: 0 0 8px var(--color-success);
}
```

### 5.4 Data Display Components

```css
/* Key-value pair */
.data-pair {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  padding: var(--space-1) 0;
  border-bottom: 1px solid var(--color-gunmetal);
}

.data-pair:last-child {
  border-bottom: none;
}

.data-pair-key {
  font-family: var(--font-mono);
  font-size: var(--text-2xs);
  letter-spacing: var(--tracking-wider);
  text-transform: uppercase;
  color: var(--color-text-tertiary);
}

.data-pair-value {
  font-family: var(--font-mono);
  font-size: var(--text-sm);
  color: var(--color-primary-400);
  font-variant-numeric: tabular-nums;
}

/* Status indicator */
.status-indicator {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  font-family: var(--font-mono);
  font-size: var(--text-xs);
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
}

.status-indicator::before {
  content: '';
  width: 8px;
  height: 8px;
  background: currentColor;
  clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%); /* Diamond */
}

.status-indicator.online { color: var(--color-success); }
.status-indicator.offline { color: var(--color-error); }
.status-indicator.warning { color: var(--color-warning); }
.status-indicator.standby { color: var(--color-standby); }
.status-indicator.scanning { color: var(--color-scanning); }

/* Pulsing status for active states */
.status-indicator.online::before,
.status-indicator.scanning::before {
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}

/* Progress bar */
.progress-bar {
  height: 20px;
  background: var(--color-abyss);
  border: var(--border-default);
  position: relative;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  background: linear-gradient(
    90deg,
    var(--color-primary-700) 0%,
    var(--color-primary-500) 100%
  );
  transition: width 0.3s ease;
}

/* Segmented progress bar (more FUI authentic) */
.progress-bar-segmented {
  display: flex;
  gap: 2px;
  height: 16px;
}

.progress-segment {
  flex: 1;
  background: var(--color-dark-matter);
  border: 1px solid var(--color-primary-900);
}

.progress-segment.filled {
  background: var(--color-primary-600);
  border-color: var(--color-primary-500);
  box-shadow: 0 0 4px rgba(0, 212, 255, 0.3);
}

/* Data table */
.data-table {
  width: 100%;
  border-collapse: collapse;
  font-family: var(--font-mono);
  font-size: var(--text-2xs);
}

.data-table th {
  text-align: left;
  padding: var(--space-2) var(--space-3);
  background: var(--color-dark-matter);
  border-bottom: 1px solid var(--color-primary-700);
  font-weight: 500;
  letter-spacing: var(--tracking-wider);
  text-transform: uppercase;
  color: var(--color-text-secondary);
}

.data-table td {
  padding: var(--space-1) var(--space-3);
  border-bottom: 1px solid var(--color-gunmetal);
  color: var(--color-text-primary);
  font-variant-numeric: tabular-nums;
}

.data-table tr:hover td {
  background: rgba(0, 212, 255, 0.05);
}

/* Scrolling log/terminal output */
.log-output {
  font-family: var(--font-mono);
  font-size: var(--text-xs);
  line-height: var(--leading-snug);
  background: var(--color-void);
  padding: var(--space-3);
  max-height: 300px;
  overflow-y: auto;
  border: var(--border-default);
}

.log-entry {
  display: flex;
  gap: var(--space-3);
  padding: var(--space-1) 0;
}

.log-timestamp {
  color: var(--color-text-tertiary);
  flex-shrink: 0;
}

.log-message {
  color: var(--color-primary-400);
}

.log-entry.error .log-message { color: var(--color-error); }
.log-entry.warning .log-message { color: var(--color-warning); }
.log-entry.success .log-message { color: var(--color-success); }
```

### 5.5 Navigation Components

```css
/* Tab navigation */
.tabs {
  display: flex;
  border-bottom: 1px solid var(--color-primary-900);
}

.tab {
  font-family: var(--font-display);
  font-size: var(--text-xs);
  letter-spacing: var(--tracking-widest);
  text-transform: uppercase;
  padding: var(--space-3) var(--space-5);
  background: transparent;
  border: none;
  color: var(--color-text-secondary);
  cursor: pointer;
  position: relative;
  transition: color 0.15s ease;
}

.tab:hover {
  color: var(--color-text-primary);
}

.tab.active {
  color: var(--color-primary-400);
  background: linear-gradient(
    180deg,
    rgba(0, 212, 255, 0.1) 0%,
    transparent 100%
  );
}

.tab.active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  right: 0;
  height: 2px;
  background: var(--color-primary-500);
  box-shadow: 0 0 10px var(--color-primary-500);
}

/* Vertical navigation list */
.nav-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-item {
  font-family: var(--font-mono);
  font-size: var(--text-xs);
  letter-spacing: var(--tracking-wide);
  text-transform: uppercase;
  padding: var(--space-2) var(--space-3);
  color: var(--color-text-secondary);
  cursor: pointer;
  border-left: 2px solid transparent;
  transition: all 0.15s ease;
}

.nav-item:hover {
  background: rgba(0, 212, 255, 0.05);
  color: var(--color-text-primary);
}

.nav-item.active {
  background: rgba(0, 212, 255, 0.1);
  border-left-color: var(--color-primary-500);
  color: var(--color-primary-400);
}

/* Breadcrumb */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  font-family: var(--font-mono);
  font-size: var(--text-xs);
  color: var(--color-text-tertiary);
}

.breadcrumb-separator {
  color: var(--color-primary-700);
}

.breadcrumb-item:last-child {
  color: var(--color-text-primary);
}
```

### 5.6 Modal/Dialog

```css
/* Modal backdrop */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

/* Modal container */
.modal {
  background: var(--color-deep-space);
  border: var(--border-active);
  min-width: 400px;
  max-width: 90vw;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  position: relative;
  box-shadow: 
    0 0 40px rgba(0, 212, 255, 0.2),
    0 0 80px rgba(0, 0, 0, 0.8);
  animation: modal-enter 0.2s ease-out;
}

@keyframes modal-enter {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

/* Modal corners */
.modal::before,
.modal::after {
  content: '';
  position: absolute;
  width: 20px;
  height: 20px;
  border-color: var(--color-primary-500);
  border-style: solid;
}

.modal::before {
  top: -1px; left: -1px;
  border-width: 3px 0 0 3px;
}

.modal::after {
  bottom: -1px; right: -1px;
  border-width: 0 3px 3px 0;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-4);
  background: linear-gradient(
    180deg,
    rgba(0, 212, 255, 0.15) 0%,
    transparent 100%
  );
  border-bottom: 1px solid var(--color-primary-800);
}

.modal-title {
  font-family: var(--font-display);
  font-size: var(--text-md);
  letter-spacing: var(--tracking-widest);
  text-transform: uppercase;
  color: var(--color-text-primary);
  margin: 0;
}

.modal-body {
  padding: var(--space-4);
  overflow-y: auto;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: var(--space-3);
  padding: var(--space-4);
  border-top: 1px solid var(--color-primary-900);
}
```

---

## 6. Visual Effects & Treatments

### 6.1 Glow Effects

```css
/* Standard cyan glow */
.glow {
  box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
}

.glow-strong {
  box-shadow: 
    0 0 10px rgba(0, 212, 255, 0.4),
    0 0 20px rgba(0, 212, 255, 0.2),
    0 0 40px rgba(0, 212, 255, 0.1);
}

/* Text glow */
.text-glow {
  text-shadow: 0 0 10px currentColor;
}

/* Animated pulsing glow */
.glow-pulse {
  animation: glow-pulse 2s ease-in-out infinite;
}

@keyframes glow-pulse {
  0%, 100% {
    box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
  }
  50% {
    box-shadow: 
      0 0 20px rgba(0, 212, 255, 0.5),
      0 0 40px rgba(0, 212, 255, 0.3);
  }
}
```

### 6.2 Scan Line Effect

```css
/* CRT scan lines overlay */
.scanlines {
  position: relative;
}

.scanlines::after {
  content: '';
  position: absolute;
  inset: 0;
  background: repeating-linear-gradient(
    0deg,
    transparent,
    transparent 2px,
    rgba(0, 0, 0, 0.1) 2px,
    rgba(0, 0, 0, 0.1) 4px
  );
  pointer-events: none;
}

/* Subtle version */
.scanlines-subtle::after {
  background: repeating-linear-gradient(
    0deg,
    transparent,
    transparent 1px,
    rgba(0, 0, 0, 0.03) 1px,
    rgba(0, 0, 0, 0.03) 2px
  );
}
```

### 6.3 Noise/Grain Texture

```css
/* Animated noise overlay */
.noise {
  position: relative;
}

.noise::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
  opacity: 0.03;
  pointer-events: none;
  mix-blend-mode: overlay;
}

/* Animated flicker noise */
.noise-flicker::before {
  animation: noise-shift 0.5s steps(10) infinite;
}

@keyframes noise-shift {
  0%, 100% { transform: translate(0, 0); }
  10% { transform: translate(-1%, -1%); }
  20% { transform: translate(1%, 1%); }
  30% { transform: translate(-1%, 1%); }
  40% { transform: translate(1%, -1%); }
  50% { transform: translate(-1%, 0%); }
  60% { transform: translate(1%, 0%); }
  70% { transform: translate(0%, 1%); }
  80% { transform: translate(0%, -1%); }
  90% { transform: translate(1%, 1%); }
}
```

### 6.4 Glitch Effect

```css
/* Text glitch on hover */
.glitch-text {
  position: relative;
}

.glitch-text:hover {
  animation: glitch-skew 0.5s infinite linear alternate-reverse;
}

.glitch-text::before,
.glitch-text::after {
  content: attr(data-text);
  position: absolute;
  top: 0;
  left: 0;
  opacity: 0;
}

.glitch-text:hover::before {
  opacity: 0.8;
  color: var(--color-error);
  animation: glitch-shift 0.3s infinite linear alternate-reverse;
  clip-path: polygon(0 0, 100% 0, 100% 35%, 0 35%);
}

.glitch-text:hover::after {
  opacity: 0.8;
  color: var(--color-primary-400);
  animation: glitch-shift 0.3s infinite linear alternate-reverse reverse;
  clip-path: polygon(0 65%, 100% 65%, 100% 100%, 0 100%);
}

@keyframes glitch-skew {
  0% { transform: skew(0deg); }
  20% { transform: skew(-2deg); }
  40% { transform: skew(2deg); }
  60% { transform: skew(0deg); }
  80% { transform: skew(-1deg); }
  100% { transform: skew(1deg); }
}

@keyframes glitch-shift {
  0% { transform: translate(0); }
  20% { transform: translate(-3px, 2px); }
  40% { transform: translate(3px, -2px); }
  60% { transform: translate(-2px, -1px); }
  80% { transform: translate(2px, 1px); }
  100% { transform: translate(0); }
}
```

### 6.5 Border Animation

```css
/* Animated border trace */
.border-trace {
  position: relative;
  background: var(--color-deep-space);
}

.border-trace::before {
  content: '';
  position: absolute;
  inset: 0;
  padding: 1px;
  background: conic-gradient(
    from 0deg,
    var(--color-primary-500),
    transparent 30%,
    transparent 70%,
    var(--color-primary-500)
  );
  -webkit-mask: 
    linear-gradient(#fff 0 0) content-box, 
    linear-gradient(#fff 0 0);
  mask: 
    linear-gradient(#fff 0 0) content-box, 
    linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  animation: border-rotate 4s linear infinite;
}

@keyframes border-rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
```

---

## 7. Data Visualization

### 7.1 Chart Styling

```css
/* Chart container */
.chart-container {
  background: var(--color-abyss);
  border: var(--border-default);
  padding: var(--space-4);
}

/* Chart color palette for data series */
:root {
  --chart-series-1: #00d4ff;  /* Primary cyan */
  --chart-series-2: #00ff88;  /* Success green */
  --chart-series-3: #ffaa00;  /* Warning amber */
  --chart-series-4: #ff3355;  /* Error red */
  --chart-series-5: #aa66ff;  /* Purple */
  --chart-series-6: #ff66aa;  /* Pink */
  
  /* Grid and axis colors */
  --chart-grid: rgba(0, 212, 255, 0.1);
  --chart-axis: var(--color-primary-800);
  --chart-label: var(--color-text-tertiary);
}

/* Grid lines */
.chart-grid-line {
  stroke: var(--chart-grid);
  stroke-width: 1;
}

/* Axis lines */
.chart-axis-line {
  stroke: var(--chart-axis);
  stroke-width: 1;
}

/* Data line styling */
.chart-line {
  fill: none;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
}

/* Area fill under lines */
.chart-area {
  opacity: 0.2;
}

/* Bar chart bars */
.chart-bar {
  rx: 0; /* Sharp corners */
}

/* Scatter plot dots */
.chart-dot {
  r: 4;
  stroke-width: 2;
  stroke: var(--color-deep-space);
}
```

### 7.2 Specialized Displays

```css
/* Circular gauge / dial */
.gauge-circular {
  position: relative;
  width: 120px;
  height: 120px;
}

.gauge-circular svg {
  transform: rotate(-90deg);
}

.gauge-track {
  fill: none;
  stroke: var(--color-dark-matter);
  stroke-width: 8;
}

.gauge-fill {
  fill: none;
  stroke: var(--color-primary-500);
  stroke-width: 8;
  stroke-linecap: butt;
  transition: stroke-dashoffset 0.5s ease;
}

.gauge-value {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-family: var(--font-mono);
  font-size: var(--text-xl);
  color: var(--color-primary-400);
}

/* Mini bar chart (sparkline-style) */
.mini-bars {
  display: flex;
  align-items: flex-end;
  gap: 2px;
  height: 32px;
}

.mini-bar {
  width: 4px;
  background: var(--color-primary-600);
  border-radius: 1px 1px 0 0;
}

/* Waveform display */
.waveform {
  display: flex;
  align-items: center;
  gap: 1px;
  height: 24px;
}

.waveform-bar {
  width: 2px;
  background: var(--color-primary-500);
  animation: waveform-pulse 1s ease-in-out infinite;
}

.waveform-bar:nth-child(odd) {
  animation-delay: 0.1s;
}

@keyframes waveform-pulse {
  0%, 100% { height: 20%; }
  50% { height: 100%; }
}
```

---

## 8. Animation Guidelines

### 8.1 Timing Functions

```css
:root {
  /* Standard easing */
  --ease-default: cubic-bezier(0.4, 0, 0.2, 1);
  --ease-in: cubic-bezier(0.4, 0, 1, 1);
  --ease-out: cubic-bezier(0, 0, 0.2, 1);
  --ease-in-out: cubic-bezier(0.4, 0, 0.2, 1);
  
  /* Mechanical/robotic feel */
  --ease-step: steps(8, end);
  --ease-snap: cubic-bezier(0.68, -0.55, 0.265, 1.55);
  
  /* Duration scale */
  --duration-instant: 50ms;
  --duration-fast: 150ms;
  --duration-normal: 300ms;
  --duration-slow: 500ms;
  --duration-dramatic: 1000ms;
}
```

### 8.2 Standard Animations

```css
/* Fade in */
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Slide in from bottom */
@keyframes slide-up {
  from { 
    opacity: 0;
    transform: translateY(20px);
  }
  to { 
    opacity: 1;
    transform: translateY(0);
  }
}

/* Scale in */
@keyframes scale-in {
  from { 
    opacity: 0;
    transform: scale(0.9);
  }
  to { 
    opacity: 1;
    transform: scale(1);
  }
}

/* Data tick - for updating values */
@keyframes data-tick {
  0% { color: var(--color-primary-400); }
  50% { color: var(--color-text-primary); }
  100% { color: var(--color-primary-400); }
}

/* Scan sweep - horizontal */
@keyframes scan-sweep {
  from {
    background-position: -100% 0;
  }
  to {
    background-position: 200% 0;
  }
}

.scanning {
  position: relative;
  overflow: hidden;
}

.scanning::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(
    90deg,
    transparent 0%,
    rgba(0, 212, 255, 0.3) 50%,
    transparent 100%
  );
  background-size: 50% 100%;
  animation: scan-sweep 2s linear infinite;
}

/* Blink cursor */
@keyframes blink {
  0%, 50% { opacity: 1; }
  51%, 100% { opacity: 0; }
}

.cursor-blink::after {
  content: '█';
  animation: blink 1s step-end infinite;
  color: var(--color-primary-500);
}

/* Rotating element (radar, loading) */
@keyframes rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.rotate {
  animation: rotate 4s linear infinite;
}
```

### 8.3 Animation Principles

1. **Purposeful Motion**: Every animation should communicate state change or guide attention
2. **Mechanical Feel**: Prefer stepped or snappy animations over fluid organic motion
3. **Data Updates**: Values changing should flash/tick briefly to draw attention
4. **Loading States**: Use scan lines, rotating elements, or pulsing rather than spinners
5. **Staggered Entry**: When multiple elements appear, stagger by 50-100ms each

---

## 9. Iconography

### 9.1 Icon Style Guidelines

- **Line weight**: 1.5-2px consistent stroke
- **Corners**: Sharp or minimal rounding (2px max radius)
- **Style**: Geometric, technical, avoid organic curves
- **Size grid**: Design on 24x24 base, scale to 16, 20, 32, 48
- **Color**: Single color, typically inherits from parent text color

### 9.2 Common Icon Set Requirements

```
Navigation:
├── Arrow chevrons (left, right, up, down)
├── Hamburger menu
├── Close (X)
├── Expand/collapse
└── External link

Actions:
├── Play / Pause / Stop
├── Refresh / Sync
├── Download / Upload
├── Save / Load
├── Delete / Trash
├── Edit / Configure
├── Search / Scan
└── Lock / Unlock

Status:
├── Check / Success
├── Warning triangle
├── Error / Alert
├── Info circle
├── Loading / Processing
├── Online / Offline
└── Signal strength (1-5 bars)

Data:
├── File (generic, code, image, document)
├── Folder (open, closed)
├── Database
├── Server
├── Network node
├── Graph / Chart
└── Terminal / Console

System:
├── Power
├── Settings / Gear
├── User / Profile
├── Notification
├── Camera / Eye
├── Microphone
└── Connection / Link
```

### 9.3 Icon Implementation

```css
/* Icon base class */
.icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1em;
  height: 1em;
  stroke: currentColor;
  stroke-width: 2;
  stroke-linecap: square;
  stroke-linejoin: miter;
  fill: none;
}

/* Size variants */
.icon-sm { font-size: 16px; }
.icon-md { font-size: 24px; }
.icon-lg { font-size: 32px; }
.icon-xl { font-size: 48px; }
```

---

## 10. Responsive Behavior

### 10.1 Breakpoint Strategy

```css
/* Mobile-first approach with FUI adaptations */

/* Base: Mobile (< 640px) */
/* Simplified single-column layout, larger touch targets */

/* Tablet (640px - 1024px) */
@media (min-width: 640px) {
  /* Two-column layouts begin */
  /* Side panels collapse to bottom sheets */
}

/* Desktop (1024px - 1440px) */
@media (min-width: 1024px) {
  /* Full multi-panel layouts */
  /* Sidebars visible */
}

/* Large Desktop (1440px - 1920px) */
@media (min-width: 1440px) {
  /* Maximum information density */
  /* Additional data columns */
}

/* Ultra-wide (> 1920px) */
@media (min-width: 1920px) {
  /* Extended layouts */
  /* Auxiliary monitoring panels */
}
```

### 10.2 Touch Adaptations

```css
/* Increased touch targets for mobile */
@media (pointer: coarse) {
  .btn {
    min-height: 44px;
    min-width: 44px;
  }
  
  .nav-item {
    padding: var(--space-4);
  }
  
  .tab {
    padding: var(--space-4) var(--space-5);
  }
}
```

---

## 11. Accessibility Considerations

### 11.1 Color Contrast

- Maintain minimum 4.5:1 contrast ratio for normal text
- Maintain minimum 3:1 for large text and UI components
- Provide non-color indicators for status (icons, labels)

### 11.2 Motion Sensitivity

```css
/* Respect reduced motion preferences */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
  
  .scanlines::after,
  .noise::before {
    display: none;
  }
}
```

### 11.3 Focus States

```css
/* Visible focus indicators */
:focus-visible {
  outline: 2px solid var(--color-primary-500);
  outline-offset: 2px;
}

/* Remove default focus ring when using mouse */
:focus:not(:focus-visible) {
  outline: none;
}
```

---

## 12. Implementation Checklist

When building a FUI-styled interface, ensure the following:

### Must Have
- [ ] Dark background with cyan/teal primary accent
- [ ] Monospace typography for data
- [ ] Corner bracket frames on key panels
- [ ] Uppercase labels with wide letter-spacing
- [ ] Status indicators with semantic colors
- [ ] Subtle scan line or noise overlay (optional, toggleable)
- [ ] Consistent 4px-based spacing
- [ ] Proper color contrast ratios

### Should Have
- [ ] Glowing effects on active/focused elements
- [ ] Segmented progress bars
- [ ] Tabular numeric figures
- [ ] Technical timestamp formatting
- [ ] Animated state transitions
- [ ] Collapsible panel system

### Nice to Have
- [ ] Glitch effects on interactions
- [ ] Animated border traces
- [ ] Sound effects for actions
- [ ] Data update tick animations
- [ ] Radar/sweep loading indicators
- [ ] Parallax depth on panels

---

## 13. Code Examples

### 13.1 Complete Panel Component (HTML)

```html
<div class="panel">
  <div class="panel-header">
    <div class="panel-title">
      <span class="panel-icon">◆</span>
      SYSTEM STATUS
    </div>
    <div class="panel-actions">
      <button class="btn-icon" aria-label="Refresh">↻</button>
      <button class="btn-icon" aria-label="Expand">⤢</button>
    </div>
  </div>
  
  <div class="panel-body">
    <div class="data-pair">
      <span class="data-pair-key">Connection</span>
      <span class="data-pair-value status-indicator online">ONLINE</span>
    </div>
    <div class="data-pair">
      <span class="data-pair-key">Uptime</span>
      <span class="data-pair-value">13d 15h 11m</span>
    </div>
    <div class="data-pair">
      <span class="data-pair-key">Load</span>
      <span class="data-pair-value">0.69, 0.57, 0.49</span>
    </div>
    
    <div class="progress-bar-segmented" style="margin-top: var(--space-4);">
      <div class="progress-segment filled"></div>
      <div class="progress-segment filled"></div>
      <div class="progress-segment filled"></div>
      <div class="progress-segment filled"></div>
      <div class="progress-segment filled"></div>
      <div class="progress-segment filled"></div>
      <div class="progress-segment"></div>
      <div class="progress-segment"></div>
      <div class="progress-segment"></div>
      <div class="progress-segment"></div>
    </div>
    
    <div class="micro-label" style="margin-top: var(--space-2);">
      CPU: 60% NOMINAL
    </div>
  </div>
</div>
```

### 13.2 Complete CSS Reset/Base

```css
/* FUI Base Reset */
*, *::before, *::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html {
  font-size: 16px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

body {
  font-family: var(--font-ui);
  font-size: var(--text-base);
  line-height: var(--leading-normal);
  color: var(--color-text-primary);
  background: var(--color-abyss);
  min-height: 100vh;
}

/* Apply noise and scanlines to body */
body::before {
  content: '';
  position: fixed;
  inset: 0;
  pointer-events: none;
  z-index: 9999;
  background: repeating-linear-gradient(
    0deg,
    transparent,
    transparent 2px,
    rgba(0, 0, 0, 0.03) 2px,
    rgba(0, 0, 0, 0.03) 4px
  );
}

/* Scrollbar styling */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: var(--color-abyss);
}

::-webkit-scrollbar-thumb {
  background: var(--color-primary-800);
  border: 1px solid var(--color-primary-700);
}

::-webkit-scrollbar-thumb:hover {
  background: var(--color-primary-700);
}

/* Selection styling */
::selection {
  background: var(--color-primary-700);
  color: var(--color-text-primary);
}
```

---

## 14. Asset References

### 14.1 Recommended Fonts (Free)

| Font | Use Case | Source |
|------|----------|--------|
| JetBrains Mono | Data, code | Google Fonts |
| IBM Plex Mono | Dense tables | Google Fonts |
| Orbitron | Display headers | Google Fonts |
| Share Tech Mono | Alternate tech | Google Fonts |
| Rajdhani | UI labels | Google Fonts |
| Inter | Body text | Google Fonts |

### 14.2 Font Loading

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@300;400;500&family=Orbitron:wght@500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
```

---

## Appendix A: Terminology

| Term | Meaning |
|------|---------|
| FUI | Fictional User Interface (film/game UI design) |
| HUD | Heads-Up Display |
| Telemetry | Real-time data from sensors/systems |
| Nominal | Operating within normal parameters |
| Datum | Single piece of data |
| Vector | Directional data or graphical element |

---

## Appendix B: Inspiration Sources

- Film: Blade Runner, Minority Report, Iron Man, Oblivion, The Martian
- Games: Dead Space, Mass Effect, Deus Ex, EVE Online
- Software: btop, htop, Bloomberg Terminal
- Artists: Gmunk, Territory Studio, Perception NYC

---

*Document Version: 1.0*
*Last Updated: 2025*
*Designed for AI Agent Implementation*
