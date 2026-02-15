# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

---

## Repository Purpose

ClaudeFour is a **development environment consolidating best practices and patterns** from two successful projects:

- **claudeOne:** GitHub-based project management, issue automation, local CI/CD (zero API costs)
- **ClaudeTwo:** Universal developer toolkit with plugin architecture, multi-agent orchestration, framework detection

**Goal:** Build on proven workflows and patterns to create flexible, cost-effective automation and tooling.

**Working Directory:** `/Users/kenshinzato/repos/ClaudeFour`
**Memory System:** `/Users/kenshinzato/.claude/projects/-Users-kenshinzato-repos-ClaudeFour/memory/`

---

## Quick Start

### Reference Your Memory System First
All cross-session context is stored in auto-memory:
- **`MEMORY.md`** - Index of all topics, quick links, consolidated preferences
- **`patterns.md`** - Reusable code patterns (process management, testing, terminal UI)
- **`workflow-rules.md`** - CRITICAL: Issue-first development, testing standards
- **`conventions.md`** - Code style, naming, git conventions, testing standards

**Load pattern:** Always check MEMORY.md before starting work to understand established standards.

### Development Workflow

1. **Create Issue First** - Every piece of work starts with a GitHub issue
   - Title describes the work
   - Acceptance criteria in body
   - Branch naming: `feature/name`, `fix/name`, `docs/name`, or `req-NNN-name`

2. **Write Tests Alongside Code** - Tests verify acceptance criteria
   - Run: `npm test` (or framework equivalent)
   - Coverage: 80% lines, 70% functions/branches
   - Test locally before committing

3. **Commit & Reference Issue**
   ```bash
   git commit -m "Description

   Longer explanation.

   Closes #15

   Co-Authored-By: Claude Haiku 4.5 <noreply@anthropic.com>"
   ```

4. **Create PR & Merge** - PR auto-links issue, merge auto-closes
   - PR title describes change
   - Body must have "Closes #N"
   - Push after merge

**Key Rule:** NEVER code without an issue. Ask first: "Should I create an issue for this?"

---

## Core Patterns & Techniques

### Process Management (Shell Automation)
Pattern: Intelligent process detection without interfering with user processes
- Use `pgrep -f "pattern"` for command-line matching (not lsof)
- Track whether we started the process so we don't kill it
- Safe cleanup with `|| true` for errors
- See `patterns.md` → "Intelligent Process Detection + Safe Lifecycle Management"

### Terminal UI & Dashboards
When blessed/ink fail: Use text-mode dashboard pattern
- Simple `console.clear()` + `setTimeout()` for updates
- Simpler to implement and test than TUI libraries
- Works across all terminal environments
- See `patterns.md` → "Text-Mode Dashboard Pattern"

### Testing Long-Running Processes
Pattern: Verify Node.js daemons stay alive and handle signals
- Spawn with `stdio: ['pipe', 'pipe', 'pipe']`
- Listen to stderr for startup messages (stdout buffered when TUI active)
- Wait 3-4 seconds for async initialization
- Set `NODE_ENV=test` to disable alternate screen buffer
- See `patterns.md` → "Integration Testing Long-Running Node.js Processes"

### Multi-Agent Orchestration
Use autonomous agents for:
- 3+ independent sub-tasks (parallelization benefit)
- Complex tasks that clutter main conversation
- Specialized agent types: Explore (search), Plan (architecture), Bash (operations)

**Agent Types Available:**
- `Explore` - Fast pattern matching, codebase navigation
- `Bash` - Terminal operations, git, CI/CD
- `Plan` - Architecture design, implementation strategy
- `general-purpose` - Research, code search, multi-step reasoning

See `patterns.md` → "When to Use Agents vs Direct Tool Calls"

### Prompt Caching (For Claude API Projects)
If building with Claude API:
- Cache framework detection + skills (~11K tokens)
- 90% discount on cached reads (breakeven after 2 requests)
- 50-90% annual cost savings for contractors
- Implementation: Add `cache_control: {"type": "ephemeral"}` to system messages
- See ClaudeTwo memory: `PROMPT-CACHING-RESEARCH.md`

---

## Code Standards & Conventions

### Git & Commits
- **Branch naming:** kebab-case with type prefix (`feature/auth`, `fix/login-bug`, `docs/api`)
- **Commit messages:** Imperative present tense, explain WHY not WHAT
- **Always include:** Issue reference + co-author line
- **Push frequency:** After each commit (backup safety)

### Naming Conventions
- **JavaScript:** camelCase for variables/functions
- **Python:** snake_case for variables/functions
- **All languages:** kebab-case for files/directories, UPPER_SNAKE_CASE for constants
- **Types/Classes:** PascalCase for TypeScript/C#

