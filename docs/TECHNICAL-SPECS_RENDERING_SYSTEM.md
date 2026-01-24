# Advanced Sci-Fi UI/HUD Rendering System
## Technical Specification & Implementation Guide

**Version:** 1.0.0  
**Target Audience:** AI Coding Agents, Frontend Developers, Creative Technologists  
**Document Type:** Technical Specification with Implementation Patterns

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Technology Stack](#2-technology-stack)
3. [Core Architecture](#3-core-architecture)
4. [Rendering Pipeline](#4-rendering-pipeline)
5. [Animation System](#5-animation-system)
6. [Component Library](#6-component-library)
7. [Visual Effects Catalog](#7-visual-effects-catalog)
8. [Event System & Triggers](#8-event-system--triggers)
9. [Audio Integration](#9-audio-integration)
10. [Performance Optimization](#10-performance-optimization)
11. [Implementation Patterns](#11-implementation-patterns)

---

## 1. System Overview

### 1.1 Design Philosophy

Sci-Fi UI/HUD systems are characterized by several key visual and behavioral principles:

- **Layered Depth:** Multiple translucent planes creating parallax and dimensional hierarchy
- **Geometric Precision:** Sharp angles, hexagonal patterns, circular gauges with mathematical accuracy
- **Reactive Feedback:** Every interaction produces immediate, satisfying visual and audio response
- **Information Density:** Dense data visualization that appears complex but remains readable
- **Animated Idle States:** Constant subtle motion suggesting active, living technology
- **Holographic Aesthetic:** Glowing edges, chromatic aberration, scan lines, and transparency

### 1.2 Visual Language Glossary

| Term | Definition |
|------|------------|
| **Callout** | An animated connector line extending from a point-of-interest to an information panel |
| **HUD Layer** | A discrete rendering plane at a specific z-depth in the holographic stack |
| **Glow Bloom** | Soft luminous halo effect surrounding bright UI elements |
| **Scan Line** | Horizontal interference pattern moving vertically across the display |
| **Data Stream** | Animated text/numeric sequences suggesting real-time data flow |
| **Bracket Frame** | Corner-anchored decorative elements framing content areas |
| **Reticle** | Targeting or focus indicator, typically circular with segmented animation |
| **Ghost Echo** | Delayed duplicate rendering creating motion trail effect |

---

## 2. Technology Stack

### 2.1 Recommended Primary Stack

```
┌─────────────────────────────────────────────────────────────────┐
│  APPLICATION LAYER                                               │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐              │
│  │   React     │  │    Zustand  │  │  Framer     │              │
│  │   (18.x+)   │  │   (State)   │  │  Motion     │              │
│  └─────────────┘  └─────────────┘  └─────────────┘              │
├─────────────────────────────────────────────────────────────────┤
│  RENDERING LAYER                                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐              │
│  │  Three.js   │  │ React Three │  │    GSAP     │              │
│  │  (r160+)    │  │   Fiber     │  │  (Premium)  │              │
│  └─────────────┘  └─────────────┘  └─────────────┘              │
├─────────────────────────────────────────────────────────────────┤
│  EFFECTS LAYER                                                   │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐              │
│  │  Postproc-  │  │   Custom    │  │   Leva      │              │
│  │  essing     │  │   Shaders   │  │  (Debug)    │              │
│  └─────────────┘  └─────────────┘  └─────────────┘              │
├─────────────────────────────────────────────────────────────────┤
│  FOUNDATION                                                      │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐              │
│  │ TypeScript  │  │    Vite     │  │  Tailwind   │              │
│  │   (5.x+)    │  │   (5.x+)    │  │    CSS      │              │
│  └─────────────┘  └─────────────┘  └─────────────┘              │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Package Dependencies

```json
{
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0",
    "@react-three/fiber": "^8.15.0",
    "@react-three/drei": "^9.88.0",
    "@react-three/postprocessing": "^2.15.0",
    "three": "^0.160.0",
    "framer-motion": "^10.16.0",
    "gsap": "^3.12.0",
    "zustand": "^4.4.0",
    "howler": "^2.2.4",
    "leva": "^0.9.35"
  },
  "devDependencies": {
    "typescript": "^5.3.0",
    "vite": "^5.0.0",
    "@types/three": "^0.160.0",
    "tailwindcss": "^3.4.0",
    "glslify": "^7.1.1"
  }
}
```

### 2.3 Technology Selection Rationale

| Technology | Purpose | Why Selected |
|------------|---------|--------------|
| **React Three Fiber** | 3D rendering | Declarative Three.js with React paradigm; component-based scene graph |
| **Framer Motion** | 2D animations | Declarative animation API; excellent spring physics; layout animations |
| **GSAP** | Complex timelines | Industry-standard for sequenced animations; ScrollTrigger integration |
| **Three.js Postprocessing** | Visual effects | Bloom, chromatic aberration, film grain as GPU-accelerated passes |
| **Zustand** | State management | Minimal boilerplate; excellent for cross-component animation state |
| **Howler.js** | Audio | Web Audio API wrapper with sprite support for UI sound design |

### 2.4 Alternative Stacks

**Lightweight (No 3D Engine):**
```
SVG + CSS Animations + Framer Motion + Canvas 2D
```
*Best for: Flat HUD overlays, performance-critical applications*

**Game Engine Integration:**
```
PixiJS + GSAP + Custom WebGL Shaders
```
*Best for: Game UI, extremely high element counts*

**Pure WebGL:**
```
Raw Three.js + Custom Shader Materials + GSAP
```
*Best for: Maximum control, unique visual effects*

---

## 3. Core Architecture

### 3.1 System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           HUD SYSTEM ROOT                                │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │                      SCENE MANAGER                                  │ │
│  │  • Controls render loop                                             │ │
│  │  • Manages layer visibility                                         │ │
│  │  • Handles resize/responsive scaling                                │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                  │                                       │
│         ┌────────────────────────┼────────────────────────┐             │
│         ▼                        ▼                        ▼             │
│  ┌─────────────┐         ┌─────────────┐         ┌─────────────┐       │
│  │   3D LAYER  │         │  2D OVERLAY │         │   EFFECT    │       │
│  │   (WebGL)   │         │  (DOM/SVG)  │         │   COMPOSER  │       │
│  │             │         │             │         │             │       │
│  │ • Holo panels│        │ • Callouts  │         │ • Bloom     │       │
│  │ • 3D reticles│        │ • Text HUD  │         │ • Glitch    │       │
│  │ • Particles │         │ • Data bars │         │ • Scanlines │       │
│  └─────────────┘         └─────────────┘         └─────────────┘       │
│         │                        │                        │             │
│         └────────────────────────┼────────────────────────┘             │
│                                  ▼                                       │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │                      INTERACTION HANDLER                            │ │
│  │  • Mouse/touch tracking    • Keyboard shortcuts                     │ │
│  │  • Hover states            • Focus management                       │ │
│  │  • Click/tap events        • Gesture recognition                    │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                  │                                       │
│                                  ▼                                       │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │                         STATE STORE                                 │ │
│  │  • Active callouts         • Animation states                       │ │
│  │  • Panel visibility        • Data streams                           │ │
│  │  • Focus targets           • Alert levels                           │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                  │                                       │
│                                  ▼                                       │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │                        AUDIO ENGINE                                 │ │
│  │  • UI sound sprites        • Ambient loops                          │ │
│  │  • Spatial audio           • Dynamic mixing                         │ │
│  └────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────┘
```

### 3.2 Layer System Design

The HUD operates on a multi-layer z-index system allowing independent rendering and compositing:

```typescript
enum HUDLayer {
  BACKGROUND = 0,      // Ambient effects, star fields
  HOLOGRAM_FAR = 1,    // Distant holographic panels (z: -200)
  HOLOGRAM_MID = 2,    // Primary interface panels (z: -100)
  HOLOGRAM_NEAR = 3,   // Foreground elements (z: -50)
  WORLD_MARKERS = 4,   // 3D-to-2D projected elements
  OVERLAY_BACK = 5,    // Static HUD frame
  OVERLAY_MID = 6,     // Dynamic content, callouts
  OVERLAY_FRONT = 7,   // Alerts, tooltips, focus indicators
  CURSOR = 8,          // Custom cursor, reticle
  EFFECTS = 9          // Post-processing (full-screen)
}
```

### 3.3 State Management Schema

```typescript
interface HUDState {
  // System State
  systemPower: 'boot' | 'active' | 'standby' | 'critical' | 'shutdown';
  alertLevel: 'normal' | 'caution' | 'warning' | 'critical';
  
  // Callout System
  callouts: Map<string, CalloutData>;
  activeCalloutId: string | null;
  
  // Panel System
  panels: Map<string, PanelData>;
  panelFocusStack: string[];
  
  // Animation State
  globalAnimationSpeed: number;  // 0-2 multiplier
  reducedMotion: boolean;
  
  // Cursor State
  cursorPosition: { x: number; y: number };
  cursorMode: 'default' | 'target' | 'interact' | 'drag';
  
  // Data Streams
  dataFeeds: Map<string, DataFeedConfig>;
}

interface CalloutData {
  id: string;
  originPoint: { x: number; y: number };
  anchorDirection: 'top-left' | 'top-right' | 'bottom-left' | 'bottom-right';
  lineSegments: LineSegment[];
  panelContent: React.ReactNode;
  state: 'spawning' | 'active' | 'dismissing' | 'hidden';
  spawnTimestamp: number;
}

interface PanelData {
  id: string;
  position: { x: number; y: number; z: number };
  rotation: { x: number; y: number; z: number };
  dimensions: { width: number; height: number };
  opacity: number;
  content: React.ReactNode;
  layer: HUDLayer;
  interactable: boolean;
}
```

---

## 4. Rendering Pipeline

### 4.1 Frame Update Sequence

```
┌─────────────────────────────────────────────────────────────────────────┐
│  FRAME START (requestAnimationFrame)                                     │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  1. INPUT PROCESSING                                                     │
│     • Poll input devices                                                 │
│     • Update cursor position                                             │
│     • Process queued events                                              │
│                        ↓                                                 │
│  2. STATE UPDATE                                                         │
│     • Apply state mutations                                              │
│     • Calculate derived state                                            │
│     • Trigger animation state machines                                   │
│                        ↓                                                 │
│  3. ANIMATION TICK                                                       │
│     • Update GSAP timeline                                               │
│     • Interpolate spring values                                          │
│     • Calculate particle positions                                       │
│                        ↓                                                 │
│  4. 3D SCENE RENDER (Three.js)                                           │
│     • Update camera matrices                                             │
│     • Render hologram layers (back to front)                             │
│     • Render particle systems                                            │
│                        ↓                                                 │
│  5. POST-PROCESSING                                                      │
│     • Apply bloom pass                                                   │
│     • Apply chromatic aberration                                         │
│     • Apply scan lines / noise                                           │
│                        ↓                                                 │
│  6. 2D OVERLAY COMPOSITE                                                 │
│     • Render DOM/SVG HUD elements                                        │
│     • Position callouts and panels                                       │
│     • Update data stream text                                            │
│                        ↓                                                 │
│  7. AUDIO SYNC                                                           │
│     • Trigger frame-synced sounds                                        │
│     • Update spatial audio positions                                     │
│                                                                          │
├─────────────────────────────────────────────────────────────────────────┤
│  FRAME END → VSYNC WAIT                                                  │
└─────────────────────────────────────────────────────────────────────────┘
```

### 4.2 Coordinate Systems

The HUD uses three coordinate systems that must be synchronized:

```typescript
// 1. SCREEN SPACE (2D Overlay)
// Origin: top-left, units: pixels
interface ScreenCoord {
  x: number;  // 0 to viewport.width
  y: number;  // 0 to viewport.height
}

// 2. NORMALIZED DEVICE COORDINATES (NDC)
// Origin: center, units: -1 to 1
interface NDCCoord {
  x: number;  // -1 (left) to 1 (right)
  y: number;  // -1 (bottom) to 1 (top)
}

// 3. WORLD SPACE (3D Scene)
// Origin: camera position, units: arbitrary
interface WorldCoord {
  x: number;
  y: number;
  z: number;
}

// Conversion utilities
function screenToNDC(screen: ScreenCoord, viewport: Size): NDCCoord {
  return {
    x: (screen.x / viewport.width) * 2 - 1,
    y: -(screen.y / viewport.height) * 2 + 1
  };
}

function worldToScreen(world: WorldCoord, camera: Camera, viewport: Size): ScreenCoord {
  const vector = new Vector3(world.x, world.y, world.z);
  vector.project(camera);
  return {
    x: (vector.x * 0.5 + 0.5) * viewport.width,
    y: (-vector.y * 0.5 + 0.5) * viewport.height
  };
}
```

---

## 5. Animation System

### 5.1 Animation Timing Constants

```typescript
const TIMING = {
  // Micro-interactions (immediate feedback)
  INSTANT: 0,
  SNAP: 50,           // Button press, toggle
  QUICK: 100,         // Hover state change
  
  // Standard transitions
  FAST: 200,          // Panel slide, fade
  NORMAL: 300,        // Default transition
  MODERATE: 400,      // Callout extension
  
  // Elaborate animations
  SLOW: 600,          // Complex reveals
  DRAMATIC: 1000,     // Boot sequences
  CINEMATIC: 2000,    // Scene transitions
  
  // Stagger delays
  STAGGER_TIGHT: 30,  // List items
  STAGGER_NORMAL: 50, // Grid elements
  STAGGER_LOOSE: 100  // Major elements
} as const;

const EASING = {
  // Sharp, mechanical feel
  SHARP_IN: [0.4, 0, 1, 1],
  SHARP_OUT: [0, 0, 0.2, 1],
  SHARP_IN_OUT: [0.4, 0, 0.2, 1],
  
  // Smooth, organic feel
  SMOOTH_IN: [0.4, 0, 0.6, 1],
  SMOOTH_OUT: [0.2, 0, 0.4, 1],
  SMOOTH_IN_OUT: [0.4, 0, 0.2, 1],
  
  // Bounce/overshoot (use sparingly)
  OVERSHOOT: [0.34, 1.56, 0.64, 1],
  BOUNCE: 'bounce',
  
  // Spring physics
  SPRING_SNAPPY: { stiffness: 400, damping: 30 },
  SPRING_GENTLE: { stiffness: 200, damping: 20 },
  SPRING_WOBBLY: { stiffness: 180, damping: 12 }
} as const;
```

### 5.2 Animation State Machines

Each animated component implements a finite state machine:

```typescript
// Generic HUD Element State Machine
type ElementState = 
  | 'hidden'      // Not rendered
  | 'spawning'    // Entry animation playing
  | 'idle'        // Visible, ambient animation
  | 'hover'       // Mouse over
  | 'active'      // Clicked/selected
  | 'disabled'    // Non-interactive
  | 'dismissing'  // Exit animation playing
  | 'error';      // Error state flash

interface StateTransition {
  from: ElementState;
  to: ElementState;
  animation: AnimationDefinition;
  duration: number;
  audio?: string;
}

const PANEL_TRANSITIONS: StateTransition[] = [
  {
    from: 'hidden',
    to: 'spawning',
    animation: {
      initial: { opacity: 0, scale: 0.8, y: 20 },
      animate: { opacity: 1, scale: 1, y: 0 }
    },
    duration: TIMING.MODERATE,
    audio: 'ui_panel_open'
  },
  {
    from: 'spawning',
    to: 'idle',
    animation: null,  // Immediate transition
    duration: 0
  },
  {
    from: 'idle',
    to: 'hover',
    animation: {
      animate: { borderColor: 'rgba(0, 255, 255, 0.8)' }
    },
    duration: TIMING.QUICK
  },
  // ... additional transitions
];
```

### 5.3 Keyframe Animation Definitions

Standard animations defined as reusable keyframe sequences:

```typescript
// PULSE GLOW - Continuous ambient glow pulsing
const pulseGlow = {
  keyframes: {
    '0%, 100%': {
      boxShadow: '0 0 10px rgba(0, 255, 255, 0.3), inset 0 0 10px rgba(0, 255, 255, 0.1)'
    },
    '50%': {
      boxShadow: '0 0 20px rgba(0, 255, 255, 0.6), inset 0 0 20px rgba(0, 255, 255, 0.2)'
    }
  },
  duration: 2000,
  iteration: 'infinite',
  easing: 'ease-in-out'
};

// SCAN LINE - Vertical traveling line
const scanLine = {
  keyframes: {
    '0%': { transform: 'translateY(-100%)' },
    '100%': { transform: 'translateY(100vh)' }
  },
  duration: 3000,
  iteration: 'infinite',
  easing: 'linear'
};

// DATA FLICKER - Randomized opacity flicker
const dataFlicker = {
  keyframes: {
    '0%, 100%': { opacity: 1 },
    '10%': { opacity: 0.8 },
    '20%': { opacity: 1 },
    '30%': { opacity: 0.9 },
    '40%': { opacity: 1 },
    '50%': { opacity: 0.7 },
    '60%': { opacity: 1 },
    '70%': { opacity: 0.85 },
    '80%': { opacity: 1 },
    '90%': { opacity: 0.95 }
  },
  duration: 200,
  iteration: 'infinite',
  easing: 'steps(10)'
};

// BRACKET EXPAND - Corner brackets expanding outward
const bracketExpand = {
  keyframes: {
    '0%': { 
      clipPath: 'polygon(0 0, 30% 0, 30% 10%, 10% 10%, 10% 30%, 0 30%)'
    },
    '100%': { 
      clipPath: 'polygon(0 0, 40% 0, 40% 5%, 5% 5%, 5% 40%, 0 40%)'
    }
  },
  duration: 300,
  easing: EASING.SHARP_OUT
};

// GLITCH DISPLACEMENT - RGB channel separation
const glitchDisplacement = {
  keyframes: {
    '0%, 100%': { 
      textShadow: '0 0 0 transparent, 0 0 0 transparent' 
    },
    '25%': { 
      textShadow: '-2px 0 0 rgba(255, 0, 0, 0.7), 2px 0 0 rgba(0, 255, 255, 0.7)' 
    },
    '50%': { 
      textShadow: '2px 0 0 rgba(255, 0, 0, 0.7), -2px 0 0 rgba(0, 255, 255, 0.7)' 
    },
    '75%': { 
      textShadow: '-1px 0 0 rgba(255, 0, 0, 0.5), 1px 0 0 rgba(0, 255, 255, 0.5)' 
    }
  },
  duration: 150,
  iteration: 3,
  easing: 'steps(4)'
};
```

---

## 6. Component Library

### 6.1 Callout System

The callout is a signature Sci-Fi UI element connecting points of interest to information panels.

#### 6.1.1 Anatomy of a Callout

```
                                    ┌─────────────────────────┐
                                    │  INFORMATION PANEL      │
                                    │  ┌─────────────────────┐│
                                    │  │ Title               ││
    [ORIGIN POINT]                  │  │ Data: 1,234.56      ││
          ●━━━━━━━━━━━┓             │  │ Status: ACTIVE      ││
              Segment 1 ┃           │  └─────────────────────┘│
                        ┃           └──────────┬──────────────┘
                        ┃ Segment 2            │
                        ┗━━━━━━━━━━━━━━━━━━━━━━┛
                                  Segment 3 (horizontal connector)
```

#### 6.1.2 Callout Configuration

```typescript
interface CalloutConfig {
  // Origin
  originPoint: { x: number; y: number };
  originMarker: 'dot' | 'ring' | 'crosshair' | 'diamond' | 'none';
  
  // Path Configuration
  pathStyle: 'angular' | 'curved' | 'stepped' | 'direct';
  anchorDirection: 'auto' | 'top-left' | 'top-right' | 'bottom-left' | 'bottom-right';
  segmentCount: 2 | 3 | 4;
  
  // Visual Style
  lineColor: string;
  lineWidth: number;
  lineDash: number[] | null;  // e.g., [5, 3] for dashed
  glowIntensity: number;      // 0-1
  
  // Panel
  panelWidth: number;
  panelContent: React.ReactNode;
  panelAnchor: 'start' | 'center' | 'end';
  
  // Animation
  spawnDuration: number;
  drawSpeed: number;  // pixels per second
  staggerDelay: number;
}
```

#### 6.1.3 Callout Animation Sequence

```
TIME ──────────────────────────────────────────────────────────────────►

[0ms]     ORIGIN MARKER SPAWN
          • Scale from 0 → 1 with overshoot
          • Opacity 0 → 1
          • Ring pulse effect
          
[100ms]   SEGMENT 1 DRAW
          • Line draws from origin outward
          • SVG stroke-dashoffset animation
          • Glow follows line tip
          
[200ms]   SEGMENT 2 DRAW
          • Continues from segment 1 endpoint
          • Same drawing animation
          
[300ms]   SEGMENT 3 DRAW (if applicable)
          • Horizontal extension to panel
          
[400ms]   PANEL REVEAL
          • Clip-path wipe from connector side
          • Content fades in with 50ms stagger
          • Border glow activates
          
[600ms]   IDLE STATE
          • Subtle pulse on origin marker
          • Data content updates allowed
```

#### 6.1.4 Callout Path Calculation

```typescript
function calculateCalloutPath(
  origin: Point,
  panelRect: Rect,
  direction: AnchorDirection,
  style: PathStyle
): PathSegment[] {
  const segments: PathSegment[] = [];
  
  // Determine optimal anchor direction if auto
  if (direction === 'auto') {
    direction = determineOptimalDirection(origin, panelRect, viewport);
  }
  
  // Calculate intermediate points based on style
  switch (style) {
    case 'angular':
      // 45-degree angle from origin, then horizontal
      const angle = direction.includes('right') ? 45 : 135;
      const distance = 60;
      const midPoint = {
        x: origin.x + Math.cos(angle * DEG2RAD) * distance,
        y: origin.y - Math.sin(angle * DEG2RAD) * distance
      };
      
      segments.push(
        { start: origin, end: midPoint, type: 'diagonal' },
        { start: midPoint, end: { x: panelRect.x, y: midPoint.y }, type: 'horizontal' }
      );
      break;
      
    case 'stepped':
      // Vertical then horizontal segments
      const verticalEnd = { x: origin.x, y: panelRect.y + panelRect.height / 2 };
      segments.push(
        { start: origin, end: verticalEnd, type: 'vertical' },
        { start: verticalEnd, end: { x: panelRect.x, y: verticalEnd.y }, type: 'horizontal' }
      );
      break;
      
    case 'curved':
      // Bezier curve - single segment with control points
      segments.push({
        start: origin,
        end: { x: panelRect.x, y: panelRect.y + panelRect.height / 2 },
        type: 'bezier',
        controlPoints: calculateBezierControls(origin, panelRect, direction)
      });
      break;
  }
  
  return segments;
}
```

### 6.2 Holographic Panel System

#### 6.2.1 Panel Layer Stack

```
VIEWER
   │
   ▼
┌──────────────────────────────────────────────────────────────────┐
│  NEAR LAYER (z: -50)                                              │
│  • Primary interaction panels                                     │
│  • Highest opacity (0.9)                                          │
│  • Sharpest rendering                                             │
└──────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────────┐
│  MID LAYER (z: -100)                                              │
│  • Secondary information                                          │
│  • Medium opacity (0.6)                                           │
│  • Slight blur (1px)                                              │
└──────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────────┐
│  FAR LAYER (z: -200)                                              │
│  • Ambient decoration                                             │
│  • Low opacity (0.3)                                              │
│  • Heavy blur (3px)                                               │
│  • Parallax movement                                              │
└──────────────────────────────────────────────────────────────────┘
```

#### 6.2.2 Panel Configuration

```typescript
interface HoloPanelConfig {
  // Positioning
  position: { x: number; y: number; z: number };
  rotation: { x: number; y: number; z: number };  // Euler angles in degrees
  scale: number;
  
  // Dimensions
  width: number;
  height: number;
  cornerRadius: number;
  
  // Visual Properties
  backgroundColor: string;       // Usually rgba with low alpha
  borderColor: string;
  borderWidth: number;
  opacity: number;
  blur: number;                  // Backdrop blur
  
  // Effects
  scanLines: boolean;
  scanLineOpacity: number;
  scanLineSpeed: number;
  
  noise: boolean;
  noiseOpacity: number;
  
  glowColor: string;
  glowIntensity: number;
  glowSpread: number;
  
  // Interaction
  interactable: boolean;
  hoverScale: number;           // e.g., 1.02 for subtle grow
  hoverGlowBoost: number;       // Multiplier for glow on hover
  
  // Content
  header?: {
    title: string;
    icon?: React.ReactNode;
    statusIndicator?: 'active' | 'inactive' | 'warning' | 'critical';
  };
  content: React.ReactNode;
  footer?: React.ReactNode;
}
```

#### 6.2.3 Panel CSS Implementation

```css
.holo-panel {
  /* Base structure */
  position: absolute;
  transform-style: preserve-3d;
  
  /* Glass effect */
  background: linear-gradient(
    135deg,
    rgba(0, 20, 40, 0.8) 0%,
    rgba(0, 40, 60, 0.6) 50%,
    rgba(0, 20, 40, 0.8) 100%
  );
  backdrop-filter: blur(10px);
  
  /* Border */
  border: 1px solid rgba(0, 255, 255, 0.3);
  border-radius: 4px;
  
  /* Glow effect */
  box-shadow:
    0 0 20px rgba(0, 255, 255, 0.2),
    inset 0 0 20px rgba(0, 255, 255, 0.05),
    0 0 1px rgba(0, 255, 255, 0.8);
  
  /* Scan line overlay */
  &::before {
    content: '';
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(
      0deg,
      transparent 0px,
      transparent 2px,
      rgba(0, 255, 255, 0.03) 2px,
      rgba(0, 255, 255, 0.03) 4px
    );
    pointer-events: none;
    animation: scanLineScroll 8s linear infinite;
  }
  
  /* Noise texture overlay */
  &::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: url('data:image/svg+xml,...'); /* Noise SVG */
    opacity: 0.05;
    pointer-events: none;
  }
}

@keyframes scanLineScroll {
  0% { background-position: 0 0; }
  100% { background-position: 0 100px; }
}

/* Corner bracket decorations */
.holo-panel__bracket {
  position: absolute;
  width: 20px;
  height: 20px;
  border-color: rgba(0, 255, 255, 0.6);
  border-style: solid;
  border-width: 0;
}

.holo-panel__bracket--top-left {
  top: -1px;
  left: -1px;
  border-top-width: 2px;
  border-left-width: 2px;
}

.holo-panel__bracket--top-right {
  top: -1px;
  right: -1px;
  border-top-width: 2px;
  border-right-width: 2px;
}

/* Additional corners follow same pattern */
```

### 6.3 Reticle / Targeting System

#### 6.3.1 Reticle Types

```typescript
type ReticleType = 
  | 'standard'      // Simple crosshair
  | 'targeting'     // Animated lock-on circles
  | 'scanning'      // Rotating arc segments
  | 'analysis'      // Data readout overlay
  | 'selection'     // Multi-target selection
  | 'navigation';   // Waypoint marker
  
interface ReticleConfig {
  type: ReticleType;
  size: number;
  color: string;
  thickness: number;
  
  // Segments (for circular reticles)
  segments: number;
  segmentGap: number;      // Degrees
  rotationSpeed: number;   // Degrees per second
  
  // Inner elements
  innerRing: boolean;
  innerRingSize: number;
  centerDot: boolean;
  centerDotSize: number;
  
  // Animation
  pulseEnabled: boolean;
  pulseFrequency: number;
  pulseAmplitude: number;
  
  // State-based modifications
  states: {
    idle: Partial<ReticleConfig>;
    hover: Partial<ReticleConfig>;
    locked: Partial<ReticleConfig>;
    tracking: Partial<ReticleConfig>;
  };
}
```

#### 6.3.2 Reticle SVG Structure

```svg
<svg class="reticle reticle--targeting" viewBox="-50 -50 100 100">
  <!-- Outer rotating ring -->
  <g class="reticle__outer-ring" style="animation: rotate 4s linear infinite">
    <circle 
      cx="0" cy="0" r="40" 
      fill="none" 
      stroke="currentColor" 
      stroke-width="1"
      stroke-dasharray="20 10"
      opacity="0.6"
    />
  </g>
  
  <!-- Middle segments (counter-rotate) -->
  <g class="reticle__mid-segments" style="animation: rotate 6s linear infinite reverse">
    <path d="M 0,-30 A 30,30 0 0,1 26,15" fill="none" stroke="currentColor" stroke-width="2"/>
    <path d="M 26,15 A 30,30 0 0,1 -26,15" fill="none" stroke="currentColor" stroke-width="2"/>
    <path d="M -26,15 A 30,30 0 0,1 0,-30" fill="none" stroke="currentColor" stroke-width="2"/>
  </g>
  
  <!-- Crosshair -->
  <g class="reticle__crosshair">
    <line x1="-20" y1="0" x2="-8" y2="0" stroke="currentColor" stroke-width="1"/>
    <line x1="8" y1="0" x2="20" y2="0" stroke="currentColor" stroke-width="1"/>
    <line x1="0" y1="-20" x2="0" y2="-8" stroke="currentColor" stroke-width="1"/>
    <line x1="0" y1="8" x2="0" y2="20" stroke="currentColor" stroke-width="1"/>
  </g>
  
  <!-- Center dot (pulses) -->
  <circle 
    class="reticle__center" 
    cx="0" cy="0" r="3" 
    fill="currentColor"
    style="animation: pulse 1s ease-in-out infinite"
  />
  
  <!-- Corner brackets -->
  <g class="reticle__brackets">
    <path d="M -35,-35 L -35,-25 M -35,-35 L -25,-35" fill="none" stroke="currentColor" stroke-width="1"/>
    <path d="M 35,-35 L 35,-25 M 35,-35 L 25,-35" fill="none" stroke="currentColor" stroke-width="1"/>
    <path d="M -35,35 L -35,25 M -35,35 L -25,35" fill="none" stroke="currentColor" stroke-width="1"/>
    <path d="M 35,35 L 35,25 M 35,35 L 25,35" fill="none" stroke="currentColor" stroke-width="1"/>
  </g>
</svg>
```

### 6.4 Data Display Components

#### 6.4.1 Data Stream (Scrolling Text)

```typescript
interface DataStreamConfig {
  // Content
  dataSource: () => string | string[];  // Generator function
  updateInterval: number;               // Milliseconds between updates
  
  // Display
  lines: number;                        // Visible line count
  charactersPerLine: number;
  fontFamily: 'mono' | 'display';
  fontSize: number;
  
  // Animation
  scrollDirection: 'up' | 'down';
  scrollSpeed: number;                  // Pixels per second
  fadeEdges: boolean;
  fadeDistance: number;
  
  // Effects
  flickerEnabled: boolean;
  flickerIntensity: number;
  glitchProbability: number;            // 0-1, chance per frame
  
  // Colors
  textColor: string;
  highlightColor: string;               // For important values
  dimColor: string;                     // For less important data
}

// Example data generator
function generateSystemLog(): string {
  const prefixes = ['SYS', 'NET', 'SEC', 'MEM', 'CPU', 'I/O'];
  const actions = ['INIT', 'SYNC', 'SCAN', 'LOAD', 'CHECK', 'VERIFY'];
  const statuses = ['OK', 'DONE', 'PASS', 'ACTIVE'];
  
  const prefix = prefixes[Math.floor(Math.random() * prefixes.length)];
  const action = actions[Math.floor(Math.random() * actions.length)];
  const status = statuses[Math.floor(Math.random() * statuses.length)];
  const value = Math.floor(Math.random() * 9999).toString().padStart(4, '0');
  
  return `[${prefix}] ${action}::${value} ... ${status}`;
}
```

#### 6.4.2 Gauge / Meter Components

```typescript
interface GaugeConfig {
  type: 'arc' | 'linear' | 'circular' | 'segmented';
  
  // Value
  value: number;
  min: number;
  max: number;
  
  // Visual
  size: number;
  thickness: number;
  backgroundColor: string;
  fillColor: string;
  
  // Arc-specific
  startAngle?: number;      // Degrees, 0 = top
  endAngle?: number;
  
  // Segmented-specific
  segments?: number;
  segmentGap?: number;
  
  // Labels
  showValue: boolean;
  valueFormat: (v: number) => string;
  showTicks: boolean;
  tickCount: number;
  
  // Thresholds
  thresholds?: Array<{
    value: number;
    color: string;
    label?: string;
  }>;
  
  // Animation
  animateOnChange: boolean;
  animationDuration: number;
  overshoot: boolean;
}

// Arc gauge SVG path calculation
function calculateArcPath(
  centerX: number,
  centerY: number,
  radius: number,
  startAngle: number,
  endAngle: number
): string {
  const start = polarToCartesian(centerX, centerY, radius, endAngle);
  const end = polarToCartesian(centerX, centerY, radius, startAngle);
  const largeArcFlag = endAngle - startAngle <= 180 ? 0 : 1;
  
  return [
    'M', start.x, start.y,
    'A', radius, radius, 0, largeArcFlag, 0, end.x, end.y
  ].join(' ');
}
```

### 6.5 Alert / Notification System

#### 6.5.1 Alert Levels

```typescript
enum AlertLevel {
  INFO = 'info',         // Blue/cyan, subtle
  SUCCESS = 'success',   // Green, confirmatory
  CAUTION = 'caution',   // Yellow/amber, attention
  WARNING = 'warning',   // Orange, important
  CRITICAL = 'critical'  // Red, urgent/danger
}

const ALERT_STYLES: Record<AlertLevel, AlertStyle> = {
  [AlertLevel.INFO]: {
    primaryColor: '#00FFFF',
    backgroundColor: 'rgba(0, 255, 255, 0.1)',
    borderColor: 'rgba(0, 255, 255, 0.4)',
    glowColor: 'rgba(0, 255, 255, 0.3)',
    icon: 'info-circle',
    sound: 'ui_alert_info',
    animation: 'fadeSlideIn'
  },
  [AlertLevel.CRITICAL]: {
    primaryColor: '#FF0040',
    backgroundColor: 'rgba(255, 0, 64, 0.15)',
    borderColor: 'rgba(255, 0, 64, 0.6)',
    glowColor: 'rgba(255, 0, 64, 0.4)',
    icon: 'alert-triangle',
    sound: 'ui_alert_critical',
    animation: 'pulseFlash'
  }
  // ... other levels
};
```

#### 6.5.2 Alert Animation Sequence

```
CRITICAL ALERT SPAWN:

[0ms]     FLASH OVERLAY
          • Full-screen red tint flash (opacity 0 → 0.3 → 0)
          • Duration: 100ms

[50ms]    AUDIO CUE
          • Play alert sound
          • Optional: screen shake (2px amplitude, 100ms)

[100ms]   ALERT BOX SPAWN
          • Scale from 1.1 → 1 (slight shrink-in)
          • Opacity 0 → 1
          • Border draws clockwise (stroke-dashoffset)

[200ms]   CONTENT REVEAL
          • Icon pulses
          • Title types in (optional)
          • Body text fades in

[400ms]   IDLE STATE
          • Border pulses slowly
          • Glow breathes
          • Icon continues subtle animation

DISMISS (USER OR AUTO):

[0ms]     GLITCH EFFECT
          • Brief RGB split
          • Horizontal slice displacement

[100ms]   COLLAPSE
          • Height shrinks to 0
          • Opacity fades
          • Optional: shatter particle effect
```

---

## 7. Visual Effects Catalog

### 7.1 Post-Processing Effects

#### 7.1.1 Bloom Effect

```typescript
// React Three Fiber + Postprocessing implementation
import { EffectComposer, Bloom } from '@react-three/postprocessing';
import { BloomEffect, KernelSize } from 'postprocessing';

interface BloomConfig {
  intensity: number;          // 0-3, default 1
  luminanceThreshold: number; // 0-1, pixels above this glow
  luminanceSmoothing: number; // 0-1, transition smoothness
  kernelSize: KernelSize;     // Resolution of blur
  mipmapBlur: boolean;        // Higher quality, more GPU
}

const SCIFI_BLOOM_PRESET: BloomConfig = {
  intensity: 1.5,
  luminanceThreshold: 0.6,
  luminanceSmoothing: 0.3,
  kernelSize: KernelSize.LARGE,
  mipmapBlur: true
};
```

#### 7.1.2 Chromatic Aberration

```typescript
interface ChromaticAberrationConfig {
  offset: { x: number; y: number };  // Pixel offset for R/B channels
  radialModulation: boolean;          // Stronger at edges
  animateOffset: boolean;             // Subtle drift
}

// GLSL shader snippet
const chromaticAberrationShader = `
  uniform vec2 uOffset;
  uniform sampler2D tDiffuse;
  varying vec2 vUv;
  
  void main() {
    vec2 direction = vUv - 0.5;
    float dist = length(direction);
    
    vec2 aberration = uOffset * dist * dist;
    
    float r = texture2D(tDiffuse, vUv + aberration).r;
    float g = texture2D(tDiffuse, vUv).g;
    float b = texture2D(tDiffuse, vUv - aberration).b;
    
    gl_FragColor = vec4(r, g, b, 1.0);
  }
`;
```

#### 7.1.3 Scan Lines Effect

```typescript
interface ScanLinesConfig {
  density: number;         // Lines per pixel
  opacity: number;         // 0-1
  speed: number;           // Scroll speed (0 for static)
  flickerIntensity: number;
  colorTint: string;       // Usually matches theme
}

// CSS implementation (performant for 2D overlay)
const scanLinesCSS = `
  .scan-lines {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 9999;
    
    background: repeating-linear-gradient(
      to bottom,
      transparent 0px,
      transparent 2px,
      rgba(0, 0, 0, 0.1) 2px,
      rgba(0, 0, 0, 0.1) 4px
    );
    
    animation: scanLineFlicker 0.1s steps(2) infinite;
  }
  
  @keyframes scanLineFlicker {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 0.85; }
  }
`;
```

#### 7.1.4 Glitch Effect

```typescript
interface GlitchConfig {
  // Trigger
  mode: 'continuous' | 'random' | 'triggered';
  probability: number;     // For random mode, 0-1
  
  // Visual
  sliceCount: number;      // Horizontal slice divisions
  maxDisplacement: number; // Pixels
  rgbSplit: boolean;
  colorInvert: boolean;
  
  // Timing
  duration: number;        // Single glitch duration
  cooldown: number;        // Min time between glitches
}

// Implementation approach: Canvas slice manipulation
function applyGlitchEffect(
  sourceCanvas: HTMLCanvasElement,
  targetCanvas: HTMLCanvasElement,
  config: GlitchConfig
): void {
  const ctx = targetCanvas.getContext('2d')!;
  const { width, height } = sourceCanvas;
  
  ctx.drawImage(sourceCanvas, 0, 0);
  
  const sliceHeight = height / config.sliceCount;
  
  for (let i = 0; i < config.sliceCount; i++) {
    if (Math.random() > 0.7) {
      const y = i * sliceHeight;
      const displacement = (Math.random() - 0.5) * config.maxDisplacement;
      
      // Cut slice and redraw with offset
      const imageData = ctx.getImageData(0, y, width, sliceHeight);
      ctx.putImageData(imageData, displacement, y);
    }
  }
  
  if (config.rgbSplit) {
    // Apply RGB channel separation
    applyRGBSplit(ctx, width, height);
  }
}
```

### 7.2 Particle Systems

#### 7.2.1 Ambient Particles

```typescript
interface AmbientParticleConfig {
  count: number;
  shape: 'circle' | 'square' | 'triangle' | 'line' | 'custom';
  
  // Size
  sizeMin: number;
  sizeMax: number;
  
  // Movement
  velocityMin: { x: number; y: number };
  velocityMax: { x: number; y: number };
  turbulence: number;        // Random velocity variation
  
  // Appearance
  color: string | string[];  // Single or gradient palette
  opacity: number;
  blendMode: 'normal' | 'add' | 'screen';
  
  // Behavior
  respawnOnExit: boolean;
  fadeInOut: boolean;
  connectLines: boolean;     // Draw lines between nearby particles
  connectDistance: number;
}

// Three.js Points implementation
function createAmbientParticles(config: AmbientParticleConfig): THREE.Points {
  const geometry = new THREE.BufferGeometry();
  const positions = new Float32Array(config.count * 3);
  const velocities = new Float32Array(config.count * 3);
  const sizes = new Float32Array(config.count);
  
  for (let i = 0; i < config.count; i++) {
    positions[i * 3] = (Math.random() - 0.5) * 200;
    positions[i * 3 + 1] = (Math.random() - 0.5) * 200;
    positions[i * 3 + 2] = (Math.random() - 0.5) * 50;
    
    sizes[i] = config.sizeMin + Math.random() * (config.sizeMax - config.sizeMin);
    
    velocities[i * 3] = lerp(config.velocityMin.x, config.velocityMax.x, Math.random());
    velocities[i * 3 + 1] = lerp(config.velocityMin.y, config.velocityMax.y, Math.random());
    velocities[i * 3 + 2] = 0;
  }
  
  geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
  geometry.setAttribute('size', new THREE.BufferAttribute(sizes, 1));
  
  const material = new THREE.PointsMaterial({
    size: 2,
    color: config.color as string,
    transparent: true,
    opacity: config.opacity,
    blending: THREE.AdditiveBlending,
    depthWrite: false
  });
  
  const points = new THREE.Points(geometry, material);
  points.userData.velocities = velocities;
  
  return points;
}
```

#### 7.2.2 Energy Trail Effect

```typescript
interface EnergyTrailConfig {
  // Source
  followTarget: THREE.Object3D | null;
  emitPosition: { x: number; y: number; z: number };
  
  // Trail
  length: number;           // Number of segments
  width: number;
  taperEnd: boolean;        // Thin towards tail
  
  // Color
  colorStart: string;
  colorEnd: string;
  emissiveIntensity: number;
  
  // Behavior
  decayRate: number;        // How quickly trail fades
  smoothing: number;        // Position interpolation factor
}

// Implementation: Ribbon geometry with vertex color gradient
class EnergyTrail {
  private positions: THREE.Vector3[] = [];
  private geometry: THREE.BufferGeometry;
  private mesh: THREE.Mesh;
  
  update(newPosition: THREE.Vector3): void {
    // Add new position to front
    this.positions.unshift(newPosition.clone());
    
    // Remove excess
    while (this.positions.length > this.config.length) {
      this.positions.pop();
    }
    
    // Rebuild geometry
    this.rebuildGeometry();
  }
  
  private rebuildGeometry(): void {
    const vertices: number[] = [];
    const colors: number[] = [];
    
    for (let i = 0; i < this.positions.length - 1; i++) {
      const t = i / (this.positions.length - 1);
      const width = this.config.taperEnd 
        ? this.config.width * (1 - t) 
        : this.config.width;
      
      // Calculate perpendicular for ribbon width
      const direction = this.positions[i + 1].clone().sub(this.positions[i]).normalize();
      const perpendicular = new THREE.Vector3(-direction.y, direction.x, 0).multiplyScalar(width);
      
      // Add two vertices (ribbon edges)
      const left = this.positions[i].clone().add(perpendicular);
      const right = this.positions[i].clone().sub(perpendicular);
      
      vertices.push(left.x, left.y, left.z, right.x, right.y, right.z);
      
      // Interpolate color
      const color = lerpColor(this.config.colorStart, this.config.colorEnd, t);
      colors.push(color.r, color.g, color.b, color.r, color.g, color.b);
    }
    
    this.geometry.setAttribute('position', new THREE.Float32BufferAttribute(vertices, 3));
    this.geometry.setAttribute('color', new THREE.Float32BufferAttribute(colors, 3));
  }
}
```

### 7.3 Hologram Effect Shaders

#### 7.3.1 Hologram Material

```glsl
// Vertex Shader
varying vec2 vUv;
varying vec3 vNormal;
varying vec3 vPosition;

void main() {
  vUv = uv;
  vNormal = normalize(normalMatrix * normal);
  vPosition = position;
  gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
}

// Fragment Shader
uniform float uTime;
uniform vec3 uColor;
uniform float uOpacity;
uniform float uScanLineIntensity;
uniform float uScanLineCount;
uniform float uFlickerSpeed;
uniform float uRimPower;
uniform float uGlitchIntensity;

varying vec2 vUv;
varying vec3 vNormal;
varying vec3 vPosition;

// Noise function for glitch
float random(vec2 st) {
  return fract(sin(dot(st.xy, vec2(12.9898, 78.233))) * 43758.5453123);
}

void main() {
  // Base color
  vec3 color = uColor;
  
  // Fresnel rim effect
  vec3 viewDirection = normalize(cameraPosition - vPosition);
  float fresnel = pow(1.0 - abs(dot(viewDirection, vNormal)), uRimPower);
  color += fresnel * uColor * 0.5;
  
  // Scan lines
  float scanLine = sin(vUv.y * uScanLineCount + uTime * 2.0) * 0.5 + 0.5;
  scanLine = pow(scanLine, 8.0) * uScanLineIntensity;
  color -= scanLine;
  
  // Flicker
  float flicker = sin(uTime * uFlickerSpeed) * 0.5 + 0.5;
  flicker = flicker * 0.1 + 0.9; // Keep between 0.9 and 1.0
  color *= flicker;
  
  // Horizontal glitch lines
  if (uGlitchIntensity > 0.0) {
    float glitchLine = step(0.99, random(vec2(floor(vUv.y * 50.0), floor(uTime * 10.0))));
    float displacement = (random(vec2(uTime)) - 0.5) * 0.1 * uGlitchIntensity;
    color = mix(color, uColor * 1.5, glitchLine * uGlitchIntensity);
  }
  
  // Alpha falloff at edges
  float edgeFade = smoothstep(0.0, 0.1, vUv.x) * smoothstep(1.0, 0.9, vUv.x);
  edgeFade *= smoothstep(0.0, 0.1, vUv.y) * smoothstep(1.0, 0.9, vUv.y);
  
  gl_FragColor = vec4(color, uOpacity * edgeFade);
}
```

---

## 8. Event System & Triggers

### 8.1 Event Architecture

```typescript
// Central event bus for HUD system
type HUDEventType =
  // Input Events
  | 'cursor:move'
  | 'cursor:click'
  | 'cursor:enter'
  | 'cursor:leave'
  | 'key:press'
  | 'key:release'
  // Component Events
  | 'panel:open'
  | 'panel:close'
  | 'panel:focus'
  | 'callout:spawn'
  | 'callout:dismiss'
  | 'alert:trigger'
  | 'alert:dismiss'
  // System Events
  | 'system:boot'
  | 'system:ready'
  | 'system:alert'
  | 'system:shutdown'
  // Animation Events
  | 'animation:start'
  | 'animation:complete'
  | 'animation:interrupt';

interface HUDEvent<T = unknown> {
  type: HUDEventType;
  payload: T;
  timestamp: number;
  source: string;        // Component ID that emitted
  bubbles: boolean;
  cancelable: boolean;
}

// Event bus implementation
class HUDEventBus {
  private listeners: Map<HUDEventType, Set<EventHandler>> = new Map();
  private eventQueue: HUDEvent[] = [];
  private processing: boolean = false;
  
  emit<T>(type: HUDEventType, payload: T, options?: EventOptions): void {
    const event: HUDEvent<T> = {
      type,
      payload,
      timestamp: performance.now(),
      source: options?.source ?? 'unknown',
      bubbles: options?.bubbles ?? true,
      cancelable: options?.cancelable ?? false
    };
    
    this.eventQueue.push(event);
    this.processQueue();
  }
  
  on<T>(type: HUDEventType, handler: EventHandler<T>): () => void {
    if (!this.listeners.has(type)) {
      this.listeners.set(type, new Set());
    }
    this.listeners.get(type)!.add(handler as EventHandler);
    
    // Return unsubscribe function
    return () => this.listeners.get(type)?.delete(handler as EventHandler);
  }
  
  private processQueue(): void {
    if (this.processing) return;
    this.processing = true;
    
    while (this.eventQueue.length > 0) {
      const event = this.eventQueue.shift()!;
      const handlers = this.listeners.get(event.type);
      
      if (handlers) {
        handlers.forEach(handler => {
          try {
            handler(event);
          } catch (error) {
            console.error(`HUD Event Handler Error [${event.type}]:`, error);
          }
        });
      }
    }
    
    this.processing = false;
  }
}
```

### 8.2 Interaction Handlers

#### 8.2.1 Click-to-Callout System

```typescript
interface CalloutTriggerConfig {
  // Activation
  triggerOn: 'click' | 'contextmenu' | 'longpress' | 'hover';
  longpressDuration?: number;
  hoverDelay?: number;
  
  // Behavior
  maxActiveCallouts: number;
  dismissOnClickOutside: boolean;
  dismissOnEscape: boolean;
  dismissAfterTimeout?: number;
  
  // Content
  contentProvider: (point: Point, context: any) => CalloutContent | Promise<CalloutContent>;
}

function setupCalloutTrigger(
  container: HTMLElement,
  config: CalloutTriggerConfig,
  eventBus: HUDEventBus,
  state: HUDState
): void {
  const handleTrigger = async (event: MouseEvent) => {
    // Prevent if max callouts reached
    const activeCount = [...state.callouts.values()]
      .filter(c => c.state !== 'hidden').length;
    
    if (activeCount >= config.maxActiveCallouts) {
      // Dismiss oldest callout first
      const oldest = [...state.callouts.entries()]
        .filter(([, c]) => c.state === 'active')
        .sort((a, b) => a[1].spawnTimestamp - b[1].spawnTimestamp)[0];
      
      if (oldest) {
        eventBus.emit('callout:dismiss', { id: oldest[0] });
      }
    }
    
    const originPoint = { x: event.clientX, y: event.clientY };
    
    // Fetch content (may be async)
    const content = await config.contentProvider(originPoint, {
      target: event.target,
      modifiers: {
        shift: event.shiftKey,
        ctrl: event.ctrlKey,
        alt: event.altKey
      }
    });
    
    // Emit spawn event
    eventBus.emit('callout:spawn', {
      id: `callout-${Date.now()}`,
      originPoint,
      content,
      direction: calculateOptimalDirection(originPoint, container.getBoundingClientRect())
    });
  };
  
  // Attach listeners based on trigger type
  switch (config.triggerOn) {
    case 'click':
      container.addEventListener('click', handleTrigger);
      break;
    case 'contextmenu':
      container.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        handleTrigger(e);
      });
      break;
    case 'longpress':
      setupLongPressHandler(container, handleTrigger, config.longpressDuration!);
      break;
  }
  
  // Dismiss handlers
  if (config.dismissOnClickOutside) {
    document.addEventListener('click', (e) => {
      if (!container.contains(e.target as Node)) {
        eventBus.emit('callout:dismiss', { all: true });
      }
    });
  }
  
  if (config.dismissOnEscape) {
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        eventBus.emit('callout:dismiss', { all: true });
      }
    });
  }
}
```

#### 8.2.2 Hover Effects System

```typescript
interface HoverEffectConfig {
  // Detection
  elements: string;          // CSS selector
  enterDelay: number;
  leaveDelay: number;
  
  // Effects
  effects: {
    glow?: { color: string; intensity: number; spread: number };
    scale?: { factor: number; origin: string };
    highlight?: { color: string; opacity: number };
    sound?: { enter: string; leave: string };
    cursor?: { mode: CursorMode };
  };
  
  // Callbacks
  onEnter?: (element: HTMLElement, event: MouseEvent) => void;
  onLeave?: (element: HTMLElement, event: MouseEvent) => void;
}

function setupHoverEffects(config: HoverEffectConfig): void {
  const elements = document.querySelectorAll(config.elements);
  let hoverTimeout: number | null = null;
  let activeElement: HTMLElement | null = null;
  
  const applyEffects = (element: HTMLElement, entering: boolean) => {
    const { effects } = config;
    
    if (effects.glow) {
      const shadow = entering
        ? `0 0 ${effects.glow.spread}px ${effects.glow.color}`
        : 'none';
      element.style.boxShadow = shadow;
    }
    
    if (effects.scale) {
      element.style.transform = entering
        ? `scale(${effects.scale.factor})`
        : 'scale(1)';
      element.style.transformOrigin = effects.scale.origin;
    }
    
    if (effects.highlight) {
      element.style.backgroundColor = entering
        ? effects.highlight.color
        : '';
    }
    
    if (effects.sound) {
      const soundId = entering ? effects.sound.enter : effects.sound.leave;
      audioEngine.play(soundId);
    }
    
    if (effects.cursor) {
      document.body.style.cursor = entering
        ? effects.cursor.mode
        : 'default';
    }
  };
  
  elements.forEach(element => {
    element.addEventListener('mouseenter', (event) => {
      if (config.enterDelay > 0) {
        hoverTimeout = window.setTimeout(() => {
          activeElement = element as HTMLElement;
          applyEffects(activeElement, true);
          config.onEnter?.(activeElement, event as MouseEvent);
        }, config.enterDelay);
      } else {
        activeElement = element as HTMLElement;
        applyEffects(activeElement, true);
        config.onEnter?.(activeElement, event as MouseEvent);
      }
    });
    
    element.addEventListener('mouseleave', (event) => {
      if (hoverTimeout) {
        clearTimeout(hoverTimeout);
        hoverTimeout = null;
      }
      
      if (activeElement) {
        const leaveElement = activeElement;
        
        if (config.leaveDelay > 0) {
          setTimeout(() => {
            applyEffects(leaveElement, false);
            config.onLeave?.(leaveElement, event as MouseEvent);
          }, config.leaveDelay);
        } else {
          applyEffects(leaveElement, false);
          config.onLeave?.(leaveElement, event as MouseEvent);
        }
        
        activeElement = null;
      }
    });
  });
}
```

### 8.3 Keyboard Shortcuts

```typescript
interface KeyboardShortcutConfig {
  shortcuts: KeyboardShortcut[];
  preventDefault: boolean;
  enabledWhen?: () => boolean;
}

interface KeyboardShortcut {
  key: string;
  modifiers?: {
    ctrl?: boolean;
    shift?: boolean;
    alt?: boolean;
    meta?: boolean;
  };
  action: string | (() => void);
  description: string;
  scope?: string;  // e.g., 'global', 'panel', 'callout'
}

const DEFAULT_HUD_SHORTCUTS: KeyboardShortcut[] = [
  {
    key: 'Escape',
    action: 'dismiss:all',
    description: 'Close all callouts and panels'
  },
  {
    key: 'Tab',
    action: 'focus:next',
    description: 'Focus next interactive element'
  },
  {
    key: 'Tab',
    modifiers: { shift: true },
    action: 'focus:previous',
    description: 'Focus previous interactive element'
  },
  {
    key: 'Space',
    action: 'activate',
    description: 'Activate focused element',
    scope: 'focused'
  },
  {
    key: '/',
    modifiers: { ctrl: true },
    action: 'toggle:overlay',
    description: 'Toggle HUD visibility'
  },
  {
    key: 'g',
    modifiers: { ctrl: true, shift: true },
    action: 'trigger:glitch',
    description: 'Trigger glitch effect'
  }
];
```

---

## 9. Audio Integration

### 9.1 Audio System Architecture

```typescript
interface AudioSystemConfig {
  masterVolume: number;
  categories: {
    ui: { volume: number; enabled: boolean };
    ambient: { volume: number; enabled: boolean };
    alerts: { volume: number; enabled: boolean };
    voice: { volume: number; enabled: boolean };
  };
  spatialAudio: boolean;
  crossfadeDuration: number;
}

interface SoundSprite {
  id: string;
  category: keyof AudioSystemConfig['categories'];
  url: string;
  sprites: {
    [key: string]: {
      start: number;
      duration: number;
      loop?: boolean;
    };
  };
}

const UI_SOUNDS: SoundSprite = {
  id: 'ui_main',
  category: 'ui',
  url: '/audio/ui-sounds.mp3',
  sprites: {
    // Click/tap sounds
    'click_soft': { start: 0, duration: 100 },
    'click_hard': { start: 150, duration: 120 },
    'click_toggle': { start: 300, duration: 80 },
    
    // Panel sounds
    'panel_open': { start: 500, duration: 300 },
    'panel_close': { start: 850, duration: 250 },
    'panel_slide': { start: 1150, duration: 200 },
    
    // Callout sounds
    'callout_spawn': { start: 1400, duration: 350 },
    'callout_dismiss': { start: 1800, duration: 200 },
    'callout_line_draw': { start: 2050, duration: 150 },
    
    // Hover sounds
    'hover_enter': { start: 2250, duration: 50 },
    'hover_leave': { start: 2350, duration: 50 },
    
    // Data sounds
    'data_tick': { start: 2450, duration: 30 },
    'data_stream': { start: 2500, duration: 500, loop: true },
    
    // Alert sounds
    'alert_info': { start: 3050, duration: 400 },
    'alert_warning': { start: 3500, duration: 600 },
    'alert_critical': { start: 4150, duration: 800 },
    
    // System sounds
    'boot_sequence': { start: 5000, duration: 2000 },
    'system_ready': { start: 7050, duration: 500 },
    'shutdown': { start: 7600, duration: 1500 }
  }
};
```

### 9.2 Audio Event Mapping

```typescript
// Automatic sound triggers based on events
const AUDIO_EVENT_MAP: Record<HUDEventType, AudioTrigger> = {
  'cursor:click': {
    sound: 'click_soft',
    conditions: (event) => !event.payload.target?.closest('[data-sound="hard"]')
  },
  'panel:open': {
    sound: 'panel_open',
    delay: 0
  },
  'panel:close': {
    sound: 'panel_close',
    delay: 0
  },
  'callout:spawn': {
    sound: 'callout_spawn',
    chain: [
      { sound: 'callout_line_draw', delay: 100 },
      { sound: 'callout_line_draw', delay: 200 }
    ]
  },
  'alert:trigger': {
    sound: (event) => `alert_${event.payload.level}`,
    priority: 'high'
  },
  'system:boot': {
    sound: 'boot_sequence',
    exclusive: true  // Stop other sounds
  }
};

// Audio engine integration
function setupAudioEventBindings(
  eventBus: HUDEventBus,
  audioEngine: AudioEngine,
  config: AudioSystemConfig
): void {
  Object.entries(AUDIO_EVENT_MAP).forEach(([eventType, trigger]) => {
    eventBus.on(eventType as HUDEventType, (event) => {
      if (!config.categories.ui.enabled) return;
      
      // Check conditions
      if (trigger.conditions && !trigger.conditions(event)) return;
      
      // Determine sound ID
      const soundId = typeof trigger.sound === 'function'
        ? trigger.sound(event)
        : trigger.sound;
      
      // Handle exclusive sounds
      if (trigger.exclusive) {
        audioEngine.stopAll();
      }
      
      // Play sound(s)
      audioEngine.play(soundId, { delay: trigger.delay ?? 0 });
      
      // Handle chain
      if (trigger.chain) {
        trigger.chain.forEach(chainItem => {
          audioEngine.play(chainItem.sound, { delay: chainItem.delay });
        });
      }
    });
  });
}
```

### 9.3 Ambient Sound Loops

```typescript
interface AmbientLayerConfig {
  id: string;
  sound: string;
  volume: number;
  fadeIn: number;
  fadeOut: number;
  conditions: {
    alertLevel?: AlertLevel[];
    systemState?: SystemState[];
    always?: boolean;
  };
}

const AMBIENT_LAYERS: AmbientLayerConfig[] = [
  {
    id: 'base_hum',
    sound: 'ambient_hum',
    volume: 0.3,
    fadeIn: 2000,
    fadeOut: 1000,
    conditions: { always: true }
  },
  {
    id: 'data_flow',
    sound: 'data_stream',
    volume: 0.15,
    fadeIn: 500,
    fadeOut: 500,
    conditions: { systemState: ['active'] }
  },
  {
    id: 'alert_tension',
    sound: 'tension_loop',
    volume: 0.4,
    fadeIn: 1000,
    fadeOut: 2000,
    conditions: { alertLevel: ['warning', 'critical'] }
  }
];
```

---

## 10. Performance Optimization

### 10.1 Rendering Optimization Strategies

```typescript
// 1. LAYER CULLING
// Only render layers that have visible content
interface LayerManager {
  visibleLayers: Set<HUDLayer>;
  
  setLayerVisibility(layer: HUDLayer, visible: boolean): void;
  isLayerVisible(layer: HUDLayer): boolean;
  getVisibleLayers(): HUDLayer[];
}

// 2. OBJECT POOLING
// Reuse objects instead of creating/destroying
class ObjectPool<T> {
  private available: T[] = [];
  private inUse: Set<T> = new Set();
  private factory: () => T;
  private reset: (obj: T) => void;
  
  constructor(factory: () => T, reset: (obj: T) => void, initialSize: number = 10) {
    this.factory = factory;
    this.reset = reset;
    
    for (let i = 0; i < initialSize; i++) {
      this.available.push(this.factory());
    }
  }
  
  acquire(): T {
    const obj = this.available.pop() ?? this.factory();
    this.inUse.add(obj);
    return obj;
  }
  
  release(obj: T): void {
    if (this.inUse.has(obj)) {
      this.inUse.delete(obj);
      this.reset(obj);
      this.available.push(obj);
    }
  }
}

// 3. RENDER BATCHING
// Batch DOM updates to minimize reflows
class RenderBatcher {
  private pendingUpdates: Map<string, () => void> = new Map();
  private frameRequested: boolean = false;
  
  schedule(key: string, update: () => void): void {
    this.pendingUpdates.set(key, update);
    
    if (!this.frameRequested) {
      this.frameRequested = true;
      requestAnimationFrame(() => this.flush());
    }
  }
  
  private flush(): void {
    this.pendingUpdates.forEach(update => update());
    this.pendingUpdates.clear();
    this.frameRequested = false;
  }
}

// 4. THROTTLE / DEBOUNCE
function throttle<T extends (...args: any[]) => void>(
  fn: T,
  limit: number
): T {
  let lastRun = 0;
  return ((...args) => {
    const now = Date.now();
    if (now - lastRun >= limit) {
      lastRun = now;
      fn(...args);
    }
  }) as T;
}

// 5. VISIBILITY OBSERVER
// Pause animations for off-screen elements
function setupVisibilityOptimization(element: HTMLElement): IntersectionObserver {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        const target = entry.target as HTMLElement;
        if (entry.isIntersecting) {
          target.style.animationPlayState = 'running';
        } else {
          target.style.animationPlayState = 'paused';
        }
      });
    },
    { threshold: 0 }
  );
  
  observer.observe(element);
  return observer;
}
```

### 10.2 Memory Management

```typescript
// Cleanup manager for preventing memory leaks
class HUDCleanupManager {
  private cleanupTasks: Map<string, () => void> = new Map();
  
  register(id: string, cleanup: () => void): void {
    this.cleanupTasks.set(id, cleanup);
  }
  
  unregister(id: string): void {
    const cleanup = this.cleanupTasks.get(id);
    if (cleanup) {
      cleanup();
      this.cleanupTasks.delete(id);
    }
  }
  
  cleanupAll(): void {
    this.cleanupTasks.forEach(cleanup => cleanup());
    this.cleanupTasks.clear();
  }
}

// Component cleanup checklist
interface ComponentCleanup {
  // Event listeners
  removeEventListeners: () => void;
  
  // Timers/intervals
  clearTimers: () => void;
  
  // Animation frames
  cancelAnimationFrames: () => void;
  
  // WebGL resources
  disposeGeometries: () => void;
  disposeMaterials: () => void;
  disposeTextures: () => void;
  
  // Audio
  stopSounds: () => void;
  
  // DOM
  removeElements: () => void;
}
```

### 10.3 Performance Budgets

```typescript
const PERFORMANCE_BUDGETS = {
  // Frame timing
  targetFPS: 60,
  maxFrameTime: 16.67,  // ms
  
  // Memory
  maxTextureMemory: 256,  // MB
  maxGeometryMemory: 64,  // MB
  
  // Draw calls
  maxDrawCalls: 100,
  maxTriangles: 500000,
  
  // DOM
  maxDOMElements: 500,
  maxDOMDepth: 15,
  
  // Animations
  maxConcurrentAnimations: 50,
  maxParticles: 10000
};

// Performance monitor
class PerformanceMonitor {
  private frameTimes: number[] = [];
  private lastFrameTime: number = 0;
  
  startFrame(): void {
    this.lastFrameTime = performance.now();
  }
  
  endFrame(): void {
    const frameTime = performance.now() - this.lastFrameTime;
    this.frameTimes.push(frameTime);
    
    if (this.frameTimes.length > 60) {
      this.frameTimes.shift();
    }
    
    // Warn if over budget
    if (frameTime > PERFORMANCE_BUDGETS.maxFrameTime) {
      console.warn(`Frame budget exceeded: ${frameTime.toFixed(2)}ms`);
    }
  }
  
  getAverageFPS(): number {
    const avgFrameTime = this.frameTimes.reduce((a, b) => a + b, 0) / this.frameTimes.length;
    return 1000 / avgFrameTime;
  }
  
  getMetrics(): PerformanceMetrics {
    return {
      fps: this.getAverageFPS(),
      frameTime: this.frameTimes[this.frameTimes.length - 1],
      memory: (performance as any).memory?.usedJSHeapSize / 1048576,
      domElements: document.getElementsByTagName('*').length
    };
  }
}
```

---

## 11. Implementation Patterns

### 11.1 Quick Start Template

```typescript
// main.tsx - Entry point
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { HUDProvider } from './providers/HUDProvider';
import { HUDRoot } from './components/HUDRoot';
import './styles/global.css';

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <HUDProvider>
      <HUDRoot />
    </HUDProvider>
  </StrictMode>
);
```

```typescript
// providers/HUDProvider.tsx
import { createContext, useContext, useRef, useEffect, ReactNode } from 'react';
import { create } from 'zustand';
import { HUDEventBus } from '../systems/EventBus';
import { AudioEngine } from '../systems/AudioEngine';
import type { HUDState } from '../types';

interface HUDContextValue {
  state: HUDState;
  eventBus: HUDEventBus;
  audioEngine: AudioEngine;
}

const HUDContext = createContext<HUDContextValue | null>(null);

export const useHUD = () => {
  const context = useContext(HUDContext);
  if (!context) throw new Error('useHUD must be used within HUDProvider');
  return context;
};

const useHUDStore = create<HUDState>((set) => ({
  systemPower: 'boot',
  alertLevel: 'normal',
  callouts: new Map(),
  activeCalloutId: null,
  panels: new Map(),
  panelFocusStack: [],
  globalAnimationSpeed: 1,
  reducedMotion: false,
  cursorPosition: { x: 0, y: 0 },
  cursorMode: 'default',
  dataFeeds: new Map()
}));

export function HUDProvider({ children }: { children: ReactNode }) {
  const eventBusRef = useRef(new HUDEventBus());
  const audioEngineRef = useRef(new AudioEngine());
  const state = useHUDStore();
  
  useEffect(() => {
    // Boot sequence
    eventBusRef.current.emit('system:boot', {});
    
    // Initialize audio
    audioEngineRef.current.initialize();
    
    // Mark ready after boot animation
    setTimeout(() => {
      useHUDStore.setState({ systemPower: 'active' });
      eventBusRef.current.emit('system:ready', {});
    }, 2000);
    
    return () => {
      audioEngineRef.current.dispose();
    };
  }, []);
  
  return (
    <HUDContext.Provider value={{
      state,
      eventBus: eventBusRef.current,
      audioEngine: audioEngineRef.current
    }}>
      {children}
    </HUDContext.Provider>
  );
}
```

```typescript
// components/HUDRoot.tsx
import { Canvas } from '@react-three/fiber';
import { EffectComposer, Bloom, ChromaticAberration } from '@react-three/postprocessing';
import { useHUD } from '../providers/HUDProvider';
import { HologramLayer } from './3d/HologramLayer';
import { Overlay2D } from './2d/Overlay2D';
import { BootSequence } from './sequences/BootSequence';
import { CalloutManager } from './callouts/CalloutManager';
import { CustomCursor } from './cursor/CustomCursor';
import { ScanLines } from './effects/ScanLines';

export function HUDRoot() {
  const { state } = useHUD();
  
  return (
    <div className="hud-root">
      {/* 3D Layer */}
      <Canvas
        camera={{ position: [0, 0, 100], fov: 50 }}
        className="hud-canvas"
      >
        <HologramLayer />
        
        <EffectComposer>
          <Bloom 
            intensity={1.5}
            luminanceThreshold={0.6}
            luminanceSmoothing={0.3}
          />
          <ChromaticAberration offset={[0.002, 0.002]} />
        </EffectComposer>
      </Canvas>
      
      {/* 2D Overlay */}
      <Overlay2D />
      
      {/* Callout System */}
      <CalloutManager />
      
      {/* Global Effects */}
      <ScanLines />
      
      {/* Custom Cursor */}
      <CustomCursor mode={state.cursorMode} />
      
      {/* Boot Sequence (conditional) */}
      {state.systemPower === 'boot' && <BootSequence />}
    </div>
  );
}
```

### 11.2 Common Implementation Recipes

#### Recipe 1: Interactive Callout on Click

```typescript
// hooks/useCalloutOnClick.ts
import { useCallback } from 'react';
import { useHUD } from '../providers/HUDProvider';

interface UseCalloutOnClickOptions {
  contentGenerator: (point: { x: number; y: number }) => React.ReactNode;
  maxCallouts?: number;
}

export function useCalloutOnClick(options: UseCalloutOnClickOptions) {
  const { eventBus, state } = useHUD();
  const { contentGenerator, maxCallouts = 3 } = options;
  
  const handleClick = useCallback((event: React.MouseEvent) => {
    const activeCount = [...state.callouts.values()]
      .filter(c => c.state === 'active').length;
    
    // Dismiss oldest if at max
    if (activeCount >= maxCallouts) {
      const oldest = [...state.callouts.entries()]
        .filter(([, c]) => c.state === 'active')
        .sort((a, b) => a[1].spawnTimestamp - b[1].spawnTimestamp)[0];
      
      if (oldest) {
        eventBus.emit('callout:dismiss', { id: oldest[0] });
      }
    }
    
    const point = { x: event.clientX, y: event.clientY };
    
    eventBus.emit('callout:spawn', {
      id: `callout-${Date.now()}`,
      originPoint: point,
      content: contentGenerator(point),
      direction: 'auto'
    });
  }, [eventBus, state.callouts, contentGenerator, maxCallouts]);
  
  return { onClick: handleClick };
}
```

#### Recipe 2: Animated Data Panel

```typescript
// components/panels/DataPanel.tsx
import { motion, AnimatePresence } from 'framer-motion';
import { useEffect, useState } from 'react';

interface DataPanelProps {
  title: string;
  dataSource: () => { label: string; value: string | number }[];
  updateInterval?: number;
}

export function DataPanel({ title, dataSource, updateInterval = 1000 }: DataPanelProps) {
  const [data, setData] = useState(dataSource());
  const [isVisible, setIsVisible] = useState(false);
  
  useEffect(() => {
    setIsVisible(true);
    
    const interval = setInterval(() => {
      setData(dataSource());
    }, updateInterval);
    
    return () => clearInterval(interval);
  }, [dataSource, updateInterval]);
  
  return (
    <AnimatePresence>
      {isVisible && (
        <motion.div
          className="data-panel"
          initial={{ opacity: 0, scale: 0.9, y: 20 }}
          animate={{ opacity: 1, scale: 1, y: 0 }}
          exit={{ opacity: 0, scale: 0.95, y: -10 }}
          transition={{ duration: 0.3, ease: [0.4, 0, 0.2, 1] }}
        >
          {/* Header */}
          <motion.div 
            className="data-panel__header"
            initial={{ width: 0 }}
            animate={{ width: '100%' }}
            transition={{ duration: 0.5, delay: 0.1 }}
          >
            <span className="data-panel__title">{title}</span>
            <span className="data-panel__status-dot" />
          </motion.div>
          
          {/* Data rows */}
          <div className="data-panel__content">
            {data.map((item, index) => (
              <motion.div
                key={item.label}
                className="data-panel__row"
                initial={{ opacity: 0, x: -10 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ 
                  duration: 0.2, 
                  delay: 0.2 + index * 0.05 
                }}
              >
                <span className="data-panel__label">{item.label}</span>
                <motion.span 
                  className="data-panel__value"
                  key={`${item.label}-${item.value}`}
                  initial={{ opacity: 0.5 }}
                  animate={{ opacity: 1 }}
                  transition={{ duration: 0.15 }}
                >
                  {item.value}
                </motion.span>
              </motion.div>
            ))}
          </div>
          
          {/* Corner brackets */}
          <div className="data-panel__bracket data-panel__bracket--top-left" />
          <div className="data-panel__bracket data-panel__bracket--top-right" />
          <div className="data-panel__bracket data-panel__bracket--bottom-left" />
          <div className="data-panel__bracket data-panel__bracket--bottom-right" />
        </motion.div>
      )}
    </AnimatePresence>
  );
}
```

#### Recipe 3: Boot Sequence Animation

```typescript
// components/sequences/BootSequence.tsx
import { motion, useAnimation } from 'framer-motion';
import { useEffect, useState } from 'react';
import { useHUD } from '../../providers/HUDProvider';

const BOOT_STAGES = [
  { text: 'INITIALIZING SYSTEM...', duration: 400 },
  { text: 'LOADING CORE MODULES', duration: 300 },
  { text: 'CALIBRATING SENSORS', duration: 350 },
  { text: 'ESTABLISHING CONNECTIONS', duration: 400 },
  { text: 'VERIFYING INTEGRITY', duration: 250 },
  { text: 'SYSTEM READY', duration: 300 }
];

export function BootSequence() {
  const { audioEngine, eventBus } = useHUD();
  const [currentStage, setCurrentStage] = useState(0);
  const [displayText, setDisplayText] = useState('');
  const controls = useAnimation();
  
  useEffect(() => {
    audioEngine.play('boot_sequence');
    
    const runBootSequence = async () => {
      for (let i = 0; i < BOOT_STAGES.length; i++) {
        setCurrentStage(i);
        
        // Typewriter effect
        const text = BOOT_STAGES[i].text;
        for (let j = 0; j <= text.length; j++) {
          setDisplayText(text.slice(0, j));
          await new Promise(r => setTimeout(r, 20));
        }
        
        await new Promise(r => setTimeout(r, BOOT_STAGES[i].duration));
      }
      
      // Final flash and dismiss
      await controls.start({
        opacity: [1, 0, 1, 0],
        transition: { duration: 0.3, times: [0, 0.3, 0.6, 1] }
      });
    };
    
    runBootSequence();
  }, [audioEngine, controls]);
  
  return (
    <motion.div
      className="boot-sequence"
      animate={controls}
      initial={{ opacity: 1 }}
    >
      <div className="boot-sequence__container">
        {/* Progress bar */}
        <div className="boot-sequence__progress">
          <motion.div
            className="boot-sequence__progress-fill"
            initial={{ width: '0%' }}
            animate={{ width: `${((currentStage + 1) / BOOT_STAGES.length) * 100}%` }}
            transition={{ duration: 0.3 }}
          />
        </div>
        
        {/* Status text */}
        <div className="boot-sequence__text">
          <span className="boot-sequence__prefix">&gt; </span>
          <span className="boot-sequence__content">{displayText}</span>
          <motion.span
            className="boot-sequence__cursor"
            animate={{ opacity: [1, 0] }}
            transition={{ duration: 0.5, repeat: Infinity }}
          >
            _
          </motion.span>
        </div>
        
        {/* Decorative elements */}
        <div className="boot-sequence__grid" />
        <div className="boot-sequence__scan-line" />
      </div>
    </motion.div>
  );
}
```

### 11.3 CSS Foundation

```css
/* styles/global.css */

:root {
  /* Color Palette */
  --hud-primary: #00FFFF;
  --hud-secondary: #0088AA;
  --hud-accent: #FF00FF;
  --hud-warning: #FFAA00;
  --hud-danger: #FF0040;
  --hud-success: #00FF88;
  
  --hud-bg-dark: rgba(0, 10, 20, 0.95);
  --hud-bg-panel: rgba(0, 20, 40, 0.8);
  --hud-border: rgba(0, 255, 255, 0.3);
  --hud-text: rgba(200, 255, 255, 0.9);
  --hud-text-dim: rgba(150, 200, 200, 0.6);
  
  /* Typography */
  --font-display: 'Orbitron', 'Rajdhani', sans-serif;
  --font-mono: 'JetBrains Mono', 'Fira Code', monospace;
  --font-ui: 'Exo 2', 'Inter', sans-serif;
  
  /* Spacing */
  --space-xs: 4px;
  --space-sm: 8px;
  --space-md: 16px;
  --space-lg: 24px;
  --space-xl: 32px;
  
  /* Animation */
  --transition-fast: 150ms;
  --transition-normal: 300ms;
  --transition-slow: 500ms;
  --ease-sharp: cubic-bezier(0.4, 0, 0.2, 1);
  --ease-bounce: cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Reset & Base */
*, *::before, *::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html, body, #root {
  width: 100%;
  height: 100%;
  overflow: hidden;
  background: var(--hud-bg-dark);
  color: var(--hud-text);
  font-family: var(--font-ui);
}

/* HUD Root Container */
.hud-root {
  position: relative;
  width: 100%;
  height: 100%;
}

.hud-canvas {
  position: absolute;
  inset: 0;
  z-index: 1;
}

/* Panel Base Styles */
.hud-panel {
  position: absolute;
  background: var(--hud-bg-panel);
  border: 1px solid var(--hud-border);
  border-radius: 4px;
  backdrop-filter: blur(10px);
  box-shadow:
    0 0 20px rgba(0, 255, 255, 0.1),
    inset 0 0 30px rgba(0, 255, 255, 0.03);
}

/* Bracket Decoration */
.hud-brackets {
  position: absolute;
  inset: -2px;
  pointer-events: none;
}

.hud-brackets::before,
.hud-brackets::after {
  content: '';
  position: absolute;
  width: 20px;
  height: 20px;
  border-color: var(--hud-primary);
  border-style: solid;
}

.hud-brackets::before {
  top: 0;
  left: 0;
  border-width: 2px 0 0 2px;
}

.hud-brackets::after {
  bottom: 0;
  right: 0;
  border-width: 0 2px 2px 0;
}

/* Glow Effects */
.hud-glow {
  filter: drop-shadow(0 0 10px var(--hud-primary));
}

.hud-glow-intense {
  filter: 
    drop-shadow(0 0 5px var(--hud-primary))
    drop-shadow(0 0 15px var(--hud-primary))
    drop-shadow(0 0 30px var(--hud-secondary));
}

/* Animations */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

@keyframes rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes scanLine {
  from { transform: translateY(-100%); }
  to { transform: translateY(100vh); }
}

@keyframes dataFlicker {
  0%, 100% { opacity: 1; }
  92% { opacity: 1; }
  93% { opacity: 0.8; }
  94% { opacity: 1; }
  95% { opacity: 0.9; }
  96% { opacity: 1; }
}

/* Utility Classes */
.animate-pulse { animation: pulse 2s ease-in-out infinite; }
.animate-rotate { animation: rotate 10s linear infinite; }
.animate-flicker { animation: dataFlicker 0.15s steps(5) infinite; }

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
```

---

## Appendix A: Color Palettes

### Cyan/Blue (Classic Sci-Fi)
```
Primary:    #00FFFF (Cyan)
Secondary:  #0088AA
Accent:     #00FF88
Warning:    #FFAA00
Danger:     #FF0040
Background: #001428
```

### Orange/Amber (Military/Tactical)
```
Primary:    #FF8800
Secondary:  #AA5500
Accent:     #FFCC00
Warning:    #FF4400
Danger:     #FF0000
Background: #1A0A00
```

### Green/Matrix (Hacker/Terminal)
```
Primary:    #00FF00
Secondary:  #00AA00
Accent:     #88FF88
Warning:    #FFFF00
Danger:     #FF0000
Background: #000A00
```

### Purple/Pink (Cyberpunk)
```
Primary:    #FF00FF
Secondary:  #AA00AA
Accent:     #00FFFF
Warning:    #FFAA00
Danger:     #FF0040
Background: #0A001A
```

---

## Appendix B: Font Recommendations

| Font | Style | Use Case | License |
|------|-------|----------|---------|
| Orbitron | Display | Headers, titles | OFL |
| Rajdhani | Display | Compact headers | OFL |
| Exo 2 | UI | Body text, labels | OFL |
| JetBrains Mono | Monospace | Data, code | OFL |
| Share Tech Mono | Monospace | Terminal style | OFL |
| Aldrich | Display | Military aesthetic | OFL |

---

## Appendix C: Sound Design Guidelines

### UI Sound Characteristics
- **Frequency Range:** 1kHz - 8kHz (crisp, present)
- **Duration:** 50ms - 300ms (snappy, non-intrusive)
- **Attack:** Fast (< 10ms)
- **Release:** Medium (50-150ms)

### Recommended Sound Types
| Action | Sound Character |
|--------|----------------|
| Click | Short blip, slight resonance |
| Hover | Soft sweep up, very quiet |
| Panel Open | Rising sweep + impact |
| Panel Close | Falling sweep + soft thud |
| Alert | Distinctive tone + urgency cues |
| Data Tick | Soft click, randomize pitch slightly |
| Boot | Rising synth pad with digital artifacts |

---

## Document Revision History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-01 | Initial specification |

---

*End of Technical Specification*
