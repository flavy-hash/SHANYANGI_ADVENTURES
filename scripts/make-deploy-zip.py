# Builds a cumulative deploy zip for the live server.
# Usage: python -I scripts/make-deploy-zip.py <zip-name.zip>
#
# Contents: every file changed or added since BASE (the commit before the first update zip),
# committed or not, plus public/build/ and the protected media that Git doesn't track
# (home video stream, site photos). Paths use forward slashes, so Linux unzip is happy.
import glob, json, os, subprocess, sys, zipfile

root = os.path.dirname(os.path.dirname(os.path.abspath(__file__))).replace(os.sep, '/')
out = os.path.join(os.path.expanduser('~'), 'Downloads', sys.argv[1])
BASE = 'eddceaa'  # 10 Oct 2026: the site as first uploaded in full
skip = ('.env', 'vendor/', 'node_modules/', '.git/', 'storage/logs/', 'storage/framework/', 'bootstrap/cache/',
        'package.json', 'package-lock.json', 'database/database.sqlite', 'scripts/')
media = [
    'storage/app/private/media/streams/hero/*',   # re-encoded home video
    'storage/app/private/media/images/hero-poster.jpg',
    'storage/app/private/media/images/site/*',    # banner and card photos
]


def git(*args):
    return subprocess.run(['git', '-c', 'core.quotepath=off', *args], cwd=root, capture_output=True,
                          text=True, encoding='utf-8', check=True).stdout.splitlines()


def tree(pattern):
    return [os.path.relpath(f, root).replace(os.sep, '/') for f in glob.glob(root + '/' + pattern, recursive=True) if os.path.isfile(f)]


files = git('diff', '--name-only', '--diff-filter=ACMR', BASE) + git('ls-files', '--others', '--exclude-standard')
deleted = git('diff', '--name-only', '--diff-filter=D', BASE)
files += tree('public/build/**/*')
for pattern in media:
    files += tree(pattern)
files = [f for f in dict.fromkeys(files) if not f.startswith(skip) and not f.endswith(('.zip', '.tar.gz'))
         and os.path.isfile(os.path.join(root, f))]

with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as z:
    for f in files:
        z.write(os.path.join(root, f), f)

with zipfile.ZipFile(out) as z:
    names = z.namelist()
    assert z.testzip() is None, 'corrupt zip'
    assert not any(chr(92) in n for n in names), 'backslash in a zip path'
    assert len(names) == len(set(names)), 'duplicate entries'
    manifest = json.loads(z.read('public/build/manifest.json'))
    lost = [v['file'] for v in manifest.values() if 'public/build/' + v['file'] not in names]
    assert not lost, 'assets in manifest but not in zip: %s' % lost

print('%d files -> %s (%d KB), verified' % (len(files), out, os.path.getsize(out) // 1024))
print('migrations:', len([n for n in names if n.startswith('database/migrations/')]))
for d in deleted:
    print('DELETED (add rm -f to the commands):', d)
