---
trigger: always_on
---

* You will create a file called "./docs/CODE_MAP.md" that will function as your personal code-map of the entire codebase and all the logic within it. 
* This file will serve as a comprehensive map of the codebase structure and key functionalities.
* Before adding new code or making changes to existing code, you will check this file to see if the logic you want to implement already exists.
* If it does, you will use the existing code instead of duplicating it.
* If it does not, you will add a new entry to this file for the new code you are about to write.
* This file should be updated whenever you add new functionality or modify existing functionality.
* The file should be organized by feature areas and include brief descriptions of what each section does.
* The file should be kept up-to-date with any changes made to the codebase.
* You will use this file as a reference when planning new features or refactoring existing code.
* When adding entries, include the file path, start, code-lines, and a brief summary of the functionality.

Example format:
- src/components/ThreeScene.tsx:1-25: Camera controls and scene initialization logic
- src/utils/mathHelpers.ts:10-30: Vector math utility functions
- src/hooks/useAnimation.ts:5-40: Animation loop and frame management
- src/types/threejs.d.ts:1-15: Three.js type definitions and extensions
- src/services/apiClient.ts:1-35: HTTP client and API service implementations
- src/context/ThemeContext.tsx:1-20: Theme provider and theme management
- src/components/ModelViewer.tsx:1-45: 3D model rendering and interaction components
- src/pages/index.tsx:1-60: Main page layout and component composition
- src/components/Header.tsx:1-30: Navigation header and branding
- src/components/Footer.tsx:1-25: Page footer and copyright information
- src/styles/global.css:1-20: Global CSS variables and base styles
- src/hooks/useWindowSize.ts:1-15: Window size detection and responsive utilities
- src/utils/threeHelpers.ts:1-25: Three.js scene helper functions
- src/components/ModelLoader.tsx:1-35: 3D model loading and resource management
- src/hooks/useScrollPosition.ts:1-20: Scroll position tracking and animation triggers
- src/components/CanvasWrapper.tsx:1-30: Three.js canvas container and resize handling
- src/utils/animationHelpers.ts:1-25: Animation timing and easing functions
- src/components/ScrollIndicator.tsx:1-20: Scroll progress indicator component
- src/hooks/useIntersectionObserver.ts:1-25: Element intersection detection for scroll triggers
- src/components/ScrollTrigger.tsx:1-30: Scroll-based animation trigger component

* This file serves as a comprehensive map of the codebase structure and key functionalities.
* It helps prevent code duplication and ensures consistent implementation across the project.
* Regular updates to this file will help maintain code quality and reduce technical debt.
* This practice will improve code maintainability and accelerate future development cycles.
* By documenting all code paths and functionalities, you'll have a clear overview of the project's architecture and be able to quickly identify opportunities for refactoring and optimization.
* This documentation will serve as a valuable resource for onboarding new team members and understanding the codebase quickly.
* The code-map will also help in identifying deprecated or unused code paths for cleanup.
* This comprehensive documentation will serve as a single source of truth for the entire codebase, making it easier to navigate and understand the project structure.
