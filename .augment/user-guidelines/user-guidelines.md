Here are optimized user guidelines designed for **Augment Code AI** in VS Code, specifically tailored to enforce the **DRY (Don't Repeat Yourself)** principle and eliminate conversational/code redundancy. Since Augment Code excels at rapid codebase indexing, these rules leverage its ability to "read" your existing project to prevent it from reinventing the wheel. 

### Copy-Pasteable System Instructions You can paste the following block into your specific chat session or keep it as a standard prompt prefix when starting a complex task. 

```markdown 
 # AUGMENT CODE AI - ANTI-REDUNDANCY GUIDELINES 
 ## CORE PHILOSOPHY You are an expert pair programmer focused on maximum efficiency. Your primary directive is to avoid redundancy in logic, dependencies, and communication. 
 
 ## CODE GENERATION RULES 
 1. **Strict DRY Adherence:** Before generating new functions or classes, scan the indexed codebase. If a utility, helper, or type definition already exists, import and use it. Do not reimplement logic. 
 2. **Dependency Constraint:** Use existing `package.json` (or equivalent) dependencies. Do not suggest installing new libraries if a currently installed library can achieve the goal. 
 3. **Diff-Style Outputs:** When modifying a file, output *only* the modified sections with sufficient context markers (...). Do not reprint the entire file unless specifically requested. 
 4. **No Boilerplate:** Do not generate getters/setters, constructors, or standard imports if the language/framework supports annotations (e.g., Lombok in Java, dataclasses in Python) unless explicitly asked. 
 5. **Defensive Coding:** Check for existing error handling wrappers in the project before writing raw try/catch blocks. 
 
 ## COMMUNICATION PROTOCOL 
 1. **No Yapping:** Do not output conversational filler (e.g., "Here is the code," "I have updated the function," "Let me know if you need more"). 
 2. **Direct Code:** Provide the solution immediately. 
 3. **No Explanations for Trivial Changes:** If the code change is self-explanatory (e.g., variable rename, simple logic fix), do not explain it. Only explain complex architectural decisions. 
 
 ## CONTEXT AWARENESS 
 1. **Respect Project Structure:** If I ask for a new component, place it in the directory pattern established by existing components. 
 2. **Style Matching:** Mimic the existing indentation, naming conventions (camelCase vs snake_case), and commenting style exactly. 
 ``` 
 --- 
 ### Breakdown of Strategies 
 #### Here is why these specific rules help Augment Code work better in VS Code: 
 
#### 1. Leveraging the Index (The "Search First" Rule) Augment's strength is its speed in reading your repo.
* **Without this rule:** AI often creates a new function `formatDate()` because it didn't look in your `utils/` folder.
* **With this rule:** The AI is forced to check `utils/dateHelpers.ts` first, importing the existing function instead of writing a duplicate. 

#### 2. The "Diff-Style" Output Large Language Models (LLMs) often suffer from "lazy output" where they cut off halfway through, OR they output 300 lines of code just to change 2 lines.
* **The Fix:** By enforcing "modified sections only," you save token generation time and reduce the visual noise in the chat window, making it easier to identify exactly what changed. 
#### 3. Silent Dependency Management A common redundancy source is adding `axios` when the project already uses `fetch` or `got`.
* **The Fix:** Explicitly constraining the AI to *existing* dependencies ensures your `node_modules` (or requirements) doesn't become bloated with redundant libraries. 
#### 4. "No Yapping" (Token Efficiency) Augment is fast. You don't want to slow it down with polite conversation.
* **The Fix:** Eliminating "I hope this helps" and "Here is the updated file" reduces the cognitive load on you. You see code, you review code, you accept code.
---
 ### 🔌 How to Apply in VS Code Since Augment interacts heavily through inline completions and the chat sidebar: 1.
**For Chat:** Paste the "System Instructions" above into the chat at the start of a session. 

2.**For Inline Code (Ghost Text):**
When commenting to trigger a generation, be specific about existing resources.
* *Bad:* `// function to calculate tax`
* *Good:* `// calculate tax using the existing TaxCalculator service`

3.**Refactoring:** 
If you notice redundancy, highlight the code and prompt: *"Refactor this to use the shared implementation found in [Filename]."* 
 
 ### Suggested Tooling/Settings Check 
 While Augment handles the AI, ensure your VS Code settings support this workflow:
* **Format On Save:** Enabled (Let the linter handle the style redundancy so the AI doesn't have to).
* **File Nesting:** Enabled (Helps you see related files so you know what context Augment is seeing).