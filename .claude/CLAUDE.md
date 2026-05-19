# CLAUDE.md
# Read automatically by Claude Code at the start of every session.
# Do not delete. Do not move from project root.

---

## YOUR TOOLKIT — WHAT IS INSTALLED AND HOW TO USE IT

You are operating in a production-grade project with the following verified tools.
Read this section completely before doing anything.

---

### 1. gstack — Your Virtual Engineering Team
**Installed:** `~/.claude/skills/gstack/` (global, always available)
**Source:** https://github.com/garrytan/gstack

gstack gives you 23 slash commands that simulate specialist roles. Use them in order like a sprint:

**Think → Plan → Build → Review → Test → Ship → Reflect**

| When | Use | Role it plays |
|---|---|---|
| Starting any feature | `/office-hours` | Forces 6 product questions, challenges your framing |
| After /office-hours | `/plan-ceo-review` | Challenges scope, finds the real product |
| Architecture decisions | `/plan-eng-review` | Diagrams, data flow, edge cases, test plan |
| UI/UX features | `/plan-design-review` | Rates design 0–10, edits plan to reach 10 |
| Developer-facing features | `/plan-devex-review` | DX audit: TTHW, friction points, first-run experience |
| All of the above at once | `/autoplan` | Runs CEO → design → eng → DX automatically |
| After writing code | `/review` | Staff engineer review, auto-fixes obvious bugs |
| Stuck on a bug | `/investigate` | Root-cause methodology, Iron Law: no fix without diagnosis |
| Before shipping | `/qa` | Real browser, real clicks, finds and fixes bugs |
| Ready to ship | `/ship` | Syncs main, runs tests, opens PR |
| After merge | `/land-and-deploy` | Merges PR, watches CI, verifies production |
| Any time | `/cso` | OWASP Top 10 + STRIDE threat model |
| Second opinion | `/codex` | Independent OpenAI review of same diff |

**Safety commands:** `/careful` (warns before destructive ops), `/freeze <dir>` (locks edits to one folder), `/guard` (both), `/unfreeze`.

**Memory:** `/learn` manages patterns and preferences across sessions.

**Rule:** Run `/office-hours` before writing any significant new feature. No exceptions.

---

### 2. ui-ux-pro-max-skill — Design Intelligence
**Installed:** `.claude/skills/ui-ux-pro-max/` (this project)
**Source:** https://github.com/nextlevelbuilder/ui-ux-pro-max-skill

**Activates automatically** when you receive any UI/UX request. You do not need to invoke it manually.

What it contains:
- 67 UI styles with implementation guidance
- 161 design reasoning rules
- 57 font pairings with use-case context
- 99 UX guidelines
- 16 framework stacks: React, Next.js, Vue, Nuxt, Svelte, ShadCN, Flutter, SwiftUI, React Native, Jetpack Compose, and more

**Search the design database when you need specific guidance:**
```bash
python3 .claude/skills/ui-ux-pro-max/scripts/search.py "glassmorphism dark" --design-system
python3 .claude/skills/ui-ux-pro-max/scripts/search.py "accessibility focus states" --domain ux
python3 .claude/skills/ui-ux-pro-max/scripts/search.py "monospace code font" --domain google-fonts
```

**Workflow:**
1. User requests any UI work → search the design database for the relevant style/stack
2. Generate a design system (colors, typography, spacing tokens) before writing component code
3. Implement with the correct stack (ask user if unclear, default to React + Tailwind)
4. Run pre-delivery checklist from the skill's guidelines before presenting output

---

### 3. impeccable — Design Language and Anti-Pattern Enforcement
**Installed:** `.claude/skills/impeccable/` (this project)
**Source:** https://github.com/pbakaus/impeccable

Use `/impeccable <command>` for design quality passes:

| Command | When to use |
|---|---|
| `/impeccable audit` | Before any PR that touches UI — a11y, performance, responsive |
| `/impeccable critique` | When design feels unclear or lacks hierarchy |
| `/impeccable polish` | Final quality pass before shipping any UI |
| `/impeccable animate` | When adding or improving animations |
| `/impeccable harden` | Before shipping: error states, empty states, edge cases |
| `/impeccable typeset` | When typography looks off or generic |
| `/impeccable bolder` | When design is too safe / looks like every other app |
| `/impeccable craft` | When starting a new feature from design through code |

**27 anti-patterns you must NEVER produce:**
- Inter, Arial, Roboto, or system-ui as display fonts
- Purple-to-blue gradients on white backgrounds
- Bounce or elastic easing on UI transitions
- Gray text on colored backgrounds
- Cards nested inside cards
- Rounded-square icon tiles above every heading
- Overused side-tab borders as section separators
- Pure black `#000000` or pure gray without tinting

