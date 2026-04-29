#!/bin/bash
# Updates specs/v1/context.md with current project state before context is compacted.
set -euo pipefail

WORKTREE="/Users/nakul/Herd/archgate/.claude/worktrees/nifty-dirac-e9320f"
CONTEXT="$WORKTREE/specs/v1/context.md"

cd "$WORKTREE"

DATE=$(date '+%Y-%m-%d %H:%M')

# Gather current project state
BRANCH=$(git branch --show-current 2>/dev/null || echo "unknown")

MODELS=$(ls app/Models/*.php 2>/dev/null | xargs -I{} basename {} .php | sort | tr '\n' ', ' | sed 's/, $//')

MIGRATIONS=$(ls database/migrations/2026_*.php 2>/dev/null | xargs -I{} basename {} .php | sort | while read m; do echo "  - $m"; done)

CONTROLLERS=$(ls app/Http/Controllers/*.php 2>/dev/null | xargs -I{} basename {} .php | grep -v '^Controller$' | sort | tr '\n' ', ' | sed 's/, $//')

TESTS=$(ls tests/Feature/*.php 2>/dev/null | xargs -I{} basename {} .php | sort | tr '\n' ', ' | sed 's/, $//')

GIT_STATUS=$(git status --short 2>/dev/null | head -20 || echo "(clean)")

# Build the snapshot block to inject
SNAPSHOT=$(cat <<SNAP

---

## Auto-Snapshot — $DATE (branch: $BRANCH)

**Models:** $MODELS

**Controllers:** ${CONTROLLERS:-none yet}

**Feature Tests:** ${TESTS:-none yet}

**Migrations (2026):**
$MIGRATIONS

**Git status:**
\`\`\`
$GIT_STATUS
\`\`\`
SNAP
)

# Remove any previous auto-snapshot block (between the marker and end of file)
# Then append the new one
MARKER="## Auto-Snapshot"
HEAD=$(sed "/$MARKER/,\$d" "$CONTEXT")

printf '%s\n%s\n' "$HEAD" "$SNAPSHOT" > "$CONTEXT"

echo "Context file updated: $DATE"
