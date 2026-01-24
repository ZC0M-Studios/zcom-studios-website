# API Reference

## Overview
All API endpoints return JSON responses with a consistent structure:

```json
{
  "success": true|false,
  "data": {...},
  "error": "Error message if success is false"
}
```

## Endpoints

### 1. Get All Blog Posts
**Endpoint**: `/api/get_posts.php`  
**Method**: GET  
**Parameters**: None

**Response**:
```json
{
  "success": true,
  "posts": [
    {
      "id": 1,
      "title": "Article Title",
      "slug": "article-slug",
      "excerpt": "Brief description...",
      "content": "Full article content...",
      "thumbnail": "/path/to/image.jpg",
      "category": "Tutorial",
      "author": "Author Name",
      "published_date": "2024-01-15",
      "published_date_formatted": "Jan 15, 2024",
      "reading_time": 5,
      "views": 150,
      "featured": true,
      "tags_array": ["JavaScript", "Tutorial"],
      "post_url": "/blog/post.php?slug=article-slug"
    }
  ]
}
```

---

### 2. Get Single Blog Post
**Endpoint**: `/api/get_post.php`  
**Method**: GET  
**Parameters**: 
- `slug` (required) - The post slug

**Response**:
```json
{
  "success": true,
  "post": {
    "id": 1,
    "title": "Article Title",
    "slug": "article-slug",
    "content": "Full article content...",
    "tags": "JavaScript, Tutorial, Web Development"
  }
}
```

---

### 3. Get All Projects
**Endpoint**: `/api/get_projects.php`  
**Method**: GET  
**Parameters**: None

**Response**:
```json
{
  "success": true,
  "projects": [
    {
      "id": 1,
      "name": "Project Name",
      "slug": "project-slug",
      "tagline": "Brief tagline",
      "description": "Short description",
      "long_description": "Detailed description",
      "type": "web_app",
      "thumbnail": "/path/to/image.jpg",
      "live_url": "https://example.com",
      "github_url": "https://github.com/user/repo",
      "status": "live",
      "featured": true,
      "date_completed": "2024-01-15",
      "date_completed_formatted": "Jan 15, 2024",
      "date_display": "Q1 2024",
      "role": "Full Stack Developer",
      "tags_array": ["React", "Node.js"],
      "tech_stack_by_category": {
        "Frontend": ["React", "TypeScript"],
        "Backend": ["Node.js", "Express"]
      },
      "features_array": ["Feature 1", "Feature 2"],
      "project_url": "/portfolio/project.php?slug=project-slug"
    }
  ]
}
```

---

### 4. Get All Prompts
**Endpoint**: `/api/get_prompts.php`  
**Method**: GET  
**Parameters**: None

**Response**:
```json
{
  "success": true,
  "prompts": [
    {
      "id": 1,
      "title": "Prompt Title",
      "slug": "prompt-slug",
      "description": "Brief description",
      "prompt_text": "The actual prompt text...",
      "category": "code-generation",
      "difficulty": "intermediate",
      "ai_model": "GPT-4",
      "success_rating": 5,
      "iterations_needed": 2,
      "estimated_tokens": 500,
      "output_type": "code",
      "code_language": "JavaScript",
      "problem_solved": "Description of problem",
      "modifications_required": "Any modifications needed",
      "usage_tips": "Tips for using this prompt",
      "views": 100,
      "times_copied": 50,
      "tags_by_category": {
        "technology": ["JavaScript", "React"],
        "use-case": ["debugging", "optimization"]
      },
      "prompt_url": "/prompts/prompt.php?slug=prompt-slug"
    }
  ]
}
```

---

### 5. Get Tag Content
**Endpoint**: `/api/get_tag_content.php`  
**Method**: GET  
**Parameters**: 
- `tag` (required) - The tag name

**Response**:
```json
{
  "success": true,
  "tag": "JavaScript",
  "count": 15,
  "content": [
    {
      "type": "blog",
      "id": 1,
      "title": "Article Title",
      "slug": "article-slug",
      "excerpt": "Brief description",
      "thumbnail": "/path/to/image.jpg",
      "category": "Tutorial",
      "date": "Jan 15, 2024"
    },
    {
      "type": "project",
      "id": 2,
      "title": "Project Name",
      "slug": "project-slug",
      "description": "Project description",
      "thumbnail": "/path/to/image.jpg",
      "project_type": "web_app",
      "date": "Jan 10, 2024"
    },
    {
      "type": "prompt",
      "id": 3,
      "title": "Prompt Title",
      "slug": "prompt-slug",
      "description": "Prompt description",
      "category": "code-generation",
      "date": "Jan 05, 2024"
    }
  ]
}
```

---

### 6. Increment Prompt Copy Count
**Endpoint**: `/api/increment_prompt_copy.php`  
**Method**: GET  
**Parameters**: 
- `id` (required) - The prompt ID

**Response**:
```json
{
  "success": true,
  "message": "Copy count incremented"
}
```

---

## Error Responses

All endpoints return error responses in this format:

```json
{
  "success": false,
  "error": "Error message describing what went wrong"
}
```

Common error scenarios:
- Missing required parameters
- Invalid slug/ID
- Database connection errors
- Content not found
- Unauthorized access (for future auth implementation)

---

## Usage Examples

### JavaScript Fetch
```javascript
// Get all blog posts
fetch('/api/get_posts.php')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log(data.posts);
    }
  });

// Get single post
fetch('/api/get_post.php?slug=my-article')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log(data.post);
    }
  });

// Get tag content
fetch('/api/get_tag_content.php?tag=JavaScript')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log(data.content);
    }
  });
```

---

## Rate Limiting
Currently no rate limiting is implemented. For production deployment, consider implementing:
- Request throttling per IP
- API key authentication
- CORS restrictions
- Request logging

---

## Future API Endpoints (Planned)
- `/api/search.php` - Global search across all content
- `/api/auth/login.php` - User authentication
- `/api/auth/register.php` - User registration
- `/api/comments/add.php` - Add comment to content
- `/api/comments/get.php` - Get comments for content
- `/api/admin/*` - Admin panel endpoints

