# Sci-Fi UI Components Guide

## Overview
Futuristic cyberpunk-inspired UI components with glowing blue borders, interactive hover animations, click effects, and badge support. Perfect for game-like interfaces and modern sci-fi web applications.

## New Features
- **Hover Animations:** Smooth slide-right effect with pulsing glow
- **Click Effects:** Scale and shadow feedback on click
- **Scan Line Animation:** Animated line that travels down on hover
- **Active State Glow:** Continuous pulsing animation for selected items
- **Badge Pulse:** Badges pulse when parent item is active
- **Keyboard Navigation:** Arrow keys to navigate, Enter/Space to select
- **Haptic Feedback:** Vibration on mobile devices (if supported)

## Components

### 1. Sci-Fi Menu Container
**Class:** `.scifi-menu`

Container for menu items. Provides proper spacing and layout.

```html
<div class="scifi-menu">
  <!-- Menu items go here -->
</div>
```

---

### 2. Sci-Fi Menu Item
**Class:** `.scifi-menu-item`

Individual menu item with cyan borders and hover effects.

**States:**
- **Default:** Semi-transparent dark background with cyan border
- **Hover:** Brighter cyan glow, thicker left border, glowing effects
- **Active:** `.active` - Cyan gradient background, vertical glow bar, enhanced effects
- **Disabled:** `.disabled` - Reduced opacity, no hover effects

**Structure:**
```html
<div class="scifi-menu-item">
  <span class="scifi-menu-glow-bar"></span>
  <span class="scifi-menu-label">ITEM NAME</span>
  <span class="scifi-menu-value">VALUE</span>
</div>
```

**Note:** The `scifi-menu-glow-bar` element is required for the vertical glow effect on active items.

**With Active State:**
```html
<div class="scifi-menu-item active">
  <span class="scifi-menu-label">ARMOR FX</span>
  <span class="scifi-menu-value">50%</span>
</div>
```

**With Disabled State:**
```html
<div class="scifi-menu-item disabled">
  <span class="scifi-menu-label">LOCKED ITEM</span>
  <span class="scifi-menu-value">0%</span>
</div>
```

---

### 3. Menu Label
**Class:** `.scifi-menu-label`

Left-aligned label text for menu items. Supports badges and icons.

```html
<span class="scifi-menu-label">HELMETS</span>
```

---

### 4. Menu Value
**Class:** `.scifi-menu-value`

Right-aligned value display (percentages, counts, etc.).

```html
<span class="scifi-menu-value">12%</span>
```

---

### 5. Menu Badge
**Class:** `.scifi-menu-badge`

Gold/yellow badge for displaying counts, icons, or status indicators.

**With Number:**
```html
<span class="scifi-menu-label">
  <span class="scifi-menu-badge">3</span>
  HELMETS
</span>
```

**With Icon/Emoji:**
```html
<span class="scifi-menu-label">
  <span class="scifi-menu-badge">💎</span>
  HELMETS
</span>
```

**With Lock Icon (Disabled):**
```html
<span class="scifi-menu-label">
  <span class="scifi-menu-badge" style="opacity: 0.4;">🔒</span>
  LOCKED ITEM
</span>
```

---

## Complete Examples

### Basic Menu
```html
<div class="scifi-menu">
  <div class="scifi-menu-item">
    <span class="scifi-menu-label">HELMETS</span>
    <span class="scifi-menu-value">12%</span>
  </div>
  <div class="scifi-menu-item active">
    <span class="scifi-menu-label">ARMOR FX</span>
    <span class="scifi-menu-value">50%</span>
  </div>
  <div class="scifi-menu-item">
    <span class="scifi-menu-label">COLORS</span>
    <span class="scifi-menu-value">12%</span>
  </div>
</div>
```

### Menu with Badges
```html
<div class="scifi-menu">
  <div class="scifi-menu-item">
    <span class="scifi-menu-label">
      <span class="scifi-menu-badge">999</span>
      VISORS
    </span>
    <span class="scifi-menu-value">25%</span>
  </div>
  <div class="scifi-menu-item disabled">
    <span class="scifi-menu-label">
      <span class="scifi-menu-badge" style="opacity: 0.4;">🔒</span>
      BODY ARMOR
    </span>
    <span class="scifi-menu-value">0%</span>
  </div>
</div>
```

---

## Design Features

### Visual Effects
- **Angled corner accent** on top-right corner
- **Glowing cyan borders** on hover and active states
- **Vertical glow bar** on left side when active
- **Inset shadows** for depth
- **Smooth transitions** (0.3s ease)

### Color Palette
- **Primary Blue:** `#64b5f6` (matches site theme)
- **Blue Glow:** `rgba(100, 181, 246, 0.6)`
- **Dark Background:** `rgba(10, 20, 30, 0.85)`
- **Border:** `rgba(100, 181, 246, 0.4)`
- **Badge Gold:** `#d4a017`

### Animations
- **menuItemPulse:** Pulsing glow effect on hover (2s loop)
- **scanLine:** Animated line traveling from top to bottom (1.5s loop)
- **activeGlow:** Enhanced pulsing for active items (3s loop)
- **badgePulse:** Badge scale and glow animation (2s loop)

### Typography
- **Font:** Electrolize (sans-serif)
- **Text Transform:** Uppercase
- **Letter Spacing:** 2px
- **Font Size:** 0.95rem

---

## Demo Page
View live examples at: `/ui-demo.php`

---

## Interactive Features

### Click-to-Select
Menu items automatically handle click events to toggle active state. Disabled items are not clickable.

### Keyboard Navigation
- **Arrow Up/Down:** Navigate between menu items
- **Enter/Space:** Select the focused item
- **Tab:** Standard focus navigation

### Mobile Support
- Touch-friendly click targets
- Haptic feedback on supported devices
- Smooth animations optimized for mobile

## UniqueIDs
- **789001:** `.scifi-menu-item` component with animations
- **789002:** `.scifi-menu` container
- **789003:** Keyframe animations (pulse, scan, glow)
- **789004:** JavaScript interaction handlers
- **789100:** Demo page implementation

