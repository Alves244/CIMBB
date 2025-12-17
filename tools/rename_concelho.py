import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SKIP_DIRS = {'.git', 'vendor', 'node_modules', 'storage', 'public/storage', 'public/assets', 'bootstrap/cache'}
TEXT_SUFFIXES = {'.php', '.blade.php', '.js', '.jsx', '.ts', '.tsx', '.json', '.md', '.css', '.scss', '.sass', '.less', '.yml', '.yaml', '.xml', '.html', '.htm', '.txt', '.stub', '.env', '.ini', '.conf', '.lock', '.mix', '.vue', '.php.stub'}

processed = 0
changed = 0

for path in ROOT.rglob('*'):
    if not path.is_file():
        continue
    rel = path.relative_to(ROOT)
    parts = rel.parts
    if any(
        part in SKIP_DIRS or '/'.join(parts[:idx + 1]) in SKIP_DIRS
        for idx, part in enumerate(parts)
    ):
        continue
    is_blade = path.name.endswith('.blade.php')
    suffix = '.blade.php' if is_blade else path.suffix
    if not (is_blade or suffix in TEXT_SUFFIXES or path.suffix in TEXT_SUFFIXES):
        continue
    try:
        text = path.read_text(encoding='utf-8')
    except UnicodeDecodeError:
        continue
    new_text = text.replace('CONSELHO', 'CONCELHO').replace('Conselho', 'Concelho').replace('conselho', 'concelho')
    if new_text != text:
        path.write_text(new_text, encoding='utf-8')
        changed += 1
    processed += 1

print(f"Processed {processed} files, changed {changed} files.")