### Code Quality Standards
- Validate at boundaries (user input, external APIs)
- Don't over-handle errors - trust framework guarantees for internal code
- Error messages should help debugging (include context)
- Add comments only where logic isn't self-evident
- Prefer simple, straightforward solutions (don't over-engineer)

### Testing Requirements
- Coverage targets: 80% lines, 70% functions, 70% branches
- Test names describe what's being tested + expected outcome
- Tests isolated (no dependencies on other tests)
- Run locally before committing
- One logical assertion per test (or related assertions)

See `conventions.md` for complete style guide.

---

## Framework-Specific Development

### Node.js / JavaScript
```bash
npm install              # Install dependencies
npm test                 # Run tests
npm run lint            # Check code style
npm run test:coverage   # Check coverage
npm start               # Start application
```

### Python
```bash
python -m pytest tests/ -v          # Run tests
flake8 .                            # Lint
pytest --cov                        # Coverage
python -m mypy .                    # Type checking
```

### TypeScript
```bash
npm run build           # Compile
npm test               # Run tests
npm run lint           # Type check + lint
```

---

## Architecture Overview

### Current Structure
ClaudeFour is scaffolded with:
- `MEMORY.md` system for cross-session context
- Consolidated patterns from two successful projects
- Established workflow rules and conventions
- Plugin architecture patterns (from ClaudeTwo)
- Automation patterns (from claudeOne)

### Design Philosophy
1. **Issue-driven development** - All work tracked in GitHub issues
2. **Local-first automation** - Use Claude Code CLI (free), not Anthropic API
3. **Test-driven verification** - Tests prove code meets acceptance criteria
4. **Pattern-based reuse** - Established solutions for common problems
5. **Zero hallucination** - Reference specific docs, verify against code

### Project Integration Points
**From claudeOne:**
- GitHub Projects automation (labels, statuses, board updates)
- Scheduled automation patterns (macOS launchd, GitHub Actions)
- Zero-cost local automation via Claude Code CLI

**From ClaudeTwo:**
- Plugin development (40+ commands, three-level discovery)
- Multi-phase orchestration workflows
- Framework detection and auto-scaffolding
- Prompt caching for cost optimization

---

## When Starting New Work

### Setup
1. Check your memory system (MEMORY.md) for context
2. Review relevant patterns in patterns.md
3. Ensure you understand workflow-rules.md

### For Any Code Task
1. Create GitHub issue with acceptance criteria
2. Create feature branch from issue
3. Write tests that verify acceptance criteria
4. Implement code to pass tests
5. Commit with issue reference
6. Create PR with "Closes #N"
7. After merge, update MEMORY.md with learnings

### Before Considering Task Complete
- [ ] Issue created and linked
- [ ] Tests passing locally
- [ ] Coverage meets thresholds
- [ ] Code follows conventions.md
- [ ] Commit references issue
- [ ] PR merged to main
- [ ] Memory updated with learnings (if reusable pattern discovered)

---

## Key References

### Memory System (Local to This Repository)
These persist across sessions and are auto-loaded:
- **MEMORY.md** - Master index, quick navigation
- **patterns.md** - Reusable patterns with implementation
- **workflow-rules.md** - Development process standards
- **conventions.md** - Code style and naming conventions

### From claudeOne Project
- PM workflow automation templates
- GitHub Projects setup (14 labels, 3 milestones)
- Scheduled automation patterns (macOS launchd)
- Testing patterns for Node.js processes

### From ClaudeTwo Project
- Universal toolkit architecture (8 commands, 8 skills)
- Plugin development guide (three-level discovery)
- Prompt caching research (50-90% cost savings)
- Multi-agent orchestration patterns

---

## Developer Preferences

- **Always continue** momentum between tasks (don't pause)
- **Commit regularly** with clear messages
- **Auto-update task status** as work progresses
- **Push after each commit** (backup safety)
- **Include co-author** in all Claude-assisted commits
- **Reference issues** in all commits
- **Test before committing** - all tests must pass locally
- **Update MEMORY.md** after significant learnings

---

## Status

✅ **Memory system initialized:** 2026-02-12
✅ **Workflow rules established:** Proven on claudeOne (100+ issues)
✅ **Testing patterns proven:** 75+ tests in CI/CD
✅ **Conventions documented:** Across JavaScript, Python, TypeScript
✅ **Ready for development:** All guidance captured and referenced

---

**This file stays concise—detailed guidance is in your MEMORY.md and supporting files.**
**Start new work by reading MEMORY.md, then reference patterns.md and conventions.md as needed.**
