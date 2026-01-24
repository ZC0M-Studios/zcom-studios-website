<?php 
/* ====================================
    Global utilities php functions in a single file
====================================*/



/* ========================================================
    //ANCHOR [GENERATE_UNIQUE_ID]
    FUNCTION: generateUniqueID
-----------------------------------------------------------
    Parameters: N/A
    Returns: int
    Description: This function generates and returns a uniqueID (6-digit integer). It keeps track of all generated IDs in a static array to avoid duplicates within a single request.
    UniqueID: 123459
=========================================================== */
function generateUniqueID() {
    // Must keep record of all generated IDs so no duplicates are assigned
    static $a_uniqueIDs = []; // Static variable to keep track of all uniqueIDs generated
    // Generate a random number between 100000 and 999999 (inclusive)
    $uniqueID = rand(100000, 999999);

    // Check if the uniqueID already exists in the array of uniqueIDs
    if (!in_array($uniqueID, $a_uniqueIDs)) {
        // Add to the array of uniqueIDs (if it doesn't already exist)
        array_push($a_uniqueIDs, $uniqueID);
    } else {
        // If the uniqueID already exists, generate a new one
        generateUniqueID();
    };

    return $uniqueID;
}
?>