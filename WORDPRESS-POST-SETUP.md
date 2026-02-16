# WordPress Post Setup Guide - Claude AI Basics Blog Post

**Quick Setup:** Follow these steps to manually create the blog post in WordPress.

---

## Step 1: Create New Post

1. Go to **WordPress Admin** → **Posts** → **Add New**
2. Or visit: `http://localhost:[YOUR-PORT]/wp-admin/post-new.php?post_type=post`

---

## Step 2: Post Title

**Copy this exact title:**

```
Claude AI Basics: Context, Memory, Skills, and Tools Explained
```

---

## Step 3: Post Content

**Copy ALL content from:** `/blog-posts/claude-ai-basics.md`

Paste into WordPress editor (use Visual or Block Editor, doesn't matter).

---

## Step 4: Post Settings

### Slug/Permalink
```
claude-ai-basics-context-memory-skills-tools
```
*(Set in post settings → Permalink)*

### Categories
Select (or create) these categories:
- [ ] AI
- [ ] Technical
- [ ] Consulting

### Tags
Add these tags:
- [ ] claude-ai
- [ ] prompt-engineering
- [ ] ai-consulting
- [ ] developer-guide

### Excerpt
```
Deep-dive guide to Claude's core capabilities. Learn how context windows, persistent memory, skills, and tools work together to build production AI systems.
```

### Featured Image
1. Click **Set featured image**
2. Upload: `/app/public/wp-content/uploads/claude-ai-basics-featured.jpg`

**Image Metadata:**
- **Title:** 3D Neural Network Architecture - Claude AI Fundamentals
- **Alt Text:** Abstract 3D visualization of interconnected geometric shapes with cyan and blue glowing nodes on dark background, representing Claude AI system components and their relationships
- **Caption:** Visual representation of interconnected systems: Context, Memory, Skills, and Tools working together
- **Description:** 3D geometric visualization showing how Claude's core components—Context, Memory, Skills, and Tools—connect and interact as an integrated system. The cyan glow represents the flow of information through these interconnected architectural layers.

---

## Step 5: SEO Metadata

*(If using Yoast SEO or similar plugin)*

### SEO Title
```
Claude AI Basics: Context, Memory, Skills, and Tools Explained
```

### Meta Description (160 characters)
```
Deep-dive technical guide to Claude's core capabilities for API-familiar developers. Learn context windows, memory systems, skills, and tools with real examples.
```

### Focus Keyword
```
claude ai basics
```

### Additional Keywords
- prompt engineering
- claude context window
- claude memory
- ai consulting
- ai development

---

## Step 6: Save as Draft

1. Click **Save Draft** (top right)
2. **DO NOT PUBLISH YET** - wait for review

---

## Step 7: Verify

Once saved, check:
- [ ] Title is correct
- [ ] Content displays properly (check formatting, code blocks)
- [ ] Featured image shows at top
- [ ] Categories assigned
- [ ] Tags added
- [ ] SEO metadata filled
- [ ] Excerpt visible
- [ ] Post is in DRAFT status

---

## Step 8: Publish

When ready to go live:
1. Click **Publish** instead of **Save Draft**
2. Post goes live at the URL

---

## Markdown Template Examples in Post

The post includes these markdown templates with inline comments:

✅ **Context Section:**
- System prompt template with 8 comments

✅ **Memory Section:**
- Main memory structure template with comments
- Topic file template with comments

✅ **Skills Section:**
- Basic skill definition with examples
- Advanced skill with parameters

✅ **Tools Section:**
- File operation templates (Read/Write)
- Code search templates (Glob/Grep)
- Bash command template with security notes

---

## Estimated Read Time

The post is **3,087 words** and should display as **13 min read time** in WordPress.

---

## File Reference

- **Blog content:** `/blog-posts/claude-ai-basics.md`
- **Featured image:** `/app/public/wp-content/uploads/claude-ai-basics-featured.jpg`
- **GitHub issue:** #13
- **Branch:** `feature/claude-basics-blog`

---

## After Publishing

Once the post is live:

1. **Commit changes:**
   ```bash
   git add blog-posts/claude-ai-basics.md
   git commit -m "Publish Claude AI Basics blog post

   - Live at /blog/claude-ai-basics-context-memory-skills-tools
   - Featured image: 3D neural network visualization
   - 3,087 words, 13 min read time
   - SEO optimized for 'claude ai basics' keywords

   Closes #13"
   ```

2. **Push to remote:**
   ```bash
   git push origin feature/claude-basics-blog
   ```

3. **Create PR:**
   - Title: "Publish: Claude AI Basics Blog Post"
   - Reference: Closes #13
   - Body: Link to live post

4. **Merge to main**

---

## Troubleshooting

### Content not formatting correctly?
- Try pasting into **Code Editor** instead of Visual Editor
- Check that markdown isn't being auto-converted

### Featured image not showing?
- Verify file exists: `/app/public/wp-content/uploads/claude-ai-basics-featured.jpg`
- Check file permissions: should be readable
- Re-upload if needed

### Slug conflicts?
- If WordPress adds numbers to slug, edit manually to remove them
- Click "Edit" next to permalink

### SEO plugin not recognizing meta?
- Some plugins require you to save first, then edit SEO
- Refresh after saving post

---

## Next Steps

1. ✅ Create post in WordPress (this guide)
2. ✅ Verify content and formatting
3. ✅ Set featured image
4. ✅ Add SEO metadata
5. ✅ Save as Draft for review
6. ✅ Publish when ready
7. ✅ Create PR to main branch

---

**All content is ready. Follow these steps in WordPress and you're done!** 🚀
