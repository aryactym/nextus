#!/usr/bin/env sh
set -eu

BASE="https://raw.githubusercontent.com/drknzz/GitHub-Achievements/main"
DEST="public/images/badges"

fetch_one() {
  src="$1"
  dst="$2"
  if curl -fL --create-dirs -o "$dst" "$BASE/$src"; then
    echo "OK $dst"
    return 0
  fi
  echo "curl gagal untuk $src — memakai cadangan git clone" >&2
  return 1
}

TMPDIR_CLONE=""

clone_fallback() {
  if [ -z "$TMPDIR_CLONE" ]; then
    TMPDIR_CLONE=$(mktemp -d)
    git clone --depth 1 https://github.com/drknzz/GitHub-Achievements.git "$TMPDIR_CLONE/repo"
  fi
}

trap 'rm -rf "${TMPDIR_CLONE:-}"' EXIT

download() {
  src="$1"
  dst="$2"
  if fetch_one "$src" "$dst"; then
    return 0
  fi
  clone_fallback
  mkdir -p "$(dirname "$dst")"
  cp "$TMPDIR_CLONE/repo/$src" "$dst"
  echo "OK (fallback) $dst"
}

download "Media/Badges/Pull-Shark/PNG/PullShark.png" "$DEST/tuntas/default.png"
download "Media/Badges/Pull-Shark/PNG/PullShark_Bronze.png" "$DEST/tuntas/bronze.png"
download "Media/Badges/Pull-Shark/PNG/PullShark_Silver.png" "$DEST/tuntas/silver.png"
download "Media/Badges/Pull-Shark/PNG/PullShark_Gold.png" "$DEST/tuntas/gold.png"
download "Media/Badges/YOLO/PNG/YOLO_Badge.png" "$DEST/sekali-jalan/default.png"
download "Media/Badges/Open-Sourcerer/PNG/OpenSourcerer.png" "$DEST/alumni/default.png"

mkdir -p "$DEST"
cat > "$DEST/README.txt" <<'EOF'
Sumber badge: https://github.com/drknzz/GitHub-Achievements (folder Media/Badges).
Pembuat repo: Kamil Jankowski. Lisensi repo: MIT.
Catatan: artwork asli milik GitHub, Inc. Perlu dicek lisensinya sebelum rilis publik.
File diambil dari branch main lewat raw.githubusercontent.com (lihat scripts/fetch-badges.sh).
EOF

for f in tuntas/default.png tuntas/bronze.png tuntas/silver.png tuntas/gold.png sekali-jalan/default.png alumni/default.png; do
  if [ ! -s "$DEST/$f" ]; then
    echo "MISSING $DEST/$f" >&2
    exit 1
  fi
  ls -l "$DEST/$f"
done

echo "Selesai: 6 badge terunduh di $DEST"
