import os
import re

def check_blade_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Remove script blocks and comments to avoid false positives
    content = re.sub(r'<script.*?>.*?</script>', '', content, flags=re.DOTALL)
    content = re.sub(r'{{--.*?--}}', '', content, flags=re.DOTALL)
    
    has_issue = False
    tags = ['if', 'forelse', 'auth', 'guest', 'can', 'section', 'verbatim']
    for tag in tags:
        start = len(re.findall(f'@{tag}\\b', content))
        end = len(re.findall(f'@end{tag}\\b', content))
        if start != end:
            print(f"ISSUE in {filepath}: @{tag} ({start}) != @end{tag} ({end})")
            has_issue = True
    
    return has_issue

path = r'e:\project\pos\web\resources\views'
for root, dirs, files in os.walk(path):
    for file in files:
        if file.endswith('.blade.php'):
            check_blade_file(os.path.join(root, file))
