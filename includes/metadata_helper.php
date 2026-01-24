<?php
/* ========================================================
    //ANCHOR [METADATA_HELPER]
    FUNCTION: Metadata Helper Functions
-----------------------------------------------------------
    Parameters: Various per function
    Returns: Metadata arrays/objects
    Description: Helper functions for loading and working with extended metadata files
    UniqueID: 793600
=========================================================== */

/**
 * Load metadata from JSON file
 * @param string $type - Type of metadata (tags, projects, prompts, tools, articles, utility)
 * @return array|null - Parsed metadata or null on error
 */
function loadMetadata(string $type): ?array {
    $metadata_path = __DIR__ . '/../data/metadata/';
    
    $files = [
        'tags' => 'tags.metadata.json',
        'projects' => 'project.metadata.json',
        'prompts' => 'prompts.metadata.json',
        'tools' => 'tools.metadata.json',
        'articles' => 'article.metadata.json',
        'utility' => 'utility.metadata.json'
    ];
    
    if (!isset($files[$type])) {
        error_log("Unknown metadata type: $type");
        return null;
    }
    
    $file = $metadata_path . $files[$type];
    
    if (!file_exists($file)) {
        error_log("Metadata file not found: $file");
        return null;
    }
    
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON parse error in $file: " . json_last_error_msg());
        return null;
    }
    
    return $data;
}

/**
 * Get tag presentation metadata (color, icon)
 * @param string $slug - Tag slug
 * @param string $category - Tag category
 * @return array - Presentation data with color and icon
 */
function getTagPresentation(string $slug, string $category = 'custom'): array {
    static $metadata = null;
    
    if ($metadata === null) {
        $metadata = loadMetadata('tags');
    }
    
    $defaults = $metadata['defaults'] ?? ['color' => '#808080', 'icon' => 'bi-tag'];
    
    // Check for tag-specific override
    if (isset($metadata['tag_overrides'][$slug])) {
        return array_merge($defaults, $metadata['tag_overrides'][$slug]);
    }
    
    // Fall back to category defaults
    if (isset($metadata['categories'][$category])) {
        return [
            'color' => $metadata['categories'][$category]['color'] ?? $defaults['color'],
            'icon' => $metadata['categories'][$category]['icon'] ?? $defaults['icon']
        ];
    }
    
    return $defaults;
}

/**
 * Get project status presentation
 * @param string $status - Project status key
 * @return array - Status display data
 */
function getProjectStatusDisplay(string $status): array {
    static $metadata = null;
    
    if ($metadata === null) {
        $metadata = loadMetadata('projects');
    }
    
    return $metadata['status_display'][$status] ?? [
        'label' => ucfirst($status),
        'color' => '#808080',
        'icon' => 'bi-question-circle'
    ];
}

/**
 * Get project type presentation
 * @param string $type - Project type key
 * @return array - Type display data
 */
function getProjectTypeDisplay(string $type): array {
    static $metadata = null;
    
    if ($metadata === null) {
        $metadata = loadMetadata('projects');
    }
    
    return $metadata['project_types'][$type] ?? [
        'display_name' => ucfirst($type),
        'icon' => 'bi-folder',
        'color' => '#808080'
    ];
}

/**
 * Get prompt category presentation
 * @param string $category - Prompt category key
 * @return array - Category display data
 */
function getPromptCategoryDisplay(string $category): array {
    static $metadata = null;
    
    if ($metadata === null) {
        $metadata = loadMetadata('prompts');
    }
    
    return $metadata['categories'][$category] ?? [
        'display_name' => ucfirst($category),
        'icon' => 'bi-chat-left-text',
        'color' => '#808080'
    ];
}

/**
 * Get tool category presentation
 * @param string $category - Tool category key
 * @return array - Category display data
 */
function getToolCategoryDisplay(string $category): array {
    static $metadata = null;
    
    if ($metadata === null) {
        $metadata = loadMetadata('tools');
    }
    
    return $metadata['categories'][$category] ?? [
        'display_name' => ucfirst($category),
        'icon' => 'bi-tools',
        'color' => '#808080'
    ];
}

/**
 * Get all tag categories with metadata
 * @return array - All tag categories
 */
function getTagCategories(): array {
    $metadata = loadMetadata('tags');
    return $metadata['categories'] ?? [];
}

