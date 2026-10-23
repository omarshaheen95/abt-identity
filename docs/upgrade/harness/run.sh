#!/usr/bin/env bash
# Runs every snapshot test of this folder against one copy of the project.
#   bash run.sh <php binary> <project dir> <snapshot dir> [Test ...]
# The tests are copied into <project dir>/tests/Feature first (with
# SmokeRoutesTest, which picks the accounts they all log in with), and the
# snapshots of each one are written to <snapshot dir>.
set -u
php="$1"; project="$2"; snap="$3"; shift 3
here="$(cd "$(dirname "$0")" && pwd)"
tests=("$@")
if [ ${#tests[@]} -eq 0 ]; then
    tests=(AuthFlow PasswordReset LogViewerAccess Forms Import ExamFlow Export ParamPages Response)
fi
mkdir -p "$snap"
cp "$here"/*SnapshotTest.php "$here"/SmokeRoutesTest.php "$project/tests/Feature/"
cd "$project" || exit 1
for t in "${tests[@]}"; do
    start=$(date +%s)
    result=$(SNAPSHOT_DIR="$snap" DEBUGBAR_ENABLED=false "$php" -d memory_limit=-1 vendor/bin/phpunit "tests/Feature/${t}SnapshotTest.php" 2>&1 | tail -1)
    echo "$t: $result ($(( $(date +%s) - start ))s)"
done
