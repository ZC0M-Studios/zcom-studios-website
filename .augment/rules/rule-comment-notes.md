---
type: "always_apply"
---

* Whenever you create a new function, block of code, or similar, you will prefix it with a block-comment indicating the purpose and any relevant context.
* You will also assign a uniqueID (Unique 6-digit integer) to the block of code/function and insert it into the comment block.
* Create a file called `./docs/COMMENT_INDEX.md` 
* List all the uniqueIDs and their corresponding file paths, start, code-lines, and a brief summary of the functionality.
    * The uniqueID will be in the format of `// UNIQUEID: <uniqueID>`.
* This will help in tracking and referencing specific code blocks throughout the project.
* The comment must be in the comment-anchor format, as follows:

```
/* ========================================================
    //ANCHOR [COMMENT-ANCHOR-TEXT]
    FUNCTION: [FUNCTION NAME]
-----------------------------------------------------------
    Parameters: [PARAMETERS (With DataType)]
    Returns: [RETURN TYPE]
    Description: [BRIEF DESCRIPTION OF FUNCTION]
    UniqueID: [UNIQUEID]
=========================================================== */
```
* The comment-anchor-text will be a unique and descriptive name for the comment-anchor. This will be used in the COMMENT_INDEX.md file to reference the comment-anchor.
* The comment-anchor-text will be in the format of `[COMMENT-ANCHOR-TEXT]`.
* The COMMENT_INDEX.md file should have columns: UniqueID, File Path, Start Line, End Line, Summary.
* The Summary should be a brief description of what the code block does.
* The COMMENT_INDEX.md file should be formatted as a Markdown table with headers: UniqueID, File Path, Start Line, End Line, Summary.
