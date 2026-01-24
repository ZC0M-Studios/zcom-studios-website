# Sci-Fi Button Components Guide

## Overview
Interactive futuristic buttons with angled corners, glowing borders, hover animations, and click effects. Fully responsive and accessible.

## Features
- **Angled Corners:** Cyberpunk-style clipped corners with accent borders
- **Hover Animations:** Smooth lift effect with pulsing glow
- **Click Feedback:** Scale effect with enhanced inset glow
- **Multiple Variants:** Default, Primary, Success, Danger
- **Size Options:** Small, Default, Large
- **Haptic Feedback:** Vibration on mobile devices
- **Visual Notifications:** Toast-style notifications on click
- **Accessibility:** Works with keyboard, mouse, and touch

---

## Button Variants

### 1. Default Button
**Class:** `.btn-scifi`

Standard button with transparent background and blue borders.

```html
<button class="btn-scifi">DEFAULT</button>
```

---

### 2. Primary Button
**Class:** `.btn-scifi-primary`

Highlighted button with filled background and enhanced glow.

```html
<button class="btn-scifi btn-scifi-primary">CONFIRM</button>
```

---

### 3. Success Button
**Class:** `.btn-scifi-success`

Green-accented button for positive actions.

```html
<button class="btn-scifi btn-scifi-success">SUCCESS</button>
```

---

### 4. Danger Button
**Class:** `.btn-scifi-danger`

Red-accented button for destructive actions.

```html
<button class="btn-scifi btn-scifi-danger">DELETE</button>
```

---

## Size Variants

### Small Button
**Class:** `.btn-scifi-sm`

Compact button for tight spaces.

```html
<button class="btn-scifi btn-scifi-sm">SMALL</button>
```

### Large Button
**Class:** `.btn-scifi-lg`

Prominent button for primary actions.

```html
<button class="btn-scifi btn-scifi-lg">LARGE</button>
```

---

## Button States

### Hover State
- Lifts 2px upward
- Border color brightens
- Text color changes to blue
- Pulsing glow animation (2s loop)
- Corner accents glow

### Active/Click State
- Scales down to 98%
- Enhanced inset glow
- Quick 0.1s transition
- Haptic feedback (mobile)

### Disabled State
- 40% opacity
- No hover effects
- Cursor changes to not-allowed

```html
<button class="btn-scifi disabled">DISABLED</button>
<button class="btn-scifi" disabled>DISABLED</button>
```

---

## Combining Classes

You can combine variant and size classes:

```html
<button class="btn-scifi btn-scifi-primary btn-scifi-lg">LARGE PRIMARY</button>
<button class="btn-scifi btn-scifi-danger btn-scifi-sm">SMALL DANGER</button>
<button class="btn-scifi btn-scifi-success btn-scifi-lg">LARGE SUCCESS</button>
```

---

## Using as Links

Buttons work with anchor tags too:

```html
<a href="/page" class="btn-scifi">LINK BUTTON</a>
<a href="/confirm" class="btn-scifi btn-scifi-primary">PRIMARY LINK</a>
```

---

## Complete Examples

### Action Bar
```html
<div class="d-flex gap-3">
  <button class="btn-scifi btn-scifi-success">SAVE</button>
  <button class="btn-scifi">CANCEL</button>
  <button class="btn-scifi btn-scifi-danger">DELETE</button>
</div>
```

### Form Buttons
```html
<form>
  <!-- Form fields here -->
  <div class="d-flex gap-3 mt-4">
    <button type="submit" class="btn-scifi btn-scifi-primary btn-scifi-lg">SUBMIT</button>
    <button type="reset" class="btn-scifi">RESET</button>
  </div>
</form>
```

### Navigation
```html
<nav>
  <a href="/" class="btn-scifi btn-scifi-sm">HOME</a>
  <a href="/about" class="btn-scifi btn-scifi-sm">ABOUT</a>
  <a href="/contact" class="btn-scifi btn-scifi-primary btn-scifi-sm">CONTACT</a>
</nav>
```

---

## Color Palette

### Default/Primary (Blue)
- Border: `rgba(100, 181, 246, 0.4)`
- Hover: `#64b5f6`
- Glow: `rgba(100, 181, 246, 0.6)`

### Success (Green)
- Border: `rgba(100, 255, 150, 0.4)`
- Hover: `#64ff96`
- Glow: `rgba(100, 255, 150, 0.6)`

### Danger (Red)
- Border: `rgba(255, 100, 100, 0.4)`
- Hover: `#ff6464`
- Glow: `rgba(255, 100, 100, 0.6)`

---

## Demo Page
View live examples at: `/ui-demo.php`

---

## UniqueIDs
- **789005:** `.btn-scifi` base component
- **789006:** `.btn-scifi-primary` variant
- **789007:** `.btn-scifi-danger` variant
- **789008:** `.btn-scifi-success` variant
- **789009:** `.btn-scifi-sm` size
- **789010:** `.btn-scifi-lg` size
- **789011:** Button interaction handlers