**Run the CLI scanner on any directory before declaring work done:**
```bash
npx impeccable detect src/
```

---

### 4. Magic MCP — On-Demand Component Generation
**Registered:** User-scoped MCP tool, always available
**Source:** 21st.dev

**When to call Magic MCP:**
- User asks for a specific standard UI component (hero, pricing table, nav, card, form, modal, dashboard widget)
- You need a production-quality starting point quickly
- The component is generic enough to be generated (not tightly coupled to your domain logic)

**When NOT to call Magic MCP:**
- Components requiring deep domain knowledge (custom business logic, complex state)
- Components that must exactly match an already-defined design system
- Small utility components under ~30 lines

**After generating:** Adapt to project's existing design tokens, add Motion animations, run through impeccable anti-pattern rules.

---

### 5. Motion — Animation Library
**Installed:** `motion` npm package (in your frontend node_modules)
**Source:** https://github.com/motiondivision/motion

Use Motion for all intentional animations:

```typescript
import { motion, AnimatePresence } from 'motion/react'

// Page-level enter
<motion.div
  initial={{ opacity: 0, y: 16 }}
  animate={{ opacity: 1, y: 0 }}
  exit={{ opacity: 0, y: -16 }}
  transition={{ duration: 0.25, ease: 'easeOut' }}
/>

// Staggered list
const container = {
  hidden: { opacity: 0 },
  show: { opacity: 1, transition: { staggerChildren: 0.08 } }
}

// Layout animations (automatic)
<motion.div layout />

// Conditional render
<AnimatePresence>
  {isOpen && <motion.div .../>}
</AnimatePresence>
```

**Rules:**
- Use `transform` and `opacity` only — never animate `height`, `width`, `top`, `left` directly
- UI feedback animations: under 300ms
- Page transitions: 200–400ms
- Always wrap conditional renders in `<AnimatePresence>`
- Use `layout` prop for automatic layout shift animations
- Respect `prefers-reduced-motion` — check via `useReducedMotion()`

---

### 6. code-review-graph — Token-Efficient Code Context
**Installed:** Python MCP server, registered in `.mcp.json`
**Source:** https://github.com/tirth8205/code-review-graph

This tool builds and maintains an AST graph of the entire codebase. It dramatically reduces tokens by giving you only the context that matters for each task.

**MCP tools available to you:**

```
get_minimal_context(task="<describe what you're doing>")
  → ~100 tokens, returns risk score, affected communities, suggested next tools
  → ALWAYS call this first at the start of any review or analysis task

get_review_context_tool()
  → changed files + blast radius (callers, dependents, tests that could be affected)

query_graph_tool(pattern="tests_for", target="src/api/users.ts")
  → structural graph queries

list_graph_stats_tool()
  → node/edge counts, last update time
```

**Usage rules (from the tool's own CLAUDE.md):**
- **First call always:** `get_minimal_context(task="<description>")` — costs ~100 tokens
- **All subsequent calls:** use `detail_level="minimal"` unless you need more
- Prefer `query_graph_tool` with a specific target over broad `list_*` calls
- The `next_tool_suggestions` field in every response tells you the optimal next step
- **Target: ≤5 tool calls per task, ≤800 total tokens of graph context**

**Slash commands:**
- `/code-review-graph:review-delta` — review only what changed since last commit
- `/code-review-graph:build-graph` — rebuild graph after major refactor

The graph auto-updates on every file edit and git commit. You should not need to trigger it manually in normal operation.

---

## FULL-STACK DEVELOPMENT WORKFLOW

Follow this sequence. Do not skip phases.

### Phase 1: UNDERSTAND
```
1. Read this CLAUDE.md (done)
2. Call get_minimal_context(task="<what user is asking for>") → understand codebase state
3. If starting a new feature: run /office-hours first
```

### Phase 2: PLAN
```
4. Run /autoplan (or /plan-ceo-review → /plan-eng-review individually)
5. For UI features: also run /plan-design-review
6. Search ui-ux-pro-max for relevant design patterns
7. Output a written plan with: file list, API contracts, component tree, test plan
8. Confirm plan with user before writing code
```

### Phase 3: BUILD — Backend First
```
9.  Database schema and models
10. API routes, controllers, validation
11. Error handling (never expose stack traces)
12. Unit tests for all business logic
```

### Phase 4: BUILD — Frontend
```
13. Design tokens from ui-ux-pro-max (colors, typography, spacing)
14. Layout and navigation structure
15. Components (use Magic MCP for standard ones, build custom for domain-specific)
16. Motion animations for transitions and interactions
17. Data fetching and state management
18. Accessibility pass (keyboard nav, ARIA, focus states)
```

### Phase 5: REVIEW
```
19. Run /review (staff engineer review)
20. Run /code-review-graph:review-delta (token-efficient, changed files only)
21. Run /impeccable audit on all UI changes
22. Run npx impeccable detect src/ for anti-pattern scan
23. Run /cso for any feature touching auth, payments, or user data
```

### Phase 6: TEST & SHIP
```
24. Run /qa on the running app (real browser test)
25. Run /ship (syncs, tests, opens PR)
26. Run /document-release (updates README, ARCHITECTURE, CHANGELOG)
27. Run /land-and-deploy after PR approval
28. Run /canary for post-deploy monitoring
```

---

## NON-NEGOTIABLE QUALITY RULES

### Frontend Performance
- No component re-renders unnecessarily — memoize with `React.memo`, `useMemo`, `useCallback`
- Images: always specify `width` and `height` (or use `next/image`)
- Lazy-load all routes and heavy components
- No new dependency over 50KB without written justification
- Only animate `transform` and `opacity`
- Core Web Vitals targets: LCP < 2.5s, CLS < 0.1, INP < 200ms

### Backend Performance
- Never `SELECT *` — always specify columns
- Never allow N+1 queries — use joins or batch loading
- All list endpoints must be paginated
- API p95 response time target: under 200ms
- Never expose stack traces or internal errors in API responses

### Security (Non-Negotiable)
- No secrets or API keys in code — use environment variables only
- All user input validated and sanitized before database operations
- Auth required on all non-public routes
- CORS: never wildcard (`*`) in production
- Parameterized queries only — no string interpolation in SQL
- Sanitize all user-generated content before rendering

### Design (Non-Negotiable)
- No fonts from the banned list: Inter, Arial, Roboto, system-ui as display fonts
- All colors defined as CSS custom properties — no magic color values in components
- Every interactive element has a visible focus state
- Loading state for every async operation
- Error state for every form and data fetch
- Empty state for every list or data display

---

## ESCALATE BEFORE DOING

Stop and get explicit user approval before:
- Deleting any file (not just clearing its contents)
- Running destructive database migrations
- Modifying authentication or session logic
- Adding a dependency over 100KB
- Renaming environment variables (breaks existing deployments)
- Changing public API contracts (breaking change)
- Refactoring more than 3 files simultaneously without a written plan first

---

## CODE CONVENTIONS

```
File naming:
  Components:   PascalCase   (UserCard.tsx, AuthModal.tsx)
  Utilities:    camelCase    (formatDate.ts, parseQuery.ts)
  Constants:    UPPER_SNAKE  (MAX_RETRIES, API_BASE_URL)
  API routes:   kebab-case   (/api/user-profile, /api/auth/refresh)

Commit messages (Conventional Commits):
  feat:      new feature
  fix:       bug fix
  perf:      performance improvement
  refactor:  restructure, no behavior change
  test:      test additions or changes
  docs:      documentation only
  chore:     tooling, config, dependency updates
  style:     formatting only

Branch naming:
  feature/short-description
  fix/bug-description
  refactor/area-name
```

---

## END-OF-TASK CHECKLIST

Before declaring any task complete:

```
□ Code compiles without errors or warnings
□ No console.log, debugger, or TODO left in shipped code
□ Tests written for new business logic
□ .env.example updated if new environment variables added
□ /impeccable audit run on any UI changes
□ npx impeccable detect src/ run (zero anti-pattern violations)
□ /review run (or /code-review-graph:review-delta for small changes)
□ User told clearly: what was done, what was not done, what comes next
```

---

## TOOL QUICK REFERENCE

```
gstack commands:    /office-hours  /autoplan  /review  /qa  /ship  /cso  /investigate
impeccable:         /impeccable audit  /impeccable polish  /impeccable animate
code-review-graph:  /code-review-graph:review-delta  /code-review-graph:build-graph
MCP tools:          get_minimal_context()  get_review_context_tool()  query_graph_tool()
design search:      python3 .claude/skills/ui-ux-pro-max/scripts/search.py "<query>"
anti-pattern scan:  npx impeccable detect src/
```

---

*CLAUDE.md — Single source of truth for Claude Code in this project.*
*Manually maintained. Update when architecture or conventions change.*
*code-review-graph auto-injects its own section below when installed.*